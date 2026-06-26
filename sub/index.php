# 4. INDEX.PHP
<?php
session_start();
include 'db.php';

$site_name = getSetting('site_name', $conn);
$header_bg = getSetting('header_bg', $conn);
$header_logo = getSetting('header_logo', $conn);
$alert_text = getSetting('alert_text', $conn);

$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$cat = isset($_GET['cat']) ? $conn->real_escape_string($_GET['cat']) : 'All';

// Smart multi-criteria keyword parsing engine query string build
$query = "SELECT * FROM events WHERE 1=1";
if ($cat !== 'All') {
    $query .= " AND category = '$cat'";
}
if ($search !== '') {
    $query .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR search_keywords LIKE '%$search%' OR DATE_FORMAT(event_date, '%Y-%m-%d') LIKE '%$search%')";
}
$query .= " ORDER BY event_date ASC";
$events_res = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?> | Verified Event Tickets</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php if(!empty($alert_text)): ?>
<div class="alert-ribbon"><?php echo $alert_text; ?></div>
<?php endif; ?>

<header style="background: <?php echo !empty($header_bg) ? $header_bg : 'var(--dark)'; ?>;">
    <div class="header-top">
        <a href="index.php" class="brand">
            <?php if(!empty($header_logo)): ?>
                <img src="<?php echo $header_logo; ?>" alt="<?php echo $site_name; ?>">
            <?php else: ?>
                <?php echo $site_name; ?>
            <?php endif; ?>
        </a>
        <div id="google_translate_element"></div>
    </div>
    
    <div class="nav-links">
        <?php if(isset($_SESSION['user_auth'])): ?>
            <a href="dashboard.php" class="nav-btn" style="background:var(--primary);">🎟️ My Dashboard</a>
            <a href="logout.php" class="nav-btn">Exit</a>
        <?php else: ?>
            <a href="login.php" class="nav-btn">Log In</a>
            <a href="register.php" class="nav-btn" style="background:var(--primary);">Register</a>
        <?php endif; ?>
    </div>

    <div class="search-wrapper">
        <form action="index.php" method="GET">
            <input type="text" name="q" class="search-input" placeholder="Search artists, stadium coordinates, dates, tags..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>
</header>

<?php if($search === ''): ?>
<section class="hero-canvas" style="<?php $h_img = getSetting('hero_img', $conn); if(!empty($h_img)) echo "background: linear-gradient(rgba(10,14,26,0.85), rgba(10,14,26,0.95)), url('$h_img') no-repeat center/cover;"; ?>">
    <h1><?php echo getSetting('hero_title', $conn); ?></h1>
    <p><?php echo getSetting('hero_subtitle', $conn); ?></p>
</section>
<?php endif; ?>

