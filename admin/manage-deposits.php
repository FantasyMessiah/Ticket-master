<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/inc/header.php';

$message = '';
$error   = '';

/* --------------------------------------------------
   FETCH ALL DEPOSITS WITH USER & METHOD DETAILS
-------------------------------------------------- */
$deposits = [];
$order_map_data = []; // Structured storage container mapping order IDs to UI Modals

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

    // Collect all order IDs across the current batch to prevent N+1 Query issues
    $all_order_ids = [];
    foreach ($deposits as $deposit) {
        if (!empty($deposit['order_ids'])) {
            // Clean spacing and split comma-separated strings safely
            $ids = array_filter(array_map('intval', explode(',', $deposit['order_ids'])));
            foreach ($ids as $id) {
                $all_order_ids[$id] = $id; 
            }
        }
    }

    // Resolve data lookups if valid target order references exist
    if (!empty($all_order_ids)) {
        $placeholders = implode(',', array_fill(0, count($all_order_ids), '?'));
        
        // Single optimized Master-Join pulling the hierarchical relational details requested
        $lookup_stmt = $pdo->prepare("
            SELECT 
                t.ticket_id, t.concert_id, t.ticket_name, t.section_name, t.row_name, t.price,
                u.id AS user_id, u.full_name, u.email, u.country,
                c.artist_id, c.concert_date, c.day_time, c.venue, c.location, c.title AS concert_title,
                a.artist_name, a.artist_image
            FROM tickets t
            -- Link users directly via requested order map parameters 
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN concerts c ON t.concert_id = c.concert_id
            LEFT JOIN artists a ON c.artist_id = a.artist_id
            WHERE t.ticket_id IN ($placeholders)
        ");
        
        $lookup_stmt->execute(array_values($all_order_ids));
        $fetched_details = $lookup_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map array using order index identifiers for targeted script access injection
        foreach ($fetched_details as $row) {
            $order_map_data[$row['ticket_id']] = $row;
        }
    }

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

    <td style="padding:12px;">
        <?php if(!empty($deposit['order_ids'])): 
            // Build temporary tracking reference context array structure for JS parsing
            $current_ids = array_filter(array_map('intval', explode(',', $deposit['order_ids'])));
            $rendered_tickets = [];
            foreach($current_ids as $tid) {
                if(isset($order_map_data[$tid])) {
                    $rendered_tickets[] = $order_map_data[$tid];
                }
            }
            $json_payload = htmlspecialchars(json_encode($rendered_tickets), ENT_QUOTES, 'UTF-8');
        ?>
            <button type="button" 
                    class="order-map-trigger"
                    data-orders="<?= $json_payload ?>"
                    style="background:#2563eb;color:#fff;border:none;padding:5px 10px;border-radius:4px;font-family:monospace;cursor:pointer;font-size:12px;font-weight:bold;text-decoration:underline;display:inline-block;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                    title="Click to view full mapping details">
                <?= htmlspecialchars($deposit['order_ids']) ?>
            </button>
        <?php else: ?>
            <span style="color:#666;font-family:monospace;">None</span>
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

<div id="orderMapModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.85); align-items:center; justify-content:center; padding:20px; box-sizing:border-box;">
    <div style="background:#1f2937; color:#f9fafb; border:1px solid #374151; width:100%; max-width:700px; border-radius:12px; max-height:85vh; overflow-y:auto; position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.5);">
        
        <div style="padding:15px 20px; border-bottom:1px solid #374151; display:flex; justify-content:between; align-items:center; position:sticky; top:0; background:#1f2937; z-index:10;">
            <h3 style="margin:0; font-size:18px; color:#60a5fa; font-weight:600;">Linked Ticket Order Breakdowns</h3>
            <button type="button" id="closeModalBtn" style="background:transparent; border:none; color:#9ca3af; font-size:24px; cursor:pointer; line-height:1; padding:0; margin-left:auto;">&times;</button>
        </div>

        <div id="modalDynamicContent" style="padding:20px;"></div>
    </div>
</div>

</main>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("orderMapModal");
    const modalContent = document.getElementById("modalDynamicContent");
    const closeBtn = document.getElementById("closeModalBtn");

    // Click event attachment across table trigger buttons
    document.querySelectorAll(".order-map-trigger").forEach(button => {
        button.addEventListener("click", function() {
            try {
                const ticketsData = JSON.parse(this.getAttribute("data-orders"));
                
                if (!ticketsData || ticketsData.length === 0) {
                    modalContent.innerHTML = `<p style="text-align:center; color:#9ca3af;">No record matches found tracking this ticket segment sequence map layout.</p>`;
                    modal.style.display = "flex";
                    return;
                }

                let dynamicHTML = "";

                // Safely fetch user details block once (constant per order map map)
                const primaryUser = ticketsData[0];
                dynamicHTML += `
                    <div style="background:#111827; border:1px solid #374151; padding:15px; border-radius:8px; margin-bottom:20px;">
                        <h4 style="margin-top:0; margin-bottom:10px; text-transform:uppercase; font-size:12px; letter-spacing:1px; color:#9ca3af;"><i class="fas fa-user"></i> Purchaser Profiling Info</h4>
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; font-size:14px;">
                            <div><strong>Full Name:</strong> ${escapeHTML(primaryUser.full_name || 'N/A')}</div>
                            <div><strong>Email:</strong> ${escapeHTML(primaryUser.email || 'N/A')}</div>
                            <div><strong>Country Location:</strong> ${escapeHTML(primaryUser.country || 'N/A')}</div>
                        </div>
                    </div>
                    <hr style="border:0; border-top:1px dashed #374151; margin:20px 0;">
                `;

                // Loop layout mapping all corresponding distinct sub-tickets grouped with artists
                ticketsData.forEach((ticket, idx) => {
                    const fallbackImg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="%234b5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>';
                    const artistImgSrc = ticket.artist_image ? `../uploads/artists/${ticket.artist_image}` : fallbackImg;

                    dynamicHTML += `
                        <div style="background:#111827; border-left:4px solid #3b82f6; border-top:1px solid #374151; border-right:1px solid #374151; border-bottom:1px solid #374151; padding:15px; border-radius:0 8px 8px 0; margin-bottom:15px;">
                            <span style="background:#2563eb; color:#fff; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:bold; float:right;">Item #${idx + 1}</span>
                            
                            <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                                <img src="${artistImgSrc}" alt="Artist Showcase" style="width:55px; height:55px; object-fit:cover; border-radius:50%; background:#374151; border:1px solid #4b5563;" onerror="this.src='${fallbackImg}'">
                                <div>
                                    <h4 style="margin:0; font-size:16px; color:#f3f4f6;">${escapeHTML(ticket.artist_name || 'Generic Listing Artist')}</h4>
                                    <small style="color:#9ca3af; font-size:12px;">ID Mapping Code: #ART-${ticket.artist_id || '0'}</small>
                                </div>
                            </div>

                            <div style="background:#1f2937; padding:10px; border-radius:6px; margin-bottom:10px; font-size:13px; border:1px solid #374151;">
                                <div style="font-weight:bold; color:#f59e0b; margin-bottom:5px;">Concert: ${escapeHTML(ticket.concert_title || 'Untitled Event')}</div>
                                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:5px;">
                                    <div>📅 <strong>Date:</strong> ${escapeHTML(ticket.concert_date || 'N/A')} (${escapeHTML(ticket.day_time || 'N/A')})</div>
                                    <div>📍 <strong>Venue Location:</strong> ${escapeHTML(ticket.venue || 'N/A')}, ${escapeHTML(ticket.location || 'N/A')}</div>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:10px; font-size:13px; padding-top:5px;">
                                <div>🎟️ <strong>Ticket Name:</strong> <span style="color:#60a5fa;">${escapeHTML(ticket.ticket_name || 'N/A')}</span></div>
                                <div>💺 <strong>Section:</strong> ${escapeHTML(ticket.section_name || 'N/A')}</div>
                                <div>↔️ <strong>Row:</strong> ${escapeHTML(ticket.row_name || 'N/A')}</div>
                                <div>💵 <strong>Value Price:</strong> <span style="color:#34d399; font-weight:bold;">$${parseFloat(ticket.price || 0).toFixed(2)}</span></div>
                            </div>
                        </div>
                    `;
                });

                modalContent.innerHTML = dynamicHTML;
                modal.style.display = "flex";

            } catch (err) {
                console.error("Payload breakdown parsing exception error:", err);
                alert("Failed parsing execution sequence mapping logs securely contextually formatting.");
            }
        });
    });

    // Close action execution handler triggers
    closeBtn.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });

    // String escape context controller to prevent script injections
    function escapeHTML(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
