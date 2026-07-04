<?php
session_start();
require_once '../config/db.php';

// 1. Security Check: Ensure admin is logged in (Adjust to your auth system)
if (!isset($_SESSION['user_id'])) {
    echo '<div class="p-4 text-red-600 bg-red-50 rounded-xl">Unauthorized access. Please log in.</div>';
    exit;
}

// 2. Validate incoming Deposit ID from the AJAX request
$deposit_id = isset($_GET['deposit_id']) ? (int)$_GET['deposit_id'] : 0;
if ($deposit_id <= 0) {
    echo '<div class="p-4 text-red-600 bg-red-50 rounded-xl">Invalid deposit record request.</div>';
    exit;
}

try {
    $pdo = (new Database())->connect();
} catch (Exception $e) {
    echo '<div class="p-4 text-red-600 bg-red-50 rounded-xl">Database connection failed.</div>';
    exit;
}

// 3. Fetch the deposit record and join the associated user details
$stmt = $pdo->prepare("
    SELECT d.*, u.full_name, u.email, u.country 
    FROM deposits d
    INNER JOIN users u ON d.user_id = u.id
    WHERE d.deposit_id = ? 
    LIMIT 1
");
$stmt->execute([$deposit_id]);
$deposit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$deposit) {
    echo '<div class="p-4 text-red-600 bg-red-50 rounded-xl">Deposit record not found in system logs.</div>';
    exit;
}

// 4. Parse the order_ids string and build the corrected relational database query
$order_ids_array = array_map('intval', explode(',', $deposit['order_ids']));
$tickets = [];

if (!empty($order_ids_array)) {
    $placeholders = implode(',', array_fill(0, count($order_ids_array), '?'));

    // CORRECTED PATHWAY: Route through 'orders' table first to bridge your deposit string data
    $stmt = $pdo->prepare("
        SELECT 
            t.ticket_id,
            t.ticket_name,
            t.price,
            c.title AS concert_title,
            a.name AS artist_name,
            o.order_id
        FROM orders o
        INNER JOIN tickets t ON o.ticket_id = t.ticket_id
        LEFT JOIN concerts c ON t.concert_id = c.concert_id
        LEFT JOIN artists a ON c.artist_id = a.artist_id
        WHERE o.order_id IN ($placeholders)
    ");
    $stmt->execute($order_ids_array);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 5. Build dynamic currency formatting parameters saved during transaction routing
$currencySymbols = ['USD'=>'$', 'EUR'=>'€', 'GBP'=>'£', 'NGN'=>'₦', 'CAD'=>'C$', 'AUD'=>'A$', 'KES'=>'KSh', 'ZAR'=>'R', 'GHS'=>'GH₵'];
$symbol = $currencySymbols[$deposit['currency']] ?? '$';
?>

<div class="space-y-6">
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
        <h3 class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-3">Customer Profile</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-xs block text-slate-500 font-medium">Full Name</span>
                <span class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($deposit['full_name']); ?></span>
            </div>
            <div>
                <span class="text-xs block text-slate-500 font-medium">Email Address</span>
                <span class="text-sm font-mono text-blue-600 font-semibold select-all"><?php echo htmlspecialchars($deposit['email']); ?></span>
            </div>
            <div>
                <span class="text-xs block text-slate-500 font-medium">Country Base</span>
                <span class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($deposit['country']); ?></span>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-3">Purchased Item Details</h3>
        
        <?php if (empty($tickets)): ?>
            <div class="flex flex-col items-center justify-center p-8 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p class="text-sm font-bold">No valid ticket records matched to these criteria.</p>
                <small class="text-xs text-amber-600 mt-1">Verification failed mapping order strings (<?php echo htmlspecialchars($deposit['order_ids']); ?>).</small>
            </div>
        <?php else: ?>
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm bg-white">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 text-xs font-bold uppercase border-b border-slate-200">
                            <th class="p-4">Order ID</th>
                            <th class="p-4">Ticket Type</th>
                            <th class="p-4">Concert / Performance Event</th>
                            <th class="p-4 text-right">Price Tag</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <?php foreach ($tickets as $ticket): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-mono font-bold text-xs text-slate-500">#<?php echo $ticket['order_id']; ?></td>
                                <td class="p-4 font-bold text-slate-900"><?php echo htmlspecialchars($ticket['ticket_name']); ?></td>
                                <td class="p-4">
                                    <div class="font-medium text-slate-800"><?php echo htmlspecialchars($ticket['concert_title'] ?? 'N/A'); ?></div>
                                    <div class="text-xs text-slate-400 font-medium"><?php echo htmlspecialchars($ticket['artist_name'] ?? 'Unknown Artist'); ?></div>
                                </td>
                                <td class="p-4 text-right font-black text-slate-900">
                                    <?php echo $symbol . number_format($ticket['price'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl flex justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wide text-blue-700">Total Settlement Value:</span>
                <span class="text-lg font-black text-blue-900">
                    <?php echo $symbol . number_format($deposit['amount'], 2) . ' ' . htmlspecialchars($deposit['currency']); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>
