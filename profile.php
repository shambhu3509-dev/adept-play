<?php
require_once __DIR__.'/common/config.php'; require_user(); $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();try{
 $action=$_POST['action']??'';
 if($action==='profile'){ $username=post_string('username');$email=post_string('email');$upi=post_string('upi_id'); if(!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Invalid email.'); $s=$pdo->prepare("UPDATE users SET username=?,email=?,upi_id=? WHERE id=?");$s->execute([$username,$email,$upi,user_id()]);flash('success','Profile updated.');}
 if($action==='password'){ $old=(string)($_POST['old_password']??'');$new=(string)($_POST['new_password']??'');$u=get_user($pdo,user_id());if(!password_verify($old,$u['password']))throw new RuntimeException('Current password is incorrect.');if(strlen($new)<6)throw new RuntimeException('New password must be at least 6 characters.');$pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new,PASSWORD_DEFAULT),user_id()]);flash('success','Password changed.');}
 redirect('profile.php');
}catch(Throwable $e){$error=$e->getMessage();}}
$u=get_user($pdo,user_id());$pageTitle='Profile';require __DIR__.'/common/header.php';
?>
<div class="max-w-2xl mx-auto space-y-5"><div><h1 class="text-2xl font-black">Profile</h1><p class="text-slate-400">Manage your account and UPI details.</p></div>
<?php if($error):?><div class="rounded-xl bg-rose-500/10 text-rose-300 border border-rose-500/30 p-3"><?=$error?></div><?php endif;?>
<div class="card rounded-2xl p-5"><form method="post" class="space-y-4"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="profile"><label class="block text-sm">Username<input name="username" value="<?=e($u['username'])?>" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-700 p-3"></label><label class="block text-sm">Email<input type="email" name="email" value="<?=e($u['email'])?>" required class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-700 p-3"></label><label class="block text-sm">UPI ID<input name="upi_id" value="<?=e($u['upi_id']??'')?>" placeholder="yourname@upi" class="mt-1 w-full rounded-xl bg-slate-900 border border-slate-700 p-3"></label><button class="bg-violet-600 rounded-xl py-3 px-5 font-bold">Save Changes</button></form></div>
<div class="card rounded-2xl p-5"><h2 class="font-bold mb-4">Change Password</h2><form method="post" class="space-y-4"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="password"><input type="password" name="old_password" placeholder="Current password" required class="w-full rounded-xl bg-slate-900 border border-slate-700 p-3"><input type="password" name="new_password" placeholder="New password" required class="w-full rounded-xl bg-slate-900 border border-slate-700 p-3"><button class="bg-slate-700 rounded-xl py-3 px-5 font-bold">Change Password</button></form></div>
<a href="logout.php" class="block text-center rounded-xl border border-rose-500/30 text-rose-300 py-3">Logout</a></div>
<?php require __DIR__.'/common/bottom.php'; ?>
