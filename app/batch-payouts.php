<?php
$pageTitle='Batch payouts — Flowwithlit';
$pageDesc='Prepare and review multiple payout requests before submitting them for processing.';
$activePage='payments';$activeNavTab='Features';
require_once __DIR__.'/includes/header.php';require_once __DIR__.'/includes/sidebar.php';
?>
<main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6">
  <div class="max-w-4xl mx-auto">
    <a href="payments.php" class="text-xs font-semibold text-gray-500 hover:text-[#0b2545]">← Payments</a>
    <div class="mt-3 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
      <div><h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Batch payouts</h1><p class="mt-1 text-sm text-gray-500">Upload a prepared payout file for review. Nothing is sent until the batch is validated and explicitly submitted.</p></div>
      <span class="rounded-full bg-amber-50 border border-amber-200 px-3 py-2 text-xs font-bold text-amber-700">Review required</span>
    </div>
    <section class="mt-6 rounded-3xl border border-gray-200/80 bg-white p-5 sm:p-7 shadow-sm">
      <h2 class="text-lg font-bold text-gray-900">Prepare a batch</h2>
      <p class="mt-1 text-sm text-gray-500">Use one row per recipient. Bank verification and approval are required before money moves.</p>
      <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <button type="button" id="batchUploadButton" class="rounded-2xl border-2 border-dashed border-gray-300 p-8 text-center text-sm font-semibold text-gray-700 hover:border-[#0b2545]">Choose CSV file</button>
        <div class="rounded-2xl bg-gray-50 p-5"><h3 class="text-sm font-bold text-gray-900">Required columns</h3><p class="mt-2 font-mono text-xs text-gray-500">amount,currency,bank_code,account_number,note</p><p class="mt-4 text-xs text-gray-500">CSV parsing and provider submission will remain disabled until the batch is validated.</p></div>
      </div>
      <input id="batchFile" type="file" accept=".csv,text/csv" class="hidden">
      <p id="batchStatus" class="mt-5 text-sm" role="status" aria-live="polite"></p>
    </section>
  </div>
</main>
<script>
  const batchButton=document.getElementById('batchUploadButton'), batchFile=document.getElementById('batchFile'), batchStatus=document.getElementById('batchStatus');
  batchButton.addEventListener('click',()=>batchFile.click());
  batchFile.addEventListener('change',()=>{const f=batchFile.files?.[0]; if(f) batchStatus.textContent=`Selected ${f.name}. Validation will run before submission.`;});
</script>
<?php require_once __DIR__.'/includes/bottom_nav.php';require_once __DIR__.'/includes/footer.php'; ?>
