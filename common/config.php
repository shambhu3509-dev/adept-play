<?php
declare(strict_types=1);
$s="/data/data/com.termux/files/home/php_sessions";if(!is_dir($s)){mkdir($s,0700,true);}session_save_path($s);session_set_cookie_params(["path"=>"/","httponly"=>true,"samesite"=>"Lax"]);session_start();

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'adept_play';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser, $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Check common/config.php and MySQL settings.');
}

if (!defined('APP_NAME')) define('APP_NAME', 'Adept Play');
if (!defined('BASE_URL')) define('BASE_URL', '');

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header("Location: {$url}"); exit; }
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); exit('Invalid security token. Please go back and try again.');
    }
}
function flash(string $type, string $message): void { $_SESSION['flash'] = [$type, $message]; }
function get_flash(): ?array {
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function user_id(): ?int { return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; }
function admin_id(): ?int { return isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null; }
function require_user(): void { if (!user_id()) redirect('login.php'); }
function require_admin(): void { if (!admin_id()) redirect('login.php'); }
function money(float|int|string $n): string { return '₹' . number_format((float)$n, 2); }
function post_string(string $key): string { return trim((string)($_POST[$key] ?? '')); }
function valid_amount(string $v): bool { return is_numeric($v) && (float)$v > 0 && (float)$v <= 10000000; }
function get_user(PDO $pdo, int $id): ?array {
    $s=$pdo->prepare("SELECT * FROM users WHERE id=?"); $s->execute([$id]); return $s->fetch() ?: null;
}
function setting(PDO $pdo, string $key, string $default=''): string {
    $s=$pdo->prepare("SELECT setting_value FROM settings WHERE setting_key=?"); $s->execute([$key]);
    $r=$s->fetch(); return $r ? (string)$r['setting_value'] : $default;
}
function set_setting(PDO $pdo, string $key, string $value): void {
    $s=$pdo->prepare("INSERT INTO settings(setting_key,setting_value) VALUES(?,?)
                      ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
    $s->execute([$key,$value]);
}
function upload_qr(array $file): string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new RuntimeException('Please select a QR image.');
    if (($file['size'] ?? 0) > 3*1024*1024) throw new RuntimeException('QR image must be under 3 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed = ['image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Only PNG, JPG or WEBP QR images are allowed.');
    $name = 'upi_qr_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $dir = dirname(__DIR__) . '/uploads';
    if (!is_dir($dir)) mkdir($dir,0755,true);
    if (!move_uploaded_file($file['tmp_name'], $dir.'/'.$name)) throw new RuntimeException('Could not save QR image.');
    return 'uploads/'.$name;
}
