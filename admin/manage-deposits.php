<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/inc/header.php';

$message = '';
$error   = '';

/* --------------------------------------------------
    FETCH ALL DEPOSITS WITH USER & METHOD DETAILS
-------------------------------------------------- */
try {
    $stmt = $pdo->query("
        SELECT 
            d.*, 
            u.id as user_uid, 
            u.country,
            pm.image_path as method_logo
        FROM deposits d
        LEFT JOIN users u ON d.user_id = u.id
        LEFT JOIN payment_methods pm ON d.payment_id = pm.payment_id
        ORDER BY d.deposit_id DESC
    ");
    $deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $deposits = [];
}

/* --------------------------------------------------
    HANDLE ACTIONS
-------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {

    $action = $_POST['action'] ?? '';

    try {

        /* ---------------- UPDATE STATUS (APPROVE/DECLINE) ---------------- */
        if ($action === 'update_status') {
            $id = (int)($_POST['id'] ?? 0);
            $new_status = $_POST['status'] ?? '';

            if ($id <= 0 || !in_array($new_status, ['approved', 'declined', 'pending'])) {
                throw new Exception("Invalid parameters provided.");
            }

            $stmt = $pdo->prepare("UPDATE deposits SET status = ? WHERE deposit_id = ?");
            $stmt->execute([$new_status, $id]);

            $_SESSION['success'] = "Deposit payment reference status changed to " . ucfirst($new_status) . ".";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        /* ---------------- DELETE DEPOSIT RECORD ---------------- */
        if ($action === 'delete') {

            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception("Invalid deposit entry ID.");
            }

            $stmt = $pdo->prepare("DELETE FROM deposits WHERE deposit_id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = "Deposit reference history log deleted successfully.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<style>
/* CSS styles for the Order Details Modal Context */
.order-map-link {
    color: #60a5fa; 
    text-decoration: underline; 
    cursor: pointer;
    font-weight: bold;
}
.order-map-link:hover {
    color: #93c5fd;
}

.details-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}
.details-modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
.details-modal-card {
    background: #1f2937;
    border: 1px solid #374151;
    border-radius: 12px;
    width: 100%;
    max-width: 550px;
    padding: 1.5rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
    color: #f3f4f6;
    position: relative;
    transform: translateY(-20px);
    transition: transform 0.3s ease;
}
.details-modal-overlay.active .details-modal-card {
    transform: translateY(0);
}
.modal-close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    border: none;
    color: #9ca3af;
    font-size: 20px;
    cursor: pointer;
}
.modal-close-btn:hover {
    color: #ffffff;
}
.modal-title {
    margin-top: 0;
    border-bottom: 1px solid #374151;
    padding-bottom: 10px;
    font-size: 1.25rem;
    color: #ffffff;
}
.modal-section {
    margin: 15px 0;
}
.modal-section h4 {
    margin: 0 0 8px 0;
    color: #9ca3af;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 1px;
}
.info-grid {
    background: #111827;
    padding: 12px;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.6;
}
.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
}
.info-row:last-child {
    margin-bottom: 0;
}
.info-label {
    color: #9ca3af;
}
.info-value {
    font-weight: 600;
}
</style>

<main>

<h1 style="text-align:center; margin:2rem 0;">Manage Client Deposits</h1>

<?php if (!empty($_SESSION['success'])): ?>
<div style="background:#238636;color:#fff;padding:1rem;border-radius:8px;text-align:center;max-width:1100px;margin:1rem auto;">
    <?= htmlspecialchars($_SESSION['success']) ?>
</div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
<div style="background:#f85149;color:#fff;padding:1rem;border-radius:8px;text-align:center;max-width:1100px;margin:1rem auto;">
    <?= htmlspecialchars($_SESSION['error']) ?>
</div>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div style="max-width:1100px;margin:0 auto 1.5rem auto;display:flex;gap:15px;justify-content:center;flex-wrap:wrap;">
    <span style="background:#1f2937;padding:8px 16px;border-radius:20px;font-size:13px;border:1px solid #374151;">
        Total Ledger Records: <strong><?= count($deposits) ?></strong>
    </span>
</div>

<div style="max-width:1140px;margin:0 auto;overflow-x:auto;padding:0 10px;">

<table style="width:100%;border-collapse:collapse;background:var(--card);border:1px solid var(--border);border-radius:10px;min-width:1000px;font-size:14px;">

<thead>
<tr style="text-align:left;background:#111827;">
    <th style="padding:12px;">ID</th>
    <th style="padding:12px;">Client ID</th>
    <th style="padding:12px;">Order Maps</th>
    <th style="padding:12px;">Amount Due</th>
    <th style="padding:12px;">Gateway Type</th>
    <th style="padding:12px;">Submitted Payload</th>
    <th style="padding:12px;">Proof File</th>
    <th style="padding:12px;">Status</th>
    <th style="padding:12px;text-align:right;">Actions</th>