<main class="container">
    
    <div class="filter-scroll">
        <?php 
        $categories = ['All', 'Concerts', 'Sports', 'Theater'];
        foreach($categories as $c) {
            $active_class = ($cat === $c) ? 'active' : '';
            echo "<a href='index.php?cat=$c&q=".urlencode($search)."' class='filter-pill $active_class'>$c</a>";
        }
        ?>
    </div>

    <?php 
    // Checking for matching Peer to Peer Ticket Resale code match parameter targets
    if ($search !== '') {
        $p2p_chk = $conn->query("SELECT * FROM transfers WHERE transfer_id = '$search' AND status='available'");
        if ($p2p_chk && $p2p_chk->num_rows > 0) {
            $t_row = $p2p_chk->fetch_assoc();
            echo "
            <div class='card' style='border: 2px solid var(--accent); margin-bottom: 25px;'>
                <div class='card-body'>
                    <div class='badge' style='background:#FEF3C7; color:#D97706;'>SECURE TRANSFER ID KEY MATCH</div>
                    <h3 style='margin-top:5px;'>Resale Pass: ".htmlspecialchars($t_row['event_title'])."</h3>
                    <p class='card-meta'>📍 Section Location Map Vector: <strong>".htmlspecialchars($t_row['section'])."</strong></p>
                    <p class='card-meta'>🪑 Row Coordinate: ".htmlspecialchars($t_row['row_num'])." | Seat Frame ID: ".htmlspecialchars($t_row['seat_num'])."</p>
                    <div class='card-price'>$".number_format($t_row['price'], 2)."</div>
                    <a href='event.php?p2p_id=".$t_row['transfer_id']."' class='btn' style='background:var(--accent); color:var(--dark); margin-top:10px;'>Inspect Peer Listing Asset</a>
                </div>
            </div>";
        }
    }
    ?>

    <h2 class="section-title">
        <?php echo ($search !== '') ? 'Discovered Matches' : 'Trending Entertainment Packages'; ?>
    </h2>

    <div class="grid-container">
        <?php
        if($events_res && $events_res->num_rows > 0) {
            while($ev = $events_res->fetch_assoc()) {
                echo "
                <div class='card'>
                    <img src='".(!empty($ev['image_main']) ? htmlspecialchars($ev['image_main']) : 'uploads/default_event.jpg')."' alt='Poster'>
                    <div class='card-body'>
                        <div class='badge'>".htmlspecialchars($ev['category'])."</div>
                        <h3>".htmlspecialchars($ev['title'])."</h3>
                        <p class='card-meta'>📍 ".htmlspecialchars($ev['venue'])."</p>
                        <p class='card-meta'>📅 ".date('M d, Y • h:i A', strtotime($ev['event_date']))."</p>
                        <div class='card-price'>From $".number_format($ev['price_regular'], 2)."</div>
                        <a href='event.php?id=".$ev['id']."' class='btn' style='margin-top:10px;'>Find Tickets</a>
                    </div>
                </div>";
            }
        } else {
            echo "<p style='grid-column: 1/-1; text-align:center; color:var(--gray); padding: 30px 0;'>No interactive matches registered under configuration keywords.</p>";
        }
        ?>
    </div>

    <section class="trust-widget">
        <div class="trust-item">
            <span>🛡️</span>
            <h4>100% Secure</h4>
            <p>Verified pass tokens mapped directly.</p>
        </div>
        <div class="trust-item">
            <span>🔄</span>
            <h4>P2P Swap</h4>
            <p>Direct authentic code allocation matrix.</p>
        </div>
        <div class="trust-item">
            <span>💬</span>
            <h4>Direct Chat</h4>
            <p>Instant clear settlement vectors.</p>
        </div>
    </section>

    <section class="card" style="padding: 20px; background: white; margin-bottom:25px;">
        <h3 style="font-size:1.2rem; margin-bottom:8px; border-bottom: 2px solid var(--border); padding-bottom:5px;"><?php echo getSetting('about_title', $conn); ?></h3>
        <p style="font-size:0.85rem; color:var(--gray); line-height:1.6;"><?php echo getSetting('about_text', $conn); ?></p>
    </section>

    <section class="faq-box">
        <h3 class="section-title">Frequently Answered Contexts</h3>
        <div class="faq-item">
            <div class="faq-trigger">❓ <?php echo getSetting('faq_1_q', $conn); ?> <span>▼</span></div>
            <div class="faq-content"><?php echo getSetting('faq_1_a', $conn); ?></div>
        </div>
        <div class="faq-item">
            <div class="faq-trigger">❓ <?php echo getSetting('faq_2_q', $conn); ?> <span>▼</span></div>
            <div class="faq-content"><?php echo getSetting('faq_2_a', $conn); ?></div>
        </div>
    </section>

</main>

<footer>
    <div style="font-weight:800; color:white; letter-spacing:1px; font-size:1rem;"><?php echo $site_name; ?> GLOBAL</div>
    <div style="margin-top:10px; display:flex; justify-content:center; gap:15px; font-size:0.75rem;">
        <a href="#" style="color:#64748B; text-decoration:none;">Terms of Agreement</a> • 
        <a href="#" style="color:#64748B; text-decoration:none;">Privacy Shield</a> • 
        <a href="#" style="color:#64748B; text-decoration:none;">Escrow Protocols</a>
    </div>
    <p><?php echo getSetting('footer_copyright', $conn); ?></p>
</footer>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
// Accordion Toggle Handling Node Module script
document.querySelectorAll('.faq-trigger').forEach(item => {
    item.addEventListener('click', () => {
        const content = item.nextElementSibling;
        content.style.display = (content.style.display === 'block') ? 'none' : 'block';
    });
});
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
