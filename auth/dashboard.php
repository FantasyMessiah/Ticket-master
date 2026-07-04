<?php
session_start();

ini_set('display_errors', 0); // Production safe
error_reporting(E_ALL);

require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['auth_error'] = "Please log in to access your dashboard.";
    $_SESSION['redirect_after_auth'] = $_SERVER['REQUEST_URI'];
    header("Location: ../auth.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$pdo = null;

try {
    if (class_exists('Database')) {
        $dbInstance = new Database();
        $pdo = $dbInstance->connect(); 
    }
} catch (Exception $e) {
    // Structural boundary fallback
}

$success_message = "";
$error_message = "";

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email_address = trim($_POST['email']);
    $phone_number = trim($_POST['phone']);

    if (!empty($full_name) && !empty($email_address)) {
        try {
            if ($pdo !== null) {
                $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
                $update_stmt->execute([$full_name, $email_address, $phone_number, $user_id]);
            }
            $success_message = "Your profile changes have been saved successfully.";
        } catch (Exception $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Required fields cannot be left blank.";
    }
}

// Clear setup for database arrays
$user_profile = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'balance' => 0.00
];

$admin_messages = [];
$recent_orders = [];
$transaction_history = [];
$recently_viewed_shows = [];
$admin_tickets = []; 

