package main

import (
	"context"
	"crypto/subtle"
	"database/sql"
	"encoding/json"
	"fmt"
	_ "github.com/go-sql-driver/mysql"
	"github.com/gorilla/websocket"
	"log"
	"net/http"
	"os"
	"strings"
	"sync"
	"time"
)

// Database-first payment worker: PHP keeps the public API contract, while Go
// owns provider event intake, ordering/idempotency, expiry, retries and WS fanout.
type service struct {
	db      *sql.DB
	clients map[string]map[*websocket.Conn]struct{}
	mu      sync.Mutex
}

var upgrader = websocket.Upgrader{CheckOrigin: func(r *http.Request) bool { return true }}

func main() {
	db, err := sql.Open("mysql", dsn())
	if err != nil {
		log.Fatal(err)
	}
	db.SetMaxOpenConns(20)
	db.SetMaxIdleConns(5)
	db.SetConnMaxLifetime(5 * time.Minute)
	s := &service{db: db, clients: map[string]map[*websocket.Conn]struct{}{}}
	mux := http.NewServeMux()
	mux.HandleFunc("/healthz", s.health)
	mux.HandleFunc("/readyz", s.ready)
	mux.HandleFunc("/webhooks/breet", s.breetWebhook)
	mux.HandleFunc("/webhooks/onepipe", s.onePipeWebhook)
	mux.HandleFunc("/ws/checkout/", s.checkoutWS)
	mux.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		jsonOut(w, 404, map[string]any{"success": false, "error": "route_not_found"})
	})
	go s.worker()
	addr := os.Getenv("FLOW_GO_ADDR")
	if addr == "" {
		addr = os.Getenv("GO_BACKEND_ADDR")
	}
	if addr == "" {
		addr = ":8081"
	}
	log.Printf("flowwithlit payment service listening on %s", addr)
	log.Fatal((&http.Server{Addr: addr, Handler: requestID(mux), ReadHeaderTimeout: 5 * time.Second, ReadTimeout: 20 * time.Second, WriteTimeout: 20 * time.Second, IdleTimeout: 60 * time.Second}).ListenAndServe())
}
func dsn() string {
	return fmt.Sprintf("%s:%s@tcp(%s:3306)/%s?parseTime=true&charset=utf8mb4", env("FLOW_DB_USER", "root"), os.Getenv("FLOW_DB_PASSWORD"), env("FLOW_DB_HOST", "127.0.0.1"), env("FLOW_DB_NAME", "flowwithlit_db"))
}
func env(k, f string) string {
	if v := os.Getenv(k); v != "" {
		return v
	}
	return f
}
func (s *service) health(w http.ResponseWriter, r *http.Request) {
	jsonOut(w, 200, map[string]string{"status": "ok"})
}
func (s *service) ready(w http.ResponseWriter, r *http.Request) {
	if e := s.db.PingContext(r.Context()); e != nil {
		jsonOut(w, 503, map[string]string{"status": "not_ready"})
		return
	}
	jsonOut(w, 200, map[string]string{"status": "ready"})
}
func (s *service) verified(r *http.Request) bool {
	sec := os.Getenv("FLOW_GO_SERVICE_SECRET")
	if sec == "" {
		return false
	}
	got := r.Header.Get("X-Flow-Service-Secret")
	return len(got) == len(sec) && subtle.ConstantTimeCompare([]byte(got), []byte(sec)) == 1
}
func (s *service) breetWebhook(w http.ResponseWriter, r *http.Request)   { s.webhook(w, r, "breet") }
func (s *service) onePipeWebhook(w http.ResponseWriter, r *http.Request) { s.webhook(w, r, "onepipe") }
func (s *service) webhook(w http.ResponseWriter, r *http.Request, provider string) {
	if r.Method != "POST" {
		jsonOut(w, 405, map[string]any{"success": false, "error": "method_not_allowed"})
		return
	}
	if provider == "onepipe" {
		jsonOut(w, 503, map[string]any{"success": false, "error": "provider_verification_not_configured"})
		return
	}
	if provider == "breet" {
		sec := os.Getenv("BREET_WEBHOOK_SECRET")
		got := r.Header.Get("x-webhook-secret")
		if sec == "" || len(got) != len(sec) || subtle.ConstantTimeCompare([]byte(got), []byte(sec)) != 1 {
			jsonOut(w, 401, map[string]any{"success": false, "error": "invalid_webhook_secret"})
			return
		}
		if !allowedBreetIP(r.RemoteAddr) {
			jsonOut(w, 403, map[string]any{"success": false, "error": "untrusted_webhook_source"})
			return
		}
	}
	var body map[string]any
	if e := json.NewDecoder(http.MaxBytesReader(w, r.Body, 2<<20)).Decode(&body); e != nil {
		jsonOut(w, 400, map[string]any{"success": false, "error": "invalid_json"})
		return
	}
	id := scalar(body, "eventId")
	if id == "" {
		id = scalar(body, "event_id")
	}
	if id == "" {
		id = scalar(body, "id")
	}
	if id == "" {
		id = hashJSON(body)
	}
	typ := scalar(body, "event")
	if typ == "" {
		typ = scalar(body, "type")
	}
	subject := scalar(body, "reference")
	if subject == "" {
		subject = scalar(body, "id")
	}
	et := parseEventTime(scalar(body, "eventTime"))
	raw := jsonBytes(body)
	res, e := s.db.Exec(`INSERT IGNORE INTO payment_provider_events(provider,event_id,event_type,subject_reference,event_time,payload) VALUES(?,?,?,?,?,?)`, provider, id, typ, subject, et, raw)
	if e != nil {
		log.Printf("webhook inbox: %v", e)
		jsonOut(w, 500, map[string]any{"success": false, "error": "inbox_unavailable"})
		return
	}
	if n, _ := res.RowsAffected(); n == 0 {
		jsonOut(w, 200, map[string]any{"success": true, "duplicate": true})
		return
	}
	// Acknowledgement means durable receipt only. Settlement belongs to the PHP
	// ledger worker contract being implemented; intake must never guess a payment.
	jsonOut(w, 202, map[string]any{"success": true, "received": true, "processing": "queued"})
}