</tr>
</thead>

<tbody>

<?php if (empty($deposits)): ?>
<tr>
    <td colspan="9" style="padding:2rem;text-align:center;color:#888;">No deposit transactions found in database logs.</td>
</tr>
<?php endif; ?>

<?php foreach ($deposits as $deposit): ?>
<tr style="border-top:1px solid var(--border); vertical-align: middle;">

    <td style="padding:12px;font-family:monospace;font-weight:bold;">
        #DEP-<?= $deposit['deposit_id'] ?>
    </td>

    <td style="padding:12px;">
        <span style="display:block;font-weight:600;">UID: <?= htmlspecialchars($deposit['user_id'] ?? 'N/A') ?></span>
        <small style="color:#aaa;font-size:11px;text-transform:uppercase;"><?= htmlspecialchars($deposit['country'] ?? 'Unknown Reg') ?></small>
    </td>

    <td style="padding:12px;font-family:monospace;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="Click to view full detail mappings">
        <?php if (!empty($deposit['order_ids'])): ?>
            <span class="order-map-link class-trigger-modal" data-order-id="<?= htmlspecialchars($deposit['order_ids']) ?>">
                <?= htmlspecialchars($deposit['order_ids']) ?>
            </span>
        <?php else: ?>
            <span style="color:#666;">N/A</span>
        <?php endif; ?>
    </td>

    <td style="padding:12px;font-weight:bold;color:#34d399;">
        <?= htmlspecialchars($deposit['currency']) ?> <?= number_format($deposit['amount'], 2) ?>
    </td>

    <td style="padding:12px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <?php if (!empty($deposit['method_logo'])): ?>
                <img src="../uploads/payment-methods/<?= htmlspecialchars($deposit['method_logo']) ?>" 
                     style="width:28px;height:28px;object-fit:contain;background:#fff;padding:2px;border-radius:4px;">
            <?php endif; ?>
            <span style="font-size:12px;text-transform:uppercase;background:#374151;padding:2px 6px;border-radius:4px;font-weight:bold;">
                <?= str_replace('_', ' ', $deposit['payment_type']) ?>
            </span>
        </div>
    </td>

    <td style="padding:12px;font-size:12px;max-width:220px;word-break:break-all;">
        <?php 
            $payload = json_decode($deposit['submitted_details'], true);
            if(is_array($payload)){
                foreach($payload as $key => $val){
                    if(!empty($val)){
                        echo "<strong>".htmlspecialchars(str_replace('_',' ',$key)).":</strong> ".htmlspecialchars($val)."<br>";
                    }
                }
            } else {
                echo '<span style="color:#666;">Raw: '.htmlspecialchars($deposit['submitted_details']).'</span>';
            }
        ?>
    </td>

    <td style="padding:12px;">
        <?php if (!empty($deposit['proof_file'])): ?>
            <a href="../<?= htmlspecialchars($deposit['proof_file']) ?>" target="_blank" 
               style="background:#1f2937;color:#60a5fa;border:1px solid #3b82f6;padding:4px 8px;border-radius:6px;text-decoration:none;font-size:11px;font-weight:bold;display:inline-flex;align-items:center;gap:3px;">
                <i class="fas fa-file-invoice"></i> View Proof
            </a>
        <?php else: ?>
            <span style="color:#f85149;font-size:12px;">Missing Document</span>
        <?php endif; ?>
    </td>

    <td style="padding:12px;">
        <?php 
            $status = $deposit['status'];
            $bg = '#1f2937'; $co = '#9ca3af';
            if($status === 'approved') { $bg = '#14532d'; $co = '#4ade80'; }
            if($status === 'declined') { $bg = '#7f1d1d'; $co = '#f87171'; }
            if($status === 'pending')  { $bg = '#7c2d12'; $co = '#fb923c'; }
        ?>
        <span style="background:<?= $bg ?>;color:<?= $co ?>;padding:4px 8px;border-radius:6px;font-weight:bold;text-transform:uppercase;font-size:11px;letter-spacing:0.5px;">
            <?= $status ?>
        </span>
    </td>

    <td style="padding:12px;white-space:nowrap;text-align:right;">

        <?php if($status === 'pending'): ?>
            <form method="POST" style="display:inline-block;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="status" value="approved">
                <input type="hidden" name="id" value="<?= $deposit['deposit_id'] ?>">
                <button class="btn green" style="padding:5px 8px;font-size:12px;font-weight:bold;cursor:pointer;">
                    Approve
                </button>
            </form>

            <form method="POST" style="display:inline-block;margin-left:3px;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="status" value="declined">
                <input type="hidden" name="id" value="<?= $deposit['deposit_id'] ?>">
                <button class="btn red" style="padding:5px 8px;font-size:12px;font-weight:bold;background:#b91c1c;cursor:pointer;">
                    Decline
                </button>
            </form>
        <?php else: ?>
            <form method="POST" style="display:inline-block;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="status" value="pending">
                <input type="hidden" name="id" value="<?= $deposit['deposit_id'] ?>">
                <button class="btn" style="padding:4px 8px;font-size:11px;background:#4b5563;color:#e5e7eb;cursor:pointer;">
                    Reset to Pending
                </button>
            </form>
        <?php endif; ?>

        <form method="POST"
              onsubmit="return confirm('CRITICAL WARN: Are you sure you want to permanently purge this financial tracking ledger record? This step cannot be reversed.');"
              style="display:inline-block;margin-left:6px;">

            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $deposit['deposit_id'] ?>">

            <button class="btn red" style="padding:5px 8px;font-size:12px;background:#310000;border:1px solid #f85149;color:#f85149;cursor:pointer;">
                <i class="fas fa-trash-alt"></i>
            </button>

        </form>

    </td>

