<?php
require_once __DIR__.'/common/config.php';
if(user_id()) redirect('index.php');
$mode = ($_GET['mode'] ?? 'login') === 'signup' ? 'signup' : 'login';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $action=$_POST['action'] ?? 'login';
    try {
        if($action==='signup'){
            $username=post_string('username'); $email=post_string('email'); $password=(string)($_POST['password']??'');
            if(strlen($username)<3 || strlen($username)>40) throw new RuntimeException('Username must be 3-40 characters.');
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Enter a valid email.');
            if(strlen($password)<6) throw new RuntimeException('Password must be at least 6 characters.');
            $s=$pdo->prepare("SELECT id FROM users WHERE username=? OR email=?"); $s->execute([$username,$email]);
            if($s->fetch()) throw new RuntimeException('Username or email already exists.');
            $s=$pdo->prepare("INSERT INTO users(username,email,password,wallet_balance) VALUES(?,?,?,0)");
            $s->execute([$username,$email,password_hash($password,PASSWORD_DEFAULT)]);
            $_SESSION['user_id']=(int)$pdo->lastInsertId();
            flash('success','Account created successfully.'); redirect('index.php');
        } else {
            $login=post_string('login'); $password=(string)($_POST['password']??'');
            $s=$pdo->prepare("SELECT * FROM users WHERE username=? OR email=?"); $s->execute([$login,$login]); $u=$s->fetch();
            if(!$u || !password_verify($password,$u['password'])) throw new RuntimeException('Invalid username/email or password.');
            if((int)$u['is_blocked']===1) throw new RuntimeException('Your account is blocked.');
            $_SESSION['user_id']=(int)$u['id']; session_regenerate_id(true); redirect('index.php');
        }
    } catch(Throwable $e){ $error=$e->getMessage(); }
}
$pageTitle=$mode==='signup'?'Create Account':'Login'; require __DIR__.'/common/header.php';
?>
<div class="max-w-md mx-auto mt-6">
<div class="card rounded-3xl p-6 shadow-2xl">
<div class="text-center mb-6"><div class="w-14 h-14 rounded-2xl bg-violet-500/20 mx-auto flex items-center justify-center text-violet-300 text-2xl"><i class="fa-solid fa-gamepad"></i></div><h1 class="text-2xl font-black mt-3"><?= $mode==='signup'?'Join Adept Play':'Welcome Back' ?></h1><p class="text-slate-400 text-sm mt-1">Tournament play made simple.</p></div>
<?php if($error): ?><div class="mb-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 p-3 text-sm"><?=e($error)?></div><?php endif; ?>
<form method="post" class="space-y-4">
<input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<input type="hidden" name="action" value="<?=$mode?>">
<?php if($mode==='signup'): ?>
<label class="block text-sm text-slate-300">Username<input name="username" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-700 p-3" autocomplete="username"></label>
<label class="block text-sm text-slate-300">Email<input type="email" name="email" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-700 p-3"></label>
<?php else: ?>
<label class="block text-sm text-slate-300">Username or Email<input name="login" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-700 p-3" autocomplete="username"></label>
<?php endif; ?>
<label class="block text-sm text-slate-300">Password<input type="password" name="password" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-700 p-3" autocomplete="<?=$mode==='signup'?'new-password':'current-password'?>"></label>
<button class="w-full rounded-xl bg-violet-600 hover:bg-violet-500 py-3 font-bold"><?=$mode==='signup'?'Create Account':'Login'?></button>
</form>
<div class="text-center mt-5 text-sm text-slate-400">
<?php if($mode==='signup'): ?>Already registered? <a class="text-violet-300" href="login.php">Login</a>
<?php else: ?>New here? <a class="text-violet-300" href="login.php?mode=signup">Create account</a><?php endif; ?>
</div>
</div></div>
<?php require __DIR__.'/common/bottom.php'; ?>