func terminal(s string) bool {
	return s == "succeeded" || s == "failed" || s == "refunded" || s == "expired"
}
func (s *service) worker() {
	t := time.NewTicker(30 * time.Second)
	defer t.Stop()
	for range t.C {
		s.expirePayments()
		s.retryOutbox()
		s.processJobs()
	}
}
func (s *service) processJobs() {
	base := os.Getenv("FLOW_PHP_INTERNAL_URL")
	secret := os.Getenv("FLOW_GO_SERVICE_SECRET")
	if base == "" || secret == "" {
		return
	}
	req, err := http.NewRequest(http.MethodPost, strings.TrimRight(base, "/")+"/api/internal/jobs.php", nil)
	if err != nil {
		return
	}
	req.Header.Set("X-Flow-Service-Secret", secret)
	req.Header.Set("X-Flow-Worker", "go-worker")
	ctx, cancel := context.WithTimeout(context.Background(), 15*time.Second)
	defer cancel()
	req = req.WithContext(ctx)
	res, err := http.DefaultClient.Do(req)
	if err != nil {
		return
	}
	defer res.Body.Close()
	for i := 0; i < 10; i++ {
		ev, e := http.NewRequest(http.MethodPost, strings.TrimRight(base, "/")+"/api/internal/provider-events.php", nil)
		if e != nil {
			return
		}
		ev.Header.Set("X-Flow-Service-Secret", secret)
		ev.Header.Set("X-Flow-Worker", "go-worker")
		ev = ev.WithContext(ctx)
		r, e := http.DefaultClient.Do(ev)
		if e != nil {
			return
		}
		r.Body.Close()
		if r.StatusCode != http.StatusOK {
			break
		}
	}
}
func (s *service) expirePayments() {
	if _, e := s.db.Exec(`UPDATE payments p JOIN transactions t ON t.id=p.transaction_id LEFT JOIN checkout_sessions cs ON cs.id=p.checkout_session_id SET p.status='expired',t.status='cancelled' WHERE p.status IN ('pending','processing') AND ((cs.expires_at IS NOT NULL AND cs.expires_at<UTC_TIMESTAMP()) OR p.created_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 24 HOUR))`); e != nil {
		log.Printf("expiry worker: %v", e)
	}
}
func (s *service) retryOutbox() {
	// Delivery requires the durable leased worker, tracked in payment-gateway-plan.md.
	// Never increment attempts unless a delivery was actually attempted.
}