</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

</main>

<div id="orderMapModal" class="details-modal-overlay">
    <div class="details-modal-card">
        <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
        <h3 class="modal-title">Order Mappings Reference</h3>
        
        <div class="modal-section">
            <h4>Client Base Information</h4>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value" id="modalUserFullName">Loading...</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email Address:</span>
                    <span class="info-value" id="modalUserEmail">Loading...</span>
                </div>
            </div>
        </div>

        <div class="modal-section">
            <h4>Purchased Ticket Details</h4>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Order Link ID:</span>
                    <span class="info-value" id="modalTicketId">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Event Target:</span>
                    <span class="info-value" id="modalEventTitle">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tier Type:</span>
                    <span class="info-value" id="modalTicketType">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Quantity:</span>
                    <span class="info-value" id="modalTicketQty">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Volume Cost:</span>
                    <span class="info-value" style="color:#34d399;" id="modalTicketPrice">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Workflow Status:</span>
                    <span class="info-value" id="modalTicketStatus">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Logged Timestamp:</span>
                    <span class="info-value" id="modalTicketDate">-</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('orderMapModal');
    const closeBtn = document.getElementById('closeModalBtn');
    
    // Elements to update dynamic content
    const nameEl = document.getElementById('modalUserFullName');
    const emailEl = document.getElementById('modalUserEmail');
    const idEl = document.getElementById('modalTicketId');
    const eventEl = document.getElementById('modalEventTitle');
    const typeEl = document.getElementById('modalTicketType');
    const qtyEl = document.getElementById('modalTicketQty');
    const priceEl = document.getElementById('modalTicketPrice');
    const statusEl = document.getElementById('modalTicketStatus');
    const dateEl = document.getElementById('modalTicketDate');

    // Attach click listeners to all Order Map links
    document.querySelectorAll('.class-trigger-modal').forEach(link => {
        link.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            
            // Set loading state visuals before calling API
            nameEl.textContent = 'Fetching mapping details...';
            emailEl.textContent = 'Fetching mapping details...';
            idEl.textContent = '#'+orderId;
            eventEl.textContent = '-';
            typeEl.textContent = '-';
            qtyEl.textContent = '-';
            priceEl.textContent = '-';
            statusEl.textContent = '-';
            dateEl.textContent = '-';
            
            // Activate the Modal window opacity wrapper
            modal.classList.add('active');
            
            // Execute non-blocking backend payload fetch
            fetch(`get_order_details.php?order_id=${encodeURIComponent(orderId)}`)
                .then(response => response.json())
                .then(res => {
                    if(res.success) {
                        const d = res.data;
                        nameEl.textContent = d.full_name ? d.full_name : 'N/A';
                        emailEl.textContent = d.email ? d.email : 'N/A';
                        idEl.textContent = '# ' + d.ticket_id;
                        eventEl.textContent = d.event_title ? d.event_title : 'N/A';
                        typeEl.textContent = d.ticket_type ? d.ticket_type.toUpperCase() : 'N/A';
                        qtyEl.textContent = d.quantity;
                        priceEl.textContent = '$' + parseFloat(d.total_price).toFixed(2);
                        statusEl.textContent = d.ticket_status ? d.ticket_status.toUpperCase() : 'PENDING';
                        dateEl.textContent = d.purchase_date;
                    } else {
                        nameEl.textContent = 'Error occurred';
                        emailEl.textContent = res.message;
                    }
                })
                .catch(err => {
                    nameEl.textContent = 'Failed to fetch tracking data.';
                    emailEl.textContent = err.message;
                });
        });
    });

    // Close handler event bindings
    closeBtn.addEventListener('click', () => modal.classList.remove('active'));
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
