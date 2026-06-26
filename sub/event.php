# 5. EVENT.PHP
<?php
session_start();
include 'db.php';
$site_name = getSetting('site_name', $conn);

$id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
$p2p_id = isset($_GET['p2p_id']) ? $conn->real_escape_string($_GET['p2p_id']) : '';

$is_p2p = !empty($p2p_id);
$title = ''; $venue = ''; $img_main = ''; $img_seat = ''; $price_vip = 0; $price_reg = 0; $sec_desc = '';

if ($is_p2p) {
    $stmt = $conn->prepare("SELECT * FROM transfers WHERE transfer_id = ? AND status='available'");
    $stmt->bind_param("s", $p2p_id);
    $stmt->execute();
    $t_data = $stmt->get_result()->fetch_assoc();
    if(!$t_data) { die("Market asset reference invalid or already collected."); }
    $title = "P2P Resale: " . $t_data['event_title'];
    $venue = "Section Coordinate Block Location: " . $t_data['section'] . " | Row " . $t_data['row_num'] . " | Seat " . $t_data['seat_num'];
    $price_reg = $t_data['price'];
} else {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $e_data = $stmt->get_result()->fetch_assoc();
    if(!$e_data) { die("Target standard allocation vector index out of bounds."); }
    $title = $e_data['title'];
    $venue = $e_data['venue'] . " • " . date('M d, Y', strtotime($e_data['event_date']));
    $img_main = $e_data['image_main'];
    $img_seat = $e_data['image_seating'];
    $price_vip = $e_data['price_vip'];
    $price_reg = $e_data['price_regular'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Asset Allocation | <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header style="background:<?php echo getSetting('header_bg', $conn); ?>;">
    <div class="header-top"><a href="index.php" class="brand"><?php echo $site_name; ?></a></div>
</header>

<main class="container">
    <div class="card" style="margin-top:15px;">
        <?php if(!$is_p2p && !empty($img_main)): ?>
            <img src="<?php echo htmlspecialchars($img_main); ?>" alt="Billboard Banner">
        <?php endif; ?>
        
        <div class="card-body">
            <div class="badge"><?php echo $is_p2p ? 'Peer Market Segment' : 'Primary Allocation Tier'; ?></div>
            <h2 style="font-size:1.4rem; font-weight:800; margin-top:5px; color:var(--dark);"><?php echo htmlspecialchars($title); ?></h2>
            <p style="color:var(--gray); font-size:0.9rem;">📍 <?php echo htmlspecialchars($venue); ?></p>
        </div>
    </div>

    <?php if(!$is_p2p && !empty($img_seat)): ?>
    <div class="card" style="padding:15px; background:white;">
        <h3 class="section-title" style="font-size:1rem; margin-bottom:10px;">Dynamic Venue Space Map Allocation Layout</h3>
        <img src="<?php echo htmlspecialchars($img_seat); ?>" alt="Venue Space Allocations Coordinate Graph" style="height:auto; max-height:260px; object-fit:contain; border:1px solid var(--border); border-radius:6px;">
    </div>
    <?php endif; ?>

    <h3 class="section-title" style="margin-top:20px;">Configure Order Allocation Parameter Matrices</h3>
    
    <form action="checkout.php" method="POST">
        <input type="hidden" name="item_type" value="<?php echo $is_p2p ? 'p2p' : 'primary'; ?>">
        <input type="hidden" name="target_id" value="<?php echo $is_p2p ? $p2p_id : $id; ?>">

        <?php if($is_p2p): ?>
            <div class="card" style="flex-direction:row; justify-content:space-between; align-items:center; padding:15px;">
                <div>
                    <h4 style="font-weight:700;">Verified Resale Ticket Pass</h4>
                    <p style="font-size:0.8rem; color:var(--gray);">Instant title allocation handshake</p>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:1.2rem; font-weight:800; color:var(--primary); display:block;">$<?php echo number_format($price_reg, 2); ?></span>
                    <input type="hidden" name="qty_tier_reg" value="1">
                    <span class="badge" style="background:#D1E7DD; color:#0F5132;">1 Pass Available</span>
                </div>
            </div>
        <?php else: ?>
            <div class="card" style="padding:15px; margin-bottom:12px;">
                <div style="display:flex; justify-content:between; width:100%; align-items:center;">
                    <div style="flex-grow:1;">
                        <span class="badge" style="background:var(--dark); color:white;">🌟 VIP PREMIUM TIER</span>
                        <h4 style="font-weight:700; margin-top:3px;">Front Stage Lounge Suite (Floor)</h4>
                        <p style="font-size:1.1rem; font-weight:800; color:var(--primary); margin-top:2px;">$<?php echo number_format($price_vip,2); ?></p>
                    </div>
                    <div style="width:90px;">
                        <select name="qty_tier_vip" class="form-control" style="padding:6px;">
                            <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:15px; margin-bottom:12px;">
                <div style="display:flex; justify-content:between; width:100%; align-items:center;">
                    <div style="flex-grow:1;">
                        <span class="badge">🎟️ STANDARD SECTOR</span>
                        <h4 style="font-weight:700; margin-top:3px;">Main Stadium Bowl Upper deck Deck</h4>
                        <p style="font-size:1.1rem; font-weight:800; color:var(--primary); margin-top:2px;">$<?php echo number_format($price_reg,2); ?></p>
                    </div>
                    <div style="width:90px;">
                        <select name="qty_tier_reg" class="form-control" style="padding:6px;">
                            <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn" style="margin-top:20px; font-size:1.05rem;">Lock Ticket Selections to Cart</button>
    </form>
</main>
</body>
</html>
