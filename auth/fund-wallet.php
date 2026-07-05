<?php
session_start();

require_once '../config/db.php';

// ---------------------------------------------
// AUTH CHECK
// ---------------------------------------------
if (!isset($_SESSION['user_id'])) {
    $_SESSION['auth_error'] = "Please login to continue to wallet funding.";
    $_SESSION['redirect_after_auth'] = $_SERVER['REQUEST_URI'];

    header("Location: ../auth");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// ---------------------------------------------
// ERROR REPORTING (DEV ONLY)
// ---------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ---------------------------------------------
// DATABASE
// ---------------------------------------------
try {
    $pdo = (new Database())->connect();
} catch (Exception $e) {
    die("Database connection failed.");
}

// ---------------------------------------------
// GET FUNDING AMOUNT
// ---------------------------------------------
// Fallback setup to catch it via POST form execution or direct GET parameters
$funding_amount = 0.00;
if (isset($_POST['amount'])) {
    $funding_amount = (float)$_POST['amount'];
} elseif (isset($_GET['amount'])) {
    $funding_amount = (float)$_GET['amount'];
}

// ---------------------------------------------
// USER INFORMATION
// ---------------------------------------------
$stmt = $pdo->prepare("
    SELECT country FROM users WHERE id=? LIMIT 1
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_country = trim($user['country'] ?? '');

// ---------------------------------------------
// REGION SETTINGS
// ---------------------------------------------
$stmt = $pdo->prepare("
    SELECT currency, exchange_rates FROM region_settings WHERE country=? LIMIT 1
");
$stmt->execute([$user_country]);
$region = $stmt->fetch(PDO::FETCH_ASSOC);

$localCurrency = $region['currency'] ?? 'USD';
$localRate     = (float)($region['exchange_rates'] ?? 1);

// ---------------------------------------------
// CURRENCY SYMBOLS
// ---------------------------------------------
$symbols = [
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'NGN' => '₦',
    'CAD' => 'C$',
    'AUD' => 'A$',
    'KES' => 'KSh',
    'ZAR' => 'R',
    'GHS' => 'GH₵'
];

// ---------------------------------------------
// CURRENCY TOGGLE LOGIC
// ---------------------------------------------
if (isset($_GET['currency'])) {
    if (strtoupper($_GET['currency']) === 'USD') {
        $_SESSION['selected_currency'] = 'USD';
    } elseif (strtoupper($_GET['currency']) === 'LOCAL') {
        $_SESSION['selected_currency'] = $localCurrency;
    }
}

$displayCurrency = $_SESSION['selected_currency'] ?? $localCurrency;

if ($displayCurrency === 'USD') {
    $displayRate = 1;
} else {
    $displayCurrency = $localCurrency;
    $displayRate     = $localRate;
}

$displaySymbol = $symbols[$displayCurrency] ?? '$';

// Hide conversion logic prompt for United States profiles.
$showCurrencyPrompt = strtolower($user_country) !== 'united states';

// ---------------------------------------------
// FILTERED PAYMENT METHODS (redirect_link = 'no')
// ---------------------------------------------
$stmt = $pdo->prepare("
    SELECT
        payment_id,
        image_path,
        error_msg,
        is_active,
        redirect,
        redirect_link
    FROM payment_methods
    WHERE redirect_link = 'no'
    ORDER BY payment_id ASC
");
$stmt->execute();
$paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------------------------
// SUPPORT CONTACTS
// ---------------------------------------------
$support = ['email' => '', 'telegram' => '', 'whatsapp' => ''];
$stmt = $pdo->query("SELECT email, telegram, whatsapp FROM admins LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if ($admin) {
    $support = $admin;
}

// ---------------------------------------------
// TOTAL CALCULATION
// ---------------------------------------------
$convertedTotal = $funding_amount * $displayRate;
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../inc/head.php"; ?>

<body class="bg-gradient-to-br from-slate-100 via-white to-slate-200 min-h-screen">

<?php include "../inc/header.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function showPaymentError(message){
    Swal.fire({
        icon:'warning',
        title:'Payment Method Unavailable',
        text:message,
        confirmButtonColor:'#2563eb'
    });
}
</script>

<div class="max-w-7xl mx-auto px-5 py-10">

    <div class="rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 text-white p-8 shadow-2xl mb-8">
        <div class="flex flex-col lg:flex-row justify-between gap-8">
            <div>
                <h1 class="text-4xl font-black">Fund Wallet Account</h1>
                <p class="text-blue-100 mt-2">Load your deposit balance safely using traditional manual channels.</p>
            </div>

            <div class="flex gap-4 items-start">
                <div class="relative group">
                    <button class="bg-white text-slate-900 font-bold px-6 py-3 rounded-xl shadow hover:shadow-lg transition">
                        <i class="fas fa-headset mr-2 text-blue-600"></i> Contact Support
                    </button>
                    <div class="hidden group-hover:block absolute right-0 mt-3 bg-white rounded-2xl shadow-xl w-60 overflow-hidden z-50">
                        <a href="https://wa.me/<?php echo urlencode($support['whatsapp']); ?>" target="_blank" class="flex items-center gap-3 px-5 py-4 hover:bg-green-50 text-slate-800">
                            <i class="fab fa-whatsapp text-green-600"></i> WhatsApp
                        </a>
                        <a href="https://t.me/<?php echo urlencode($support['telegram']); ?>" target="_blank" class="flex items-center gap-3 px-5 py-4 hover:bg-sky-50 text-slate-800">
                            <i class="fab fa-telegram-plane text-sky-600"></i> Telegram
                        </a>
                        <a href="mailto:<?php echo htmlspecialchars($support['email']); ?>" class="flex items-center gap-3 px-5 py-4 hover:bg-blue-50 text-slate-800">
                            <i class="fas fa-envelope text-blue-600"></i> Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7 space-y-6">

            <?php if($showCurrencyPrompt): ?>
            <div class="rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-6">
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h3 class="font-bold text-slate-900">
                            <?php if ($displayCurrency === 'USD'): ?>
                                Viewing metrics in USD ($)
                            <?php else: ?>
                                Fund using USD valuation alternatives?
                            <?php endif; ?>
                        </h3>
                        <p class="text-sm text-slate-500">
                            Your balance is currently tracking in <strong><?php echo $displayCurrency; ?></strong>.
                        </p>
                    </div>
                    <?php if ($displayCurrency === 'USD'): ?>
                        <a href="?amount=<?= $funding_amount ?>&currency=local" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-3 font-semibold transition">
                            Switch to <?php echo $localCurrency; ?>
                        </a>
                    <?php else: ?>
                        <a href="?amount=<?= $funding_amount ?>&currency=USD" class="bg-orange-500 hover:bg-orange-600 text-white rounded-xl px-6 py-3 font-semibold transition">
                            Switch to USD
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl p-6 shadow">
                <div class="grid md:grid-cols-3 gap-5 text-center">
                    <div>
                        <i class="fas fa-lock text-3xl text-green-500 mb-3"></i>
                        <div class="font-bold text-slate-800">SSL Secure Link</div>
                    </div>
                    <div>
                        <i class="fas fa-shield-alt text-3xl text-blue-500 mb-3"></i>
                        <div class="font-bold text-slate-800">Audited Routing</div>
                    </div>
                    <div>
                        <i class="fas fa-bolt text-3xl text-yellow-500 mb-3"></i>
                        <div class="font-bold text-slate-800">Manual Verification</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-2xl font-black mb-2 text-slate-900">Choose Funding Channel</h2>
                <p class="text-slate-500 mb-8">Select your designated infrastructure point to load balance parameters.</p>

                <?php if(empty($paymentMethods)): ?>
                    <div class="text-center py-8 text-slate-500 bg-slate-50 rounded-2xl border border-dashed">
                        No direct/manual funding options available right now.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <?php foreach($paymentMethods as $method): ?>
                            <?php if($method['is_active'] == 'yes'): ?>
                                <?php 
                                // Directing to system execution logic pipeline
                                $targetUrl = "secure-payment-gateway?payment_id=" . $method['payment_id'] . "&amount=" . $funding_amount . "&currency=" . urlencode($displayCurrency);
                                ?>
                                <a href="<?php echo htmlspecialchars($targetUrl); ?>" class="group rounded-2xl border border-slate-200 bg-white hover:border-blue-600 hover:shadow-xl transition p-6">
                                    <img src="../uploads/payment-methods/<?php echo htmlspecialchars($method['image_path']); ?>" class="mx-auto h-20 object-contain group-hover:scale-105 transition">
                                </a>
                            <?php else: ?>
                                <button type="button" onclick="showPaymentError('<?php echo htmlspecialchars(addslashes($method['error_msg'])); ?>')" class="rounded-2xl border bg-gray-50 opacity-60 cursor-not-allowed p-6 w-full">
                                    <img src="../uploads/payment-methods/<?php echo htmlspecialchars($method['image_path']); ?>" class="mx-auto h-20 object-contain">
                                </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 text-white shadow-2xl p-8 sticky top-6">
                <h2 class="text-2xl font-black mb-6">Funding Balance Metric</h2>
                
                <form method="GET" action="" class="space-y-4 mb-6">
                    <input type="hidden" name="currency" value="<?php echo htmlspecialchars($displayCurrency === $localCurrency ? 'local' : 'USD'); ?>">
                    
                    <label class="block text-sm font-medium text-slate-300">Adjust Deposit Amount:</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold">
                            <?php echo $displaySymbol; ?>
                        </div>
                        <input type="number" name="amount" step="0.01" min="1" value="<?php echo htmlspecialchars($funding_amount > 0 ? $funding_amount : ''); ?>" placeholder="0.00" class="block w-full pl-10 pr-12 py-3 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-white font-bold placeholder-white/30 text-lg">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl transition text-sm">
                        Update Summary Rates
                    </button>
                </form>

                <div class="space-y-5 border-t border-white/10 pt-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-white/70">Raw Calculated Principal Value</div>
                            <div class="text-xs text-slate-400">Assessed Base Rate</div>
                        </div>
                        <div class="font-bold text-lg">
                            <?php echo $displaySymbol . number_format($funding_amount * $displayRate, 2); ?>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/20 mt-8 pt-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-white/70">Total Creditable Funds</div>
                            <div class="text-4xl font-black mt-2">
                                <?php echo $displaySymbol . number_format($convertedTotal, 2); ?>
                            </div>
                        </div>
                        <i class="fas fa-wallet text-5xl text-blue-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../inc/footer.php"; ?>

</body>
</html>
