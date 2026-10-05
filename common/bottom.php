</main>
<?php if(user_id()): ?>
<nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-slate-800 bg-[#0a0e16]/95 backdrop-blur">
<div class="max-w-2xl mx-auto grid grid-cols-4">
<?php
$nav=[
 ['index.php','fa-house','Home'],
 ['my_tournaments.php','fa-trophy','Tournaments'],
 ['wallet.php','fa-wallet','Wallet'],
 ['profile.php','fa-user','Profile']
];
foreach($nav as $n): ?>
<a href="<?=$n[0]?>" class="py-3 text-center text-xs text-slate-300 hover:text-violet-300"><i class="fa-solid <?=$n[1]?> block text-base mb-1"></i><?=e($n[2])?></a>
<?php endforeach; ?>
</div></nav>
<?php endif; ?>
<script>
document.addEventListener('contextmenu',e=>e.preventDefault());
document.addEventListener('selectstart',e=>e.preventDefault());
document.addEventListener('gesturestart',e=>e.preventDefault());
</script>
</body></html>
