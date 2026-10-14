<?php
declare(strict_types=1);

/** Flowwithlit browser installer. Delete this file after production setup. */
session_start();
require_once __DIR__ . '/config/environment.php';
const FWL_ROOT = __DIR__;
const FWL_LOCK_FILE = FWL_ROOT . '/storage/install.lock';

function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function dbNameIsValid(string $name): bool { return (bool) preg_match('/^[A-Za-z0-9_]{1,64}$/', $name); }
function envValue(string $value): string { return preg_match('/^[A-Za-z0-9_.,:\/\-@]+$/', $value) ? $value : '"' . addcslashes($value, "\\\"") . '"'; }
function installerUuid(): string { $bytes = random_bytes(16); $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40); $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80); return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4)); }
function requirements(): array {
    return [
        'PHP 8.0 or newer' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'PDO MySQL extension' => extension_loaded('pdo_mysql'),
        'OpenSSL extension' => extension_loaded('openssl'),
        'Writable project folder' => is_writable(FWL_ROOT),
        'Writable storage folder' => is_dir(FWL_ROOT . '/storage') && is_writable(FWL_ROOT . '/storage'),
    ];
}
function installed(): bool {
    if (is_file(FWL_LOCK_FILE)) return true;
    $env = FWL_ROOT . '/.env';
    return is_file($env) && filesize($env) > 0;
}
function installedDatabase(): PDO {
    $host = getenv('FLOW_DB_HOST') ?: '127.0.0.1';
    $port = getenv('FLOW_DB_PORT') ?: '3306';
    $name = getenv('FLOW_DB_NAME') ?: 'flowwithlit_db';
    $user = getenv('FLOW_DB_USER') ?: 'root';
    $password = getenv('FLOW_DB_PASSWORD') ?: '';
    return new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]);
}
function firstAdminSetupAvailable(): bool {
    if (!installed()) return false;
    try {
        $pdo = installedDatabase();
        $exists = $pdo->query("SHOW TABLES LIKE 'admin_users'")->fetchColumn();
        return (bool) $exists && (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() === 0;
    } catch (Throwable $exception) { return false; }
}
function createFirstAdmin(string $name, string $email, string $password): void {
    if ($name === '' || mb_strlen($name) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
        throw new RuntimeException('Enter a name, valid email, and a password of at least 12 characters.');
    }
    $pdo = installedDatabase();
    if (!$pdo->query("SHOW TABLES LIKE 'admin_users'")->fetchColumn()) throw new RuntimeException('Admin database tables are not ready yet. Complete database installation first.');
    if ((int) $pdo->query("SELECT GET_LOCK('flowwithlit_admin_bootstrap',10)")->fetchColumn() !== 1) throw new RuntimeException('Initial administrator setup is busy. Please try again.');
    try {
        $pdo->beginTransaction();
        if ((int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0) throw new RuntimeException('An administrator already exists. Sign in instead.');
        $statement = $pdo->prepare("INSERT INTO admin_users(uuid,full_name,email,password_hash,role,status) VALUES(?,?,?,?, 'super_admin','active')");
        $statement->execute([installerUuid(), $name, strtolower($email), password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])]);
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $exception;
    } finally { $pdo->query("SELECT RELEASE_LOCK('flowwithlit_admin_bootstrap')"); }
}
function migrationFiles(): array {
    $files = glob(FWL_ROOT . '/database/migrations/*.sql') ?: [];
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    return $files;
}
function normalizeDatabaseCollation(PDO $pdo, string $database): void {
    $quoted = '`' . str_replace('`', '``', $database) . '`';
    $pdo->exec('ALTER DATABASE ' . $quoted . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $tables = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'");
    $tables->execute([$database]);
    foreach ($tables->fetchAll(PDO::FETCH_COLUMN) as $table) {
        $table = (string) $table;
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) continue;
        $pdo->exec('ALTER TABLE ' . $quoted . '.`' . $table . '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }
}
function provisionPrivateStorage(): string {
    // Keep customer documents and the encryption key outside the public htdocs folder.
    $root = dirname(FWL_ROOT, 2) . DIRECTORY_SEPARATOR . 'flowwithlit-private';
    if (!is_dir($root) && !mkdir($root, 0700, true)) throw new RuntimeException('Could not create private storage.');
    $root = realpath($root);
    if ($root === false || str_starts_with(strtolower(str_replace('\\', '/', $root)), strtolower(str_replace('\\', '/', FWL_ROOT)))) {
        throw new RuntimeException('Private storage must be outside the application folder.');
    }
    $uploads = $root . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploads) && !mkdir($uploads, 0700, true)) throw new RuntimeException('Could not create private uploads storage.');
    $key = $root . DIRECTORY_SEPARATOR . 'encryption.key';
    if (!is_file($key)) {
        $handle = @fopen($key, 'x+b');
        if ($handle === false) throw new RuntimeException('Could not create the private storage encryption key.');
        try {
            if (fwrite($handle, random_bytes(32)) !== 32) throw new RuntimeException('Could not write the private storage encryption key.');
        } finally { fclose($handle); }
        @chmod($key, 0600);
    }
    if (filesize($key) !== 32) throw new RuntimeException('The private storage encryption key is invalid. Restore the original key instead of replacing it.');
    return $root;
}
function runMigrations(PDO $pdo, string $database): int {
    foreach (migrationFiles() as $file) {
        $sql = file_get_contents($file);
        if ($sql === false) throw new RuntimeException('Could not read migration ' . basename($file) . '.');
        $quoted = '`' . str_replace('`', '``', $database) . '`';
        $sql = preg_replace('/CREATE\\s+DATABASE\\s+IF\\s+NOT\\s+EXISTS\\s+flowwithlit_db/i', 'CREATE DATABASE IF NOT EXISTS ' . $quoted, $sql);
        $sql = preg_replace('/USE\\s+flowwithlit_db\\s*;/i', 'USE ' . $quoted . ';', $sql);
        try {
            $pdo->exec($sql);
        } catch (Throwable $exception) {
            throw new RuntimeException('Migration ' . basename($file) . ' failed: ' . $exception->getMessage(), 0, $exception);
        }
    }
    return count(migrationFiles());
}

