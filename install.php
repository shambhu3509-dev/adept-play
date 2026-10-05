<?php
declare(strict_types=1);
$dbHost = getenv('DB_HOST') ?: '127.0.0.1'; $dbName = getenv('DB_NAME') ?: 'adept_play'; $dbUser = getenv('DB_USER') ?: 'root'; $dbPass = getenv('DB_PASS') ?: '';
$message='';$error='';
try{
 $pdo=new PDO("mysql:host={$dbHost};charset=utf8mb4",$dbUser,$dbPass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $pdo->exec("CREATE DATABASE IF NOT EXISTS `".str_replace('`','',$dbName)."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
 $pdo->exec("USE `".str_replace('`','',$dbName)."`");
 $sql=[
 "CREATE TABLE IF NOT EXISTS users (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,username VARCHAR(40) NOT NULL UNIQUE,email VARCHAR(190) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,wallet_balance DECIMAL(12,2) NOT NULL DEFAULT 0,upi_id VARCHAR(190) NULL,is_blocked TINYINT(1) NOT NULL DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB",
 "CREATE TABLE IF NOT EXISTS admin (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,username VARCHAR(80) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL) ENGINE=InnoDB",
 "CREATE TABLE IF NOT EXISTS tournaments (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,title VARCHAR(150) NOT NULL,game_name VARCHAR(100) NOT NULL,entry_fee DECIMAL(12,2) NOT NULL DEFAULT 0,prize_pool DECIMAL(12,2) NOT NULL DEFAULT 0,match_time DATETIME NOT NULL,commission_percentage DECIMAL(5,2) NOT NULL DEFAULT 0,room_id VARCHAR(100) NULL,room_password VARCHAR(100) NULL,status ENUM('upcoming','live','completed') NOT NULL DEFAULT 'upcoming',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB",
 "CREATE TABLE IF NOT EXISTS participants (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id INT UNSIGNED NOT NULL,tournament_id INT UNSIGNED NOT NULL,room_id VARCHAR(100) NULL,room_password VARCHAR(100) NULL,result VARCHAR(40) NULL,joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,UNIQUE KEY uq_participant(user_id,tournament_id),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE) ENGINE=InnoDB",
 "CREATE TABLE IF NOT EXISTS transactions (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id INT UNSIGNED NOT NULL,amount DECIMAL(12,2) NOT NULL,type ENUM('credit','debit') NOT NULL,description VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB",
 "CREATE TABLE IF NOT EXISTS deposits (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id INT UNSIGNED NOT NULL,amount DECIMAL(12,2) NOT NULL,transaction_id VARCHAR(120) NOT NULL,status ENUM('Pending','Completed','Rejected') NOT NULL DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB",
 "CREATE TABLE IF NOT EXISTS withdrawals (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id INT UNSIGNED NOT NULL,amount DECIMAL(12,2) NOT NULL,status ENUM('Pending','Completed','Rejected') NOT NULL DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB",
 "CREATE TABLE IF NOT EXISTS settings (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,setting_key VARCHAR(100) NOT NULL UNIQUE,setting_value TEXT NULL) ENGINE=InnoDB"
 ];
 foreach($sql as $q)$pdo->exec($q);
 $hash=password_hash('admin123',PASSWORD_DEFAULT);
 $s=$pdo->prepare("INSERT INTO admin(username,password) VALUES('admin',?) ON DUPLICATE KEY UPDATE password=VALUES(password)");$s->execute([$hash]);
 $pdo->prepare("INSERT INTO settings(setting_key,setting_value) VALUES('admin_upi',''),('upi_qr','') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)")->execute();
 $message='Installation completed successfully. Admin login: admin / admin123';
}catch(Throwable $e){$error=$e->getMessage();}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><script src="https://cdn.tailwindcss.com"></script><title>Adept Play Install</title></head><body class="bg-slate-950 text-white min-h-screen grid place-items-center p-4"><div class="max-w-xl w-full bg-slate-900 border border-slate-800 rounded-3xl p-6"><h1 class="text-2xl font-black">Adept Play Installer</h1><?php if($message):?><div class="mt-4 bg-emerald-500/10 text-emerald-300 p-4 rounded-xl"><?=htmlspecialchars($message)?></div><p class="text-slate-400 mt-4">Delete install.php after successful installation.</p><a href="login.php" class="inline-block mt-4 bg-violet-600 rounded-xl px-5 py-3">Open User Login</a><a href="admin/login.php" class="inline-block mt-4 ml-2 bg-slate-700 rounded-xl px-5 py-3">Admin</a><?php else:?><div class="mt-4 bg-rose-500/10 text-rose-300 p-4 rounded-xl"><?=htmlspecialchars($error)?></div><?php endif;?></div></body></html>
