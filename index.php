<?php
require_once __DIR__.'/common/config.php'; require_user();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $tid=(int)($_POST['tournament_id']??0);
 try{
  $pdo->beginTransaction();
  $s=$pdo->prepare("SELECT * FROM tournaments WHERE id=? FOR UPDATE"); $s->execute([$tid]); $t=$s->fetch();
  if(!$t || $t['status']!=='upcoming') throw new RuntimeException('Tournament is not available.');
  $s=$pdo->prepare("SELECT COUNT(*) c FROM participants WHERE tournament_id=? AND user_id=?"); $s->execute([$tid,user_id()]);
  if((int)$s->fetch()['c']>0) throw new RuntimeException('You have already joined this tournament.');
  $s=$pdo->prepare("SELECT wallet_balance FROM users WHERE id=? FOR UPDATE"); $s->execute([user_id()]); $bal=(float)$s->fetch()['wallet_balance'];
  $fee=(float)$t['entry_fee']; if($bal<$fee) throw new RuntimeException('Insufficient wallet balance.');
  if($fee>0){
   $pdo->prepare("UPDATE users SET wallet_balance=wallet_balance-? WHERE id=?")->execute([$fee,user_id()]);
   $pdo->prepare("INSERT INTO transactions(user_id,amount,type,description) VALUES(?,?,?,?)")->execute([user_id(),$fee,'debit','Tournament entry: '.$t['title']]);
  }
  $pdo->prepare("INSERT INTO participants(user_id,tournament_id) VALUES(?,?)")->execute([user_id(),$tid]);
  $pdo->commit(); flash('success','Tournament joined successfully.'); redirect('index.php');
 }catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); flash('error',$e->getMessage()); redirect('index.php'); }
}
$s=$pdo->query("SELECT * FROM tournaments WHERE status='upcoming' ORDER BY match_time ASC");
$tournaments=$s->fetchAll();
$pageTitle='Home'; require __DIR__.'/common/header.php';
?>
<div class="mb-6">
<h1 class="text-3xl font-black">Upcoming Tournaments</h1><p class="text-slate-400 mt-1">Pick a match and join instantly.</p>
</div>
<?php if(!$tournaments): ?><div class="card rounded-2xl p-8 text-center text-slate-400">No upcoming tournaments right now.</div><?php endif; ?>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
<?php foreach($tournaments as $t): ?>
<div class="card rounded-2xl p-5">
<div class="flex justify-between gap-3"><span class="text-xs px-2 py-1 rounded-full bg-violet-500/15 text-violet-300"><?=e($t['game_name'])?></span><span class="text-xs text-slate-400"><?=date('d M, h:i A',strtotime($t['match_time']))?></span></div>
<h2 class="text-xl font-bold mt-4"><?=e($t['title'])?></h2>
<div class="grid grid-cols-2 gap-3 mt-4 text-sm"><div><span class="text-slate-500">Entry</span><div class="font-bold"><?=money($t['entry_fee'])?></div></div><div><span class="text-slate-500">Prize Pool</span><div class="font-bold text-emerald-300"><?=money($t['prize_pool'])?></div></div></div>
<form method="post" class="mt-5"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="tournament_id" value="<?=$t['id']?>"><button class="w-full rounded-xl bg-violet-600 py-3 font-bold">Join Now</button></form>
</div>
<?php endforeach; ?></div>
<?php require __DIR__.'/common/bottom.php'; ?>