$defaults = ['host' => '127.0.0.1', 'port' => '3306', 'database' => 'flowwithlit_db', 'username' => 'root', 'password' => '', 'app_url' => ''];
$values = $defaults;
$error = '';
$success = false;
$adminCreated = false;
$migrationCount = 0;
$requirements = requirements();
$canInstall = !in_array(false, $requirements, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'create_first_admin') {
        if (!hash_equals($_SESSION['fwl_install_csrf'] ?? '', (string) ($_POST['csrf'] ?? ''))) {
            $error = 'Your session expired. Refresh the page and try again.';
        } else {
            try {
                createFirstAdmin(trim((string) ($_POST['admin_name'] ?? '')), trim((string) ($_POST['admin_email'] ?? '')), (string) ($_POST['admin_password'] ?? ''));
                $adminCreated = true;
            } catch (Throwable $exception) {
                $error = 'Administrator setup stopped: ' . $exception->getMessage();
            }
        }
    } else {
        $values = [];
        foreach ($defaults as $key => $default) {
            $values[$key] = trim((string) ($_POST[$key] ?? ''));
        }
        $values['password'] = (string) ($_POST['password'] ?? '');
        if (!hash_equals($_SESSION['fwl_install_csrf'] ?? '', (string) ($_POST['csrf'] ?? ''))) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif (!$canInstall) {
        $error = 'Fix the failed server requirements before installing.';
    } elseif (!dbNameIsValid($values['database'])) {
        $error = 'Database names can contain only letters, numbers, and underscores.';
    } elseif (!filter_var($values['port'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]])) {
        $error = 'Enter a valid MySQL port.';
    } elseif ($values['host'] === '' || $values['username'] === '') {
        $error = 'Database host and username are required.';
    } else {
        try {
            $serverDsn = 'mysql:host=' . $values['host'] . ';port=' . $values['port'] . ';charset=utf8mb4';
            $pdo = new PDO($serverDsn, $values['username'], $values['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]);
            if (isset($_POST['create_database'])) {
                $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $values['database']) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            }
            $exists = $pdo->prepare('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?');
            $exists->execute([$values['database']]);
            if (!$exists->fetchColumn()) throw new RuntimeException('The database does not exist. Enable “Create database” or create it first.');
            if (isset($_POST['normalize_collation'])) normalizeDatabaseCollation($pdo, $values['database']);
            $migrationCount = runMigrations($pdo, $values['database']);
            $privateStorage = provisionPrivateStorage();
            $url = rtrim($values['app_url'], '/');
            $origins = $url !== '' ? $url : 'http://localhost,http://127.0.0.1';
            $env = "# Generated by install.php on " . gmdate('c') . "\n"
                . 'FLOW_DB_HOST=' . envValue($values['host']) . "\n"
                . 'FLOW_DB_PORT=' . envValue($values['port']) . "\n"
                . 'FLOW_DB_NAME=' . envValue($values['database']) . "\n"
                . 'FLOW_DB_USER=' . envValue($values['username']) . "\n"
                . 'FLOW_DB_PASSWORD=' . envValue($values['password']) . "\n"
                . 'FLOW_PRIVATE_STORAGE=' . envValue($privateStorage) . "\n"
                . 'FLOW_APP_URL=' . envValue($url) . "\n"
                . 'FLOW_ALLOWED_ORIGINS=' . envValue($origins) . "\n";
            $temporary = FWL_ROOT . '/.env.installing';
            if (file_put_contents($temporary, $env, LOCK_EX) === false || !rename($temporary, FWL_ROOT . '/.env')) throw new RuntimeException('Could not save .env. Check folder permissions.');
            file_put_contents(FWL_LOCK_FILE, "Installed " . gmdate('c') . "\n", LOCK_EX);
            $success = true;
        } catch (Throwable $exception) {
            $error = 'Installation stopped: ' . $exception->getMessage();
        }
    }
    }
}
if (empty($_SESSION['fwl_install_csrf'])) $_SESSION['fwl_install_csrf'] = bin2hex(random_bytes(32));
$canCreateFirstAdmin = firstAdminSetupAvailable();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install Flowwithlit</title>
<style>body{margin:0;background:#f4f7fb;color:#17233b;font:16px/1.5 system-ui,-apple-system,"Segoe UI",sans-serif}.wrap{max-width:720px;margin:42px auto;padding:0 20px}.card{background:#fff;border:1px solid #e0e7f0;border-radius:16px;padding:30px;box-shadow:0 16px 45px #17233b12}h1{margin:0 0 6px;font-size:28px}p{color:#52627a}.tag{color:#2357b7;font-weight:700;font-size:13px;text-transform:uppercase;letter-spacing:.08em}.requirements{display:grid;grid-template-columns:1fr auto;gap:8px 14px;background:#f8fafc;padding:16px;border-radius:10px;margin:24px 0}.ok{color:#16734c}.bad{color:#b42318}label{display:block;font-weight:650;margin:15px 0 6px}input{box-sizing:border-box;width:100%;border:1px solid #bdc9d9;border-radius:8px;padding:11px;font:inherit}input:focus{outline:3px solid #b7d1ff;border-color:#2357b7}.row{display:grid;grid-template-columns:2fr 1fr;gap:14px}.check{display:flex;gap:9px;align-items:center;font-weight:500;margin:18px 0}.check input{width:auto}button{width:100%;border:0;border-radius:8px;background:#102b5d;color:white;padding:13px;font-weight:700;font-size:16px;cursor:pointer}button:hover{background:#173d7e}button:disabled{opacity:.55;cursor:not-allowed}.notice{padding:14px 16px;border-radius:9px;margin:20px 0}.error{background:#fff1f0;color:#9d1d16}.success{background:#eaf8ef;color:#136c42}.muted{font-size:14px}@media(max-width:560px){.wrap{margin:20px auto}.card{padding:22px}.row{grid-template-columns:1fr}}</style></head>
<body><main class="wrap"><section class="card"><div class="tag">Flowwithlit setup</div><h1><?php echo $success ? 'Installation complete' : ($adminCreated ? 'Super administrator created' : (installed() ? 'Flowwithlit is already installed' : 'Install Flowwithlit')); ?></h1>
<?php if ($success): ?><div class="notice success">Your database schema and local configuration are ready. For security, delete <code>install.php</code> before making the site public.</div><p><a href="index.php">Open Flowwithlit</a></p>
<?php elseif ($adminCreated): ?><div class="notice success">Your first super administrator is ready. You can now sign in to the protected admin console.</div><p><a href="admin/login.php">Go to admin sign in</a></p>
<?php elseif (installed() && $canCreateFirstAdmin): ?><p>Your database is already configured, but no administrator exists. You can create the first super administrator now.</p><?php if ($error): ?><div class="notice error"><?php echo e($error); ?></div><?php endif; ?><button id="show-admin-setup" type="button">Set up first super administrator</button><form method="post" id="first-admin-form" hidden><input type="hidden" name="csrf" value="<?php echo e($_SESSION['fwl_install_csrf']); ?>"><input type="hidden" name="action" value="create_first_admin"><label for="admin_name">Full name</label><input id="admin_name" name="admin_name" required autocomplete="name" placeholder="Your full name"><label for="admin_email">Work email</label><input id="admin_email" name="admin_email" type="email" required autocomplete="email" placeholder="you@company.com"><label for="admin_password">Password</label><input id="admin_password" name="admin_password" type="password" required minlength="12" autocomplete="new-password" placeholder="At least 12 characters"><button type="submit">Create super administrator</button></form><p class="muted">This setup option closes permanently after the first administrator is created. Delete <code>install.php</code> immediately afterward.</p>
<?php elseif (installed()): ?><div class="notice success">A local configuration or install lock already exists. This installer will not overwrite it.</div><p class="muted">To install again in a disposable development environment, remove <code>storage/install.lock</code> and <code>.env</code> manually first.</p>
<?php else: ?>
<p>Connect your MySQL database, then this page creates the schema and a private <code>.env</code> configuration file.</p>
<div class="requirements"><?php foreach ($requirements as $label => $passed): ?><span><?php echo e($label); ?></span><strong class="<?php echo $passed ? 'ok' : 'bad'; ?>"><?php echo $passed ? 'Ready' : 'Needs attention'; ?></strong><?php endforeach; ?></div>
<?php if ($error): ?><div class="notice error"><?php echo e($error); ?></div><?php endif; ?>
<form method="post" id="install-form"><input type="hidden" name="csrf" value="<?php echo e($_SESSION['fwl_install_csrf']); ?>"><div class="row"><div><label for="host">MySQL host</label><input id="host" name="host" required value="<?php echo e($values['host']); ?>" placeholder="127.0.0.1"></div><div><label for="port">Port</label><input id="port" name="port" required inputmode="numeric" value="<?php echo e($values['port']); ?>"></div></div><label for="database">Database name</label><input id="database" name="database" required value="<?php echo e($values['database']); ?>"><label for="username">Database username</label><input id="username" name="username" required value="<?php echo e($values['username']); ?>"><label for="password">Database password</label><input id="password" name="password" type="password" value="<?php echo e($values['password']); ?>" autocomplete="new-password"><label for="app_url">Application URL <span class="muted">optional</span></label><input id="app_url" name="app_url" type="url" value="<?php echo e($values['app_url']); ?>" placeholder="http://localhost/flowwithlit"><label class="check"><input type="checkbox" name="create_database" checked> Create database if it does not exist</label><label class="check"><input type="checkbox" name="normalize_collation" checked> Standardize this database's text collation for compatibility</label><p class="muted">This changes text collation in the selected database only; it prevents MariaDB collation conflicts during setup.</p><button type="submit" <?php echo $canInstall ? '' : 'disabled'; ?>>Install Flowwithlit</button></form>
<?php endif; ?></section></main><script>document.getElementById('install-form')?.addEventListener('submit',function(){const b=this.querySelector('button');b.disabled=true;b.textContent='Installing…';});document.getElementById('show-admin-setup')?.addEventListener('click',function(){this.hidden=true;document.getElementById('first-admin-form').hidden=false;document.getElementById('admin_name').focus();});document.getElementById('first-admin-form')?.addEventListener('submit',function(){const b=this.querySelector('button');b.disabled=true;b.textContent='Creating administrator…';});</script></body></html>
