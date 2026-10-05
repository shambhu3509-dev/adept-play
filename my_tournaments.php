<?php
require_once __DIR__.'/common/config.php'; require_user();
$tab=$_GET['tab']??'live';
if($tab==='live'){
 $s=$pdo->prepare("SELECT t.*,p.room_id,p.room_password,p.joined_at FROM participants p JOIN tournaments t ON t.id=p.tournament_id WHERE p.user_id=? AND t.status!='completed' ORDER BY t.match_time"); $s->execute([user_id()]);
}else{
 $s=$pdo->prepare("SELECT t.*,p.joined_at,p.result FROM participants p JOIN tournaments t ON t.id=p.tournament_id WHERE p.user_id=? AND t.status='completed' ORDER BY t.match_time DESC"); $s->execute([user_id()]);
}
$rows=$s->fetchAll(); $pageTitle='My Tournaments'; require __DIR__.'/common/header.php';
?>
<div class="flex items-end justify-between mb-5"><div><h1 class="text-2xl font-black">My Tournaments</h1><p class="text-slate-400 text-sm">Your joined matches.</p></div></div>
<div class="flex p-1 bg-slate-900 rounded-xl mb-5"><a class="flex-1 text-center py-2 rounded-lg <?= $tab==='live'?'bg-violet-600':''?>" href="my_tournaments.php?tab=live">Upcoming/Live</a><a class="flex-1 text-center py-2 rounded-lg <?= $tab==='completed'?'bg-violet-600':''?>" href="my_tournaments.php?tab=completed">Completed</a></div>
<div class="space-y-3">
<?php foreach($rows as $r): ?><div class="card rounded-2xl p-4">
<div class="flex justify-between"><h2 class="font-bold"><?=e($r['title'])?></h2><span class="text-xs text-slate-400"><?=e($r['game_name'])?></span></div>
<p class="text-sm text-slate-400 mt-1"><?=date('d M Y, h:i A',strtotime($r['match_time']))?></p>
<?php if($tab==='live'): ?><div class="mt-3 rounded-xl bg-slate-900 p-3 text-sm">Room ID: <b><?=e($r['room_id']?:'Not released')?></b> · Password: <b><?=e($r['room_password']?:'—')?></b></div>
<?php else: ?><div class="mt-3 text-sm">Result: <span class="text-emerald-300"><?=e($r['result']?:'Participated')?></span></div><?php endif; ?>
</div><?php endforeach; ?>
<?php if(!$rows): ?><div class="text-center text-slate-500 py-10">Nothing here yet.</div><?php endif; ?>
</div>
<?php require __DIR__.'/common/bottom.php'; ?>
