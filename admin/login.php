<?php
require_once __DIR__.'/../common/config.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$u=post_string('username');$p=(string)($_POST['password']??'');$s=$pdo->prepare("SELECT * FROM admin WHERE username=?");$s->execute([$u]);$a=$s->fetch();if($a&&password_verify($p,$a['password'])){$_SESSION['admin_id']=(int)$a['id'];redirect('index.php');}$error='Invalid admin credentials.';}
$pageTitle='Admin Login';require __DIR__.'/../common/header.php';
?>
<div class="max-w-md mx-auto mt-10 card rounded-3xl p-6"><h1 class="text-2xl font-black">Admin Login</h1><p class="text-slate-400 mt-1 mb-6">Secure dashboard access.</p><?php if($error):?><div class="mb-4 text-rose-300"><?=$error?></div><?php endif;?><form method="post" class="space-y-4"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input name="username" placeholder="Username" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3"><input type="password" name="password" placeholder="Password" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3"><button class="w-full bg-violet-600 rounded-xl py-3 font-bold">Login</button></form></div>
<?php require __DIR__.'/common/bottom.php'; ?>