if ($pdo !== null) {
    try {
        // 1. Load user profile information including dynamic balance ledger metrics
        $user_stmt = $pdo->prepare("SELECT full_name, email, phone, balance FROM users WHERE id = ? LIMIT 1");
        $user_stmt->execute([$user_id]);
        $fetched_user = $user_stmt->fetch();
        if ($fetched_user) {
            $user_profile = [
                'name'    => $fetched_user['full_name'] ?? '',
                'email'   => $fetched_user['email'] ?? '',
                'phone'   => $fetched_user['phone'] ?? '',
                'balance' => (float) ($fetched_user['balance'] ?? 0.00)
            ];
        }

        // 2. Load system notices and announcements
        $msg_stmt = $pdo->prepare("SELECT message, created_at FROM users WHERE id = ? AND message IS NOT NULL AND message != '' ORDER BY id DESC");
        $msg_stmt->execute([$user_id]);
        $raw_messages = $msg_stmt->fetchAll();
        foreach ($raw_messages as $m_row) {
            $admin_messages[] = [
                'title'      => 'Security Notice',
                'content'    => $m_row['message'],
                'created_at' => $m_row['created_at'] ?? date('Y-m-d H:i:s')
            ];
        }

        // 3. Load user ticket orders as download pass elements
        $order_stmt = $pdo->prepare("
            SELECT 
                o.order_id, 
                o.status AS order_status, 
                o.created_at AS purchase_date,
                a.artist_name AS show_title,
                c.title AS concert_title,
                t.ticket_name,
                t.section_name,
                t.row_name,
                t.seat_name
            FROM orders o
            INNER JOIN tickets t ON o.ticket_id = t.ticket_id
            INNER JOIN concerts c ON t.concert_id = c.concert_id
            INNER JOIN artists a ON c.artist_id = a.artist_id
            WHERE o.user_id = ?
            ORDER BY o.order_id DESC LIMIT 10
        ");
        $order_stmt->execute([$user_id]);
        $raw_orders = $order_stmt->fetchAll();
        foreach ($raw_orders as $or) {
            $seat_details = trim(sprintf(
                "%s (%s, %s, %s)", 
                $or['ticket_name'], 
                $or['section_name'], 
                $or['row_name'], 
                $or['seat_name']
            ));

            $recent_orders[] = [
                'id'     => 'TM-' . $or['order_id'],
                'title'  => $or['show_title'],
                'venue'  => $or['concert_title'],
                'seats'  => !empty($seat_details) ? $seat_details : 'General Admission',
                'status' => $or['order_status'], 
                'date'   => date('M d, Y', strtotime($or['purchase_date']))
            ];

            // Structural assignment linking valid active purchases directly into downloadable assets block
            if (strtolower($or['order_status']) === 'confirmed' || strtolower($or['order_status']) === 'completed' || strtolower($or['order_status']) === 'success') {
                $admin_tickets[] = [
                    'file_path' => '#',
                    'description' => $or['show_title'] . " - " . (!empty($seat_details) ? $seat_details : 'General Admission Entry Pass') . ". Verified Digital Voucher Pass."
                ];
            }
        }
        
        // 4. Load billing and deposit logs
        $tx_stmt = $pdo->prepare("
            SELECT 
                d.deposit_id, 
                d.created_at, 
                d.amount, 
                d.status,
                p.image_path
            FROM deposits d
            LEFT JOIN payment_methods p ON d.payment_id = p.payment_id
            WHERE d.user_id = ?
            ORDER BY d.deposit_id DESC LIMIT 15
        ");
        $tx_stmt->execute([$user_id]);
        $raw_txs = $tx_stmt->fetchAll();
        foreach ($raw_txs as $tx) {
            $transaction_history[] = [
                'ref'      => 'DEP-' . $tx['deposit_id'],
                'date'     => date('Y-m-d', strtotime($tx['created_at'])),
                'method'   => !empty($tx['image_path']) ? '../uploads/payment-methods/' . $tx['image_path'] : 'Standard Gateway',
                'amount'   => $tx['amount'],
                'currency' => 'USD',
                'status'   => ($tx['status'] === 'confirmed' || $tx['status'] === 'completed' || $tx['status'] === 'Successful') ? 'Successful' : 'Processing'
            ];
        }

        // 5. Dynamic loading for Trending Shows
        $trending_stmt = $pdo->prepare("
            SELECT 
                c.concert_id AS id,
                c.title,
                c.location,
                a.artist_name AS artist
            FROM concerts c
            INNER JOIN artists a ON c.artist_id = a.artist_id
            WHERE c.index_type = 'trending'
        ");
        $trending_stmt->execute();
        $raw_trending = $trending_stmt->fetchAll();
        
        if (!empty($raw_trending)) {
            shuffle($raw_trending);
            foreach ($raw_trending as $row) {
                $recently_viewed_shows[] = [
                    'id'       => $row['id'],
                    'artist'   => $row['artist'],
                    'title'    => $row['title'],
                    'location' => $row['location'] ?? 'Venue TBA'
                ];
            }
        }

    } catch (Exception $e) {
        $error_message = "Failed to load dashboard data: " . $e->getMessage();
    }
}

// Logic validation for completeness parameter monitoring
$profile_incomplete = (empty($user_profile['name']) || empty($user_profile['email']) || empty($user_profile['phone']));
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../inc/head.php"; ?>
<?php include "../inc/navbar.php"; ?>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <?php include "../inc/header.php"; ?>

    <div id="__next" class="min-h-screen flex flex-col justify-between">

        <main class="max-w-7xl mx-auto w-full px-4 md:px-8 py-10 flex-1">
            
            <?php if ($profile_incomplete): ?>
                <div class="mb-8 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 p-5 rounded-r-2xl shadow-sm flex items-start gap-4 transition-all">
                    <div class="p-2 rounded-xl bg-amber-100 text-amber-700">
                        <i class="fas fa-user-edit text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-amber-900 uppercase tracking-wide">Complete Your Account Profile</h4>
                        <p class="text-xs text-amber-700 mt-1 font-medium leading-relaxed">Important security systems require complete verified info. Please update your full name, email address, and working telephone details inside the settings panel below to restore layout capabilities fully.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs p-4 rounded-xl mb-6 shadow-sm">
                    <i class="fas fa-check-circle mr-2 text-emerald-600"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-800 font-bold text-xs p-4 rounded-xl mb-6 shadow-sm">
                    <i class="fas fa-exclamation-triangle mr-2 text-rose-600"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <div class="lg:col-span-8 space-y-8">
                    
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div class="border-b border-gray-100 pb-4 flex justify-between items-center">
                            <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 flex items-center gap-2.5">
                                <i class="fas fa-cloud-download-alt text-[#024DDF] text-base"></i> Download Purchased Tickets
                            </h3>
                            <span class="text-[10px] font-mono font-bold bg-blue-50 text-[#024DDF] px-2 py-0.5 rounded-full">
                                <?php echo count($admin_tickets); ?> Live Passes
                            </span>
                        </div>

                        <?php if (empty($admin_tickets)): ?>
                            <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-10 text-center">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <i class="fas fa-qrcode text-lg"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">No active downloadable gate passes found.</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($admin_tickets as $ticket): ?>
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col justify-between hover:border-blue-200 transition-all">
                                        <div class="p-5 space-y-3 flex-1">
                                            <div class="flex justify-between items-start">
                                                <span class="text-[9px] font-mono font-black bg-slate-900 text-white px-2 py-0.5 rounded tracking-widest uppercase shadow-sm">DIGITAL PASS</span>
                                                <i class="fas fa-ticket-alt text-gray-300 text-base"></i>
                                            </div>
                                            <p class="text-xs text-gray-700 leading-relaxed font-semibold">
                                                <?php echo htmlspecialchars($ticket['description']); ?>
                                            </p>
                                        </div>
                                        <div class="p-4 bg-gray-100/60 border-t border-gray-200 flex justify-end">
                                            <a href="<?php echo htmlspecialchars($ticket['file_path']); ?>" class="w-full text-center text-[10px] font-black bg-white border border-gray-300 text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-50 uppercase tracking-wider flex items-center justify-center gap-2 shadow-xs transition-all">
                                                <i class="fas fa-arrow-down text-blue-600"></i> Download Pass File
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-slate-900 text-white border border-slate-800 rounded-2xl p-6 shadow-md space-y-4">
                        <h4 class="text-xs font-black uppercase tracking-widest text-blue-400 flex items-center gap-2 border-b border-slate-800 pb-3">
                            <i class="fas fa-satellite-dish animate-pulse"></i> Messages & Security Notices
                        </h4>
                        <?php if (empty($admin_messages)): ?>
                            <div class="p-6 text-center text-slate-500 font-bold text-xs uppercase tracking-wide">
                                No new notification metrics recorded.
                            </div>
                        <?php else: ?>
                            <div class="space-y-4 max-h-[320px] overflow-y-auto pr-1">
                                <?php foreach ($admin_messages as $msg): ?>
                                    <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl space-y-2">
                                        <span class="text-[9px] text-gray-500 font-mono block"><?php echo htmlspecialchars($msg['created_at']); ?></span>
                                        <h5 class="text-xs font-black tracking-tight text-gray-200"><?php echo htmlspecialchars($msg['title']); ?></h5>
                                        <p class="text-[11px] font-medium text-gray-400 leading-relaxed"><?php echo htmlspecialchars($msg['content']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 flex items-center gap-2 border-b border-gray-100 pb-3">
                            <i class="fas fa-shopping-bag text-[#024DDF]"></i> Recent Purchase Summary
                        </h3>
                        <?php if (empty($recent_orders)): ?>
                            <p class="text-xs font-bold text-gray-400 p-4 uppercase tracking-wide text-center">No orders created yet.</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($recent_orders as $order): ?>
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:border-gray-300 transition-all">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-mono font-black text-gray-400 bg-gray-100 border border-gray-200 px-1.5 py-0.5 rounded"><?php echo htmlspecialchars($order['id']); ?></span>
                                                <?php 
                                                    $status = strtolower($order['status']);
                                                    $badge_cls = ($status === 'confirmed' || $status === 'completed' || $status === 'success') ? 'text-emerald-600 bg-emerald-50' : (($status === 'processing' || $status === 'pending') ? 'text-amber-600 bg-amber-50 animate-pulse' : 'text-blue-600 bg-blue-50');
                                                ?>
                                                <span class="text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wide <?php echo $badge_cls; ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </div>
                                            <h4 class="text-xs font-black text-gray-900 tracking-tight"><?php echo htmlspecialchars($order['title']); ?></h4>
                                            <p class="text-xs text-gray-500 font-medium">
                                                <i class="fas fa-map-marker-alt text-gray-300 mr-1"></i> <?php echo htmlspecialchars($order['venue']); ?> • <span class="font-bold text-gray-600"><?php echo htmlspecialchars($order['seats']); ?></span>
                                            </p>
                                        </div>
                                        <div class="text-left sm:text-right shrink-0 border-t sm:border-t-0 border-gray-100 pt-2 sm:pt-0">
                                            <span class="text-xs font-black text-gray-800 block"><?php echo htmlspecialchars($order['date']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 flex items-center gap-2 border-b border-gray-100 pb-3">
                            <i class="fas fa-receipt text-[#024DDF]"></i> Account Ledger Settlements
                        </h3>
                        <?php if (empty($transaction_history)): ?>
                            <p class="text-xs font-bold text-gray-400 p-4 uppercase tracking-wide text-center">No transactions available.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs font-medium text-gray-600">
                                    <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider text-[10px] font-black border-b border-gray-200">
                                        <tr>
                                            <th class="p-3">Reference ID</th>
                                            <th class="p-3">Date</th>
                                            <th class="p-3">Method</th>
                                            <th class="p-3 text-right">Total Amount</th>
                                            <th class="p-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($transaction_history as $txn): ?>
                                            <tr class="hover:bg-gray-50/60 transition-colors">
                                                <td class="p-3 font-mono font-bold text-gray-900"><?php echo htmlspecialchars($txn['ref']); ?></td>
                                                <td class="p-3 text-gray-500 font-bold"><?php echo htmlspecialchars($txn['date']); ?></td>
                                                <td class="p-3 text-gray-500 font-bold flex items-center gap-2">
                                                    <?php if (strpos($txn['method'], '../uploads/') === 0): ?>
                                                        <img src="<?php echo htmlspecialchars($txn['method']); ?>" alt="Icon" class="h-4 w-auto object-contain rounded border border-gray-200 max-w-[60px]">
                                                    <?php else: ?>
                                                        <span><?php echo htmlspecialchars($txn['method']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-3 text-right font-black text-[#024DDF]">
                                                    <?php echo (($txn['currency'] === 'EUR') ? '€' : (($txn['currency'] === 'GBP') ? '£' : '$')) . number_format($txn['amount'], 2); ?>
                                                </td>
                                                <td class="p-3 text-center">
                                                    <span class="font-black tracking-wide uppercase px-2 py-0.5 rounded text-[10px] <?php echo ($txn['status'] === 'Successful') ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'; ?>">
                                                        <?php echo htmlspecialchars($txn['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-800 flex items-center gap-2 border-b border-gray-100 pb-3">
                            <i class="fas fa-eye text-[#024DDF]"></i> Explore Trending Performances
                        </h3>
                        <?php if (empty($recently_viewed_shows)): ?>
                            <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-6 text-center">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">No production streams available at present.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($recently_viewed_shows as $show): ?>
                                    <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-200 hover:shadow-xs transition-all flex justify-between items-center bg-white">
                                        <div class="min-w-0">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-[#024DDF] block"><?php echo htmlspecialchars($show['artist']); ?></span>
                                            <h4 class="text-xs font-extrabold text-gray-900 truncate mt-0.5"><?php echo htmlspecialchars($show['title']); ?></h4>
                                            <p class="text-[11px] font-medium text-gray-400 mt-0.5 truncate"><i class="fas fa-map-pin mr-1 text-gray-300"></i> <?php echo htmlspecialchars($show['location']); ?></p>
                                        </div>
                                        <a href="../search.php?q=<?php echo urlencode($show['artist']); ?>" class="shrink-0 text-[10px] font-black text-[#024DDF] bg-blue-50 hover:bg-[#024DDF] hover:text-white px-3 py-2 rounded-lg transition-all uppercase tracking-wide ml-4">View Show</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <div class="lg:col-span-4 space-y-6">
                    
                    <div class="bg-gradient-to-br from-[#024DDF] to-blue-800 text-white rounded-2xl p-6 shadow-md border border-blue-700 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 text-blue-700/20 text-8xl pointer-events-none font-black">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="relative z-10 space-y-2">
                            <h4 class="text-[10px] font-black text-blue-200 uppercase tracking-widest flex items-center gap-1.5">
                                <i class="fas fa-shield-alt text-xs"></i> Available Account Balance
                            </h4>
                            <div class="text-3xl font-black font-mono tracking-tight pt-1">
                                $<?php echo number_format($user_profile['balance'], 2); ?>
                            </div>
                            <p class="text-[10px] font-bold text-blue-100/70 uppercase tracking-wider">Dynamic Balance Matrix</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center gap-4 border-b border-gray-100 pb-4 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gray-900 text-white font-black text-sm flex items-center justify-center shadow-xs">
                                <?php echo !empty($user_profile['name']) ? strtoupper(substr($user_profile['name'], 0, 2)) : 'ME'; ?>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900 tracking-tight uppercase">
                                    <?php echo !empty($user_profile['name']) ? htmlspecialchars($user_profile['name']) : 'New Member'; ?>
                                </h3>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wide">Account Settings</p>
                            </div>
                        </div>

                        <form action="dashboard.php" method="POST" class="space-y-4">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wider mb-1.5">Full Name</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user_profile['name']); ?>" placeholder="Enter full name" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold focus:bg-white focus:border-[#024DDF] outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wider mb-1.5">Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user_profile['email']); ?>" placeholder="name@domain.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold focus:bg-white focus:border-[#024DDF] outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wider mb-1.5">Phone Number</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($user_profile['phone']); ?>" placeholder="+1 (555) 000-0000" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold focus:bg-white focus:border-[#024DDF] outline-none transition-all">
                            </div>

                            <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-black text-xs uppercase tracking-widest py-3.5 rounded-xl transition-all shadow-xs">
                                Update Information
                            </button>
                        </form>
                    </div>

                </div>
            </div>
            
        </main>

        <?php include "../inc/footer.php"; ?>
    </div>

    <style>
        body { overflow-x: hidden; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</body>
</html>
