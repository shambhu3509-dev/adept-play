<?php
require_once __DIR__.'/config.php';
$pageTitle = $pageTitle ?? APP_NAME;
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<title><?=e($pageTitle)?> · <?=e(APP_NAME)?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config={theme:{extend:{colors:{ink:'#080b12',panel:'#111827',accent:'#7c3aed',cyan:'#06b6d4'}}}};
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
html,body{background:#080b12;color:#f8fafc}
body{overscroll-behavior:none}
.card{background:linear-gradient(145deg,#121826,#0d111b);border:1px solid #20283a}
input,select,textarea{color-scheme:dark}
.safe-bottom{padding-bottom:88px}
</style>
</head>
<body class="min-h-screen">
<header class="sticky top-0 z-40 border-b border-slate-800 bg-[#080b12]/95 backdrop-blur">
<div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
<a href="index.php" class="font-black tracking-tight text-xl"><span class="text-violet-400">Adept</span> Play</a>
<div class="flex items-center gap-3">
<?php if(user_id()): $me=get_user($pdo,user_id()); ?>
<span class="hidden sm:inline text-sm text-slate-400"><?=e($me['username'] ?? '')?></span>
<a href="wallet.php" class="text-cyan-300"><i class="fa-solid fa-wallet"></i></a>
<?php endif; ?>
</div>
</div>
</header>
<?php if($flash): ?>
<div class="max-w-6xl mx-auto px-4 pt-4"><div class="rounded-xl px-4 py-3 text-sm <?= $flash[0]==='success'?'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30':'bg-rose-500/15 text-rose-300 border border-rose-500/30' ?>"><?=e($flash[1])?></div></div>
<?php endif; ?>
<main class="max-w-6xl mx-auto px-4 py-5 safe-bottom">
