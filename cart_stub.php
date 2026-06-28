<?php
// cart_stub.php
require_once 'db.php';
$mode = isset($_REQUEST['booking_mode']) ? $_REQUEST['booking_mode'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<?php include "inc/head.php"; ?>
<body class="bg-gray-50 text-gray-900">
<?php include "inc/navbar1.php"; ?>
<?php include "inc/navbar2.php"; ?>

<div class="max-w-md mx-auto my-16 bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center">
    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-2xl mb-4">
        <i class="fas fa-shopping-basket"></i>
    </div>
    <h3 class="text-2xl font-black text-gray-900 mb-2">Cart Selection Received</h3>
    <p class="text-sm text-gray-500 mb-6">Ticketing items processed. Session selection checkout variables configured cleanly.</p>
    
    <div class="bg-gray-50 rounded-xl p-4 text-left border border-gray-100 font-mono text-xs mb-6 space-y-2">
        <p><strong>System Action Mode:</strong> Add to Order Queue</p>
        <p><strong>Selection Context Scheme:</strong> <?= htmlspecialchars($mode); ?></p>
        <p><strong>Timestamp Array:</strong> <?= date('Y-m-d H:i:s'); ?></p>
    </div>
    
    <a href="index.php" class="inline-block bg-[#024DDF] hover:bg-blue-800 text-white text-xs font-bold uppercase tracking-wider py-3 px-6 rounded-xl transition-colors shadow">
        Return to Landing Framework
    </a>
</div>

<?php include "inc/footer.php"; ?>