func (s *service) checkoutWS(w http.ResponseWriter, r *http.Request) {
	token := strings.Trim(strings.TrimPrefix(r.URL.Path, "/ws/checkout/"), "/")
	if token == "" {
		http.Error(w, "missing token", 400)
		return
	}
	c, e := upgrader.Upgrade(w, r, nil)
	if e != nil {
		return
	}
	s.mu.Lock()
	if s.clients[token] == nil {
		s.clients[token] = map[*websocket.Conn]struct{}{}
	}
	s.clients[token][c] = struct{}{}
	s.mu.Unlock()
	defer func() { s.mu.Lock(); delete(s.clients[token], c); s.mu.Unlock(); c.Close() }()
	last := ""
	for {
		st := s.checkoutStatus(token)
		if st != last {
			c.WriteJSON(map[string]string{"status": st})
			last = st
		}
		c.SetReadDeadline(time.Now().Add(45 * time.Second))
		if _, _, e = c.ReadMessage(); e != nil {
			return
		}
	}
}
func (s *service) checkoutStatus(token string) string {
	var st string
	s.db.QueryRow(`SELECT COALESCE((SELECT p.status FROM payments p JOIN checkout_sessions cs ON cs.id=p.checkout_session_id WHERE cs.public_token=? ORDER BY p.id DESC LIMIT 1),'pending')`, token).Scan(&st)
	return st
}
func (s *service) broadcastForPayment(pid int) {
	var token string
	s.db.QueryRow(`SELECT cs.public_token FROM payments p JOIN checkout_sessions cs ON cs.id=p.checkout_session_id WHERE p.id=?`, pid).Scan(&token)
	if token == "" {
		return
	}
	s.mu.Lock()
	defer s.mu.Unlock()
	for c := range s.clients[token] {
		_ = c.WriteJSON(map[string]string{"status": s.checkoutStatus(token)})
	}
}
func scalar(m map[string]any, k string) string {
	if v, ok := m[k]; ok {
		if x, ok := v.(string); ok {
			return x
		}
		return fmt.Sprint(v)
	}
	return ""
}
func parseEventTime(v string) time.Time {
	if t, e := time.Parse(time.RFC3339Nano, v); e == nil {
		return t
	}
	return time.Now().UTC()
}
func jsonBytes(v any) []byte { b, _ := json.Marshal(v); return b }
func hashJSON(v any) string {
	b := jsonBytes(v)
	var h uint64
	for _, c := range b {
		h = h*131 + uint64(c)
	}
	return fmt.Sprintf("hash-%x", h)
}

func allowedBreetIP(remote string) bool {
	configured := os.Getenv("BREET_ALLOWED_IPS")
	if configured == "" {
		configured = "46.101.201.155,46.101.225.109,46.101.225.97,46.101.225.251,159.89.20.62"
	}
	host := remote
	if i := strings.LastIndex(host, ":"); i > -1 {
		host = strings.Trim(host[:i], "[]")
	}
	for _, item := range strings.Split(configured, ",") {
		if strings.TrimSpace(item) == host {
			return true
		}
	}
	return false
}
func jsonOut(w http.ResponseWriter, c int, v any) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(c)
	json.NewEncoder(w).Encode(v)
}
func requestID(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		id := r.Header.Get("X-Flow-Request-Id")
		if id == "" {
			id = fmt.Sprintf("%d", time.Now().UnixNano())
		}
		w.Header().Set("X-Flow-Request-Id", id)
		next.ServeHTTP(w, r)
	})
}
