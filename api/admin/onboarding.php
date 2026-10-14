<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
require_once __DIR__.'/../../services/PrivateStorage.php';

$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['reviewer', 'super_admin'], 'review identity submissions');

$statuses = ['submitted', 'under_review', 'approved', 'rejected'];
$baseSelect = "SELECT s.id,s.user_id,s.business_id,s.status,s.review_note,s.submitted_at,s.updated_at,u.uuid user_uuid,u.full_name,u.email,u.phone,u.account_type,u.kyc_tier,
    b.uuid business_uuid,b.legal_name,b.trading_name,b.business_type,b.industry,b.website_url,b.verification_status,
    (SELECT COUNT(*) FROM onboarding_files f WHERE f.submission_id=s.id) document_count
    FROM onboarding_submissions s
    INNER JOIN users u ON u.id=s.user_id
    LEFT JOIN businesses b ON b.id=s.business_id";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $submissionId = (int) ($input['id'] ?? 0);
    if ($submissionId > 0) {
        $statement = $pdo->prepare($baseSelect.' WHERE s.id=? LIMIT 1');
        $statement->execute([$submissionId]);
        $submission = $statement->fetch();
        if (!$submission) jsonResponse(['success'=>false, 'error'=>'Identity submission not found.'], 404);

        $payload = [];
        try {
            $encrypted = $pdo->prepare('SELECT payload_encrypted FROM onboarding_submissions WHERE id=?');
            $encrypted->execute([$submissionId]);
            $value = (string) $encrypted->fetchColumn();
            $decoded = base64_decode($value, true);
            if ($decoded === false) throw new RuntimeException('Invalid onboarding data.');
            $payload = json_decode(PrivateStorage::decrypt($decoded, 'onboarding:'.(int) $submission['user_id']), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $error) {
            error_log('Admin onboarding payload decrypt failed: '.$error->getMessage());
        }
        $files = $pdo->prepare("SELECT f.id,f.field_name,f.mime_type,f.byte_size,COALESCE(d.document_type,'other') document_type,COALESCE(d.status,'uploaded') status
            FROM onboarding_files f LEFT JOIN business_documents d ON d.file_key=f.storage_key WHERE f.submission_id=? ORDER BY f.created_at ASC");
        $files->execute([$submissionId]);

        jsonResponse([
            'success'=>true,
            'submission'=>$submission,
            'identity'=>[
                'owner_name'=>$payload['ownerName'] ?? $submission['full_name'],
                'owner_email'=>$payload['ownerEmail'] ?? $submission['email'],
                'phone'=>$payload['phone'] ?? $submission['phone'],
            ],
            'business'=>[
                'name'=>$payload['businessName'] ?? ($submission['trading_name'] ?: $submission['legal_name']),
                'type'=>$payload['businessType'] ?? $submission['business_type'],
                'industry'=>$submission['industry'],
                'website'=>$submission['website_url'],
            ],
            'documents'=>$files->fetchAll(),
        ]);
    }

    $page = max(1, (int) ($input['page'] ?? 1));
    $limit = max(10, min(100, (int) ($input['limit'] ?? 25)));
    $status = (string) ($input['status'] ?? '');
    if ($status !== '' && !in_array($status, $statuses, true)) jsonResponse(['success'=>false, 'error'=>'Invalid identity status filter.'], 422);
    $search = trim((string) ($input['q'] ?? ''));
    if (mb_strlen($search) > 100) jsonResponse(['success'=>false, 'error'=>'Search query is too long.'], 422);

    $conditions = ["s.status<>'draft'"];
    $params = [];
    if ($status !== '') { $conditions[] = 's.status=?'; $params[] = $status; }
    if ($search !== '') {
        $conditions[] = '(u.full_name LIKE ? OR u.email LIKE ? OR b.legal_name LIKE ? OR b.trading_name LIKE ?)';
        $needle = '%'.$search.'%';
        array_push($params, $needle, $needle, $needle, $needle);
    }
    $where = ' WHERE '.implode(' AND ', $conditions);
    $count = $pdo->prepare('SELECT COUNT(*) FROM onboarding_submissions s INNER JOIN users u ON u.id=s.user_id LEFT JOIN businesses b ON b.id=s.business_id'.$where);
    $count->execute($params); $total = (int) $count->fetchColumn();
    $offset = ($page - 1) * $limit;
    $list = $pdo->prepare($baseSelect.$where.' ORDER BY FIELD(s.status,\'submitted\',\'under_review\',\'rejected\',\'approved\'),s.updated_at ASC LIMIT '.$limit.' OFFSET '.$offset);
    $list->execute($params);
    $summary = $pdo->query("SELECT
        COALESCE(SUM(status='submitted'),0) submitted,
        COALESCE(SUM(status='under_review'),0) under_review,
        COALESCE(SUM(status='approved'),0) approved,
        COALESCE(SUM(status='rejected'),0) rejected
        FROM onboarding_submissions WHERE status<>'draft'")->fetch() ?: [];

    jsonResponse([
        'success'=>true, 'summary'=>array_map('intval', $summary), 'submissions'=>$list->fetchAll(),
        'pagination'=>['page'=>$page,'limit'=>$limit,'total'=>$total,'total_pages'=>max(1,(int)ceil($total/$limit)),'from'=>$total?$offset+1:0,'to'=>min($offset+$limit,$total)],
    ]);
}

$submissionId = (int) ($input['submission_id'] ?? 0);
$status = (string) ($input['status'] ?? '');
$note = trim((string) ($input['review_note'] ?? ''));
if ($submissionId < 1 || !in_array($status, $statuses, true) || $status === 'submitted') jsonResponse(['success'=>false, 'error'=>'Invalid review decision.'], 422);
if ($status === 'rejected' && mb_strlen($note) < 5) jsonResponse(['success'=>false, 'error'=>'Provide a clear reason for requesting changes.'], 422);
if (mb_strlen($note) > 500) jsonResponse(['success'=>false, 'error'=>'The review note is too long.'], 422);

$pdo->beginTransaction();
try {
    $record = $pdo->prepare('SELECT user_id,business_id,status FROM onboarding_submissions WHERE id=? FOR UPDATE');
    $record->execute([$submissionId]); $submission = $record->fetch();
    if (!$submission) throw new RuntimeException('not-found');
    if ($submission['status'] === 'approved' && $status !== 'approved') throw new DomainException('Approved identity submissions cannot be changed from this screen.');

    $update = $pdo->prepare('UPDATE onboarding_submissions SET status=?,review_note=? WHERE id=?');
    $update->execute([$status, $note !== '' ? $note : null, $submissionId]);
    if ($submission['business_id']) {
        $businessStatus = $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'under_review');
        $business = $pdo->prepare('UPDATE businesses SET verification_status=? WHERE id=?');
        $business->execute([$businessStatus, (int) $submission['business_id']]);
    }
    if ($status === 'approved') {
        $tier = $pdo->prepare('UPDATE users SET kyc_tier=GREATEST(kyc_tier,2) WHERE id=?');
        $tier->execute([(int) $submission['user_id']]);
    }
    adminAudit($pdo, (int) $session['admin']['id'], 'onboarding.reviewed', 'onboarding_submission', (string) $submissionId, ['status'=>$status, 'review_note'=>$note ?: null]);
    $pdo->commit();
    jsonResponse(['success'=>true, 'message'=>$status === 'approved' ? 'Identity submission approved.' : ($status === 'rejected' ? 'Changes requested from the customer.' : 'Submission moved to review.')]);
} catch (DomainException $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    jsonResponse(['success'=>false, 'error'=>$error->getMessage()], 422);
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Admin identity review failed: '.$error->getMessage());
    jsonResponse(['success'=>false, 'error'=>'Identity submission could not be updated.'], 404);
}

