<?php
require_once __DIR__.'/common/config.php'; require_user();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $action=$_POST['action']??'';
 try{
  if($action==='deposit'){
   $amount=post_string('amount'); $txn=post_string('transaction_id');
   if(!valid_amount($amount)) throw new RuntimeException('Enter a valid amount.');
   if(strlen($txn)<3 || strlen($txn)>120) throw new RuntimeException('Enter the UPI transaction ID.');
   $s=$pdo->prepare("INSERT INTO deposits(user_id,amount,transaction_id,status) VALUES(?,?,?,'Pending')");
   $s->execute([user_id(),$amount,$txn]); flash('success','Deposit request submitted for verification.');
  } elseif($action==='withdraw'){
   $amount=post_string('amount'); if(!valid_amount($amount)) throw new RuntimeException('Enter a valid amount.');
   $pdo->beginTransaction(); $s=$pdo->prepare("SELECT wallet_balance,upi_id FROM users WHERE id=? FOR UPDATE"); $s->execute([user_id()]); $u=$s->fetch();
   if(!$u['upi_id']) throw new RuntimeException('Please save your UPI ID in Profile first.');
   if((float)$u['wallet_balance'] < (float)$amount) throw new RuntimeException('Insufficient wallet balance.');
   $pdo->prepare("INSERT INTO withdrawals(user_id,amount,status) VALUES(?,?,'Pending')")->execute([user_id(),$amount]);
   $pdo->commit(); flash('success','Withdrawal request submitted.');
  }
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('error',$e->getMessage());}
 redirect('wallet.php');
}
$me=get_user($pdo,user_id()); $qr=setting($pdo,'upi_qr'); $upi=setting($pdo,'admin_upi');
$s=$pdo->prepare("SELECT * FROM transactions WHERE user_id=? ORDER BY created_at DESC LIMIT 20");$s->execute([user_id()]);$tx=$s->fetchAll();
$pageTitle='Wallet'; require __DIR__.'/common/header.php';
?>
<div class="grid lg:grid-cols-3 gap-5">
<div class="lg:col-span-2">
<div class="card rounded-3xl p-6"><p class="text-slate-400 text-sm">Current Wallet Balance</p><div class="text-4xl font-black mt-2"><?=money($me['wallet_balance'])?></div>
<div class="grid grid-cols-2 gap-3 mt-6"><button onclick="document.getElementById('deposit').showModal()" class="rounded-xl bg-violet-600 py-3 font-bold"><i class="fa-solid fa-plus mr-2"></i>Add Money</button><button onclick="document.getElementById('withdraw').showModal()" class="rounded-xl bg-slate-700 py-3 font-bold"><i class="fa-solid fa-arrow-up-right-from-square mr-2"></i>Withdraw</button></div></div>
<div class="mt-5"><h2 class="font-bold text-lg mb-3">Transaction History</h2><div class="space-y-2"><?php foreach($tx as $r): ?><div class="card rounded-xl p-3 flex justify-between"><div><div class="font-medium"><?=e($r['description'])?></div><div class="text-xs text-slate-500"><?=date('d M Y, h:i A',strtotime($r['created_at']))?></div></div><span class="<?=$r['type']==='credit'?'text-emerald-300':'text-rose-300'?>"><?=($r['type']==='credit'?'+':'-').money($r['amount'])?></span></div><?php endforeach; ?><?php if(!$tx): ?><p class="text-slate-500">No transactions yet.</p><?php endif; ?></div></div>
</div>
<div class="card rounded-2xl p-5 h-fit"><h2 class="font-bold">Pay via UPI</h2><?php if($qr): ?><img src="<?=e($qr)?>" class="w-full max-w-xs mx-auto rounded-xl mt-4" alt="Admin UPI QR"><?php else: ?><div class="mt-4 p-5 rounded-xl bg-slate-900 text-slate-400 text-center">Admin QR not configured.</div><?php endif; ?><?php if($upi): ?><p class="text-center text-sm text-slate-400 mt-3">UPI ID: <b class="text-white"><?=e($upi)?></b></p><?php endif; ?></div>
</div>
<dialog id="deposit" class="bg-transparent p-0 backdrop:bg-black/80 w-full max-w-md"><div class="card rounded-2xl p-5 m-4"><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="deposit"><h2 class="text-xl font-bold">Add Money</h2><label class="block text-sm text-slate-300 mt-4">Amount (₹)<input name="amount" type="number" min="1" step="0.01" required class="mt-1 w-full bg-slate-900 border border-slate-700 rounded-xl p-3"></label><label class="block text-sm text-slate-300 mt-4">UPI Transaction ID<input name="transaction_id" required class="mt-1 w-full bg-slate-900 border border-slate-700 rounded-xl p-3"></label><div class="flex gap-2 mt-5"><button class="flex-1 bg-violet-600 rounded-xl py-3 font-bold">Submit</button><button type="button" onclick="deposit.close()" class="flex-1 bg-slate-700 rounded-xl py-3">Cancel</button></div></form></div></dialog>
<dialog id="withdraw" class="bg-transparent p-0 backdrop:bg-black/80 w-full max-w-md"><div class="card rounded-2xl p-5 m-4"><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="withdraw"><h2 class="text-xl font-bold">Withdraw</h2><p class="text-sm text-slate-400 mt-2">Saved UPI: <?=e($me['upi_id']?:'Not set')?></p><label class="block text-sm text-slate-300 mt-4">Amount to Withdraw (₹)<input name="amount" type="number" min="1" step="0.01" required class="mt-1 w-full bg-slate-900 border border-slate-700 rounded-xl p-3"></label><div class="flex gap-2 mt-5"><button class="flex-1 bg-violet-600 rounded-xl py-3 font-bold">Request</button><button type="button" onclick="withdraw.close()" class="flex-1 bg-slate-700 rounded-xl py-3">Cancel</button></div></form></div></dialog>
<?php require __DIR__.'/common/bottom.php'; ?>
