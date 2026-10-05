<?php
require_once __DIR__.'/../../common/config.php'; require_admin(); $pageTitle=$pageTitle??'Admin'; $flash=get_flash();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($pageTitle)?> · Admin</title><script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><style>body{background:#080b12;color:#f8fafc}input,select,textarea{color-scheme:dark}.card{background:#111827;border:1px solid #20283a}</style></head><body>
<header class="border-b border-slate-800 bg-[#0a0e16]"><div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center"><a href="index.php" class="font-black text-xl"><span class="text-violet-400">Adept</span> Admin</a><a href="logout.php" class="text-sm text-rose-300">Logout</a></div></header>
<?php if($flash):?><div class="max-w-7xl mx-auto px-4 pt-4"><div class="rounded-xl p-3 <?= $flash[0]==='success'?'bg-emerald-500/10 text-emerald-300':'bg-rose-500/10 text-rose-300'?>"><?=e($flash[1])?></div></div><?php endif;?>
<main class="max-w-7xl mx-auto p-4">
