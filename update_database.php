<?php
require_once __DIR__.'/common/config.php';
$queries=[
"CREATE TABLE IF NOT EXISTS deposits (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id INT UNSIGNED NOT NULL,amount DECIMAL(12,2) NOT NULL,transaction_id VARCHAR(120) NOT NULL,status ENUM('Pending','Completed','Rejected') NOT NULL DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB",
"CREATE TABLE IF NOT EXISTS withdrawals (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id INT UNSIGNED NOT NULL,amount DECIMAL(12,2) NOT NULL,status ENUM('Pending','Completed','Rejected') NOT NULL DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB",
"CREATE TABLE IF NOT EXISTS settings (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,setting_key VARCHAR(100) NOT NULL UNIQUE,setting_value TEXT NULL) ENGINE=InnoDB"
];
foreach($queries as $q)$pdo->exec($q);
try{$pdo->exec("ALTER TABLE users ADD COLUMN upi_id VARCHAR(190) NULL");}catch(Throwable $e){}
?>
<!doctype html><html><body style="font-family:system-ui;background:#080b12;color:#fff;padding:40px"><h2>✓ Database schema updated successfully!</h2><p>You can now safely delete this file.</p></body></html>
