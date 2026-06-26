<div class="top-bar">
    <div class="top-bar-left">
        <span><i class="fa-regular fa-comment-dots"></i> US</span>
    </div>
    <div class="top-bar-right">
        <a href="#">Hotels</a>
        <a href="#">Sell</a>
        <a href="#">Gift Cards</a>
        <a href="#">Help</a>
        <a href="#">VIP</a>
        <a href="/paypal.php"><img src="assets/images/paypal.png" alt="PayPal"></a>
    </div>
</div>

<header class="main-header">
    <a href="/index.php">
        <img src="assets/images/logo.png" alt="<?php echo htmlspecialchars($site_name ?? 'ticketmaster®'); ?>">
    </a>
    <nav class="nav-links">
        <a href="#">Concerts</a>
        <a href="#">Sports</a>
        <a href="#">Arts, Theater & Comedy</a>
        <a href="#">Family</a>
        <a href="#">Cities</a>
    </nav>
    <div class="header-actions">
        <div class="search-container">
            <div class="search-texts">
                <span class="search-label">SEARCH</span>
                <input type="text" placeholder="Artist, Event or Venue" spellcheck="false">
            </div>
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <button class="btn-ingresa">
            <i class="fa-regular fa-user" style="font-size: 18px;"></i>
            Sign In/Register
        </button>
    </div>
</header>

<section class="hero">
    <div class="hero-content breadcrumbs">
        Home / Concerts / K-Pop / <span>BTS Tickets</span>
    </div>

    <div class="hero-bottom">
        <span class="tag-kpop">K-Pop</span>
        <h1>BTS Tickets</h1>
        <div class="hero-interactions">
            <button class="btn-fav"><i class="fa-regular fa-heart"></i></button>
            <div class="rating-badge">
                <i class="fa-solid fa-star"></i>
                <span>5.0</span>
            </div>
        </div>
    </div>
</section>

<nav class="sub-nav">
    <ul class="sub-nav-list">
        <li><a href="#" class="active">CONCERTS</a></li>
        <li><a href="#">GALLERY</a></li>
        <li><a href="#">ABOUT</a></li>
        <li><a href="#">SETLIST</a></li>
        <li><a href="#">FAQ</a></li>
        <li><a href="#">REVIEWS</a></li>
        <li><a href="#">FANS ALSO VIEWED</a></li>
    </ul>
</nav>

<main class="main-container">
    <div class="section-title-row">
        <div class="title-left">
            <h2>CONCERTS</h2>
            <span class="bullet">•</span>
            <span class="results-count">48 RESULTS</span>
        </div>

        <div class="view-switcher">
            <button class="active"><i class="fa-solid fa-list-ul"></i></button>
            <button><i class="fa-regular fa-calendar"></i></button>
        </div>
    </div>

    <div class="filter-wrapper">
        <div class="filter-group">
            <label>Dates</label>
            <div class="dropdown">
                <i class="fa-regular fa-calendar-days"></i>
                <span>All dates</span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        </div>
    </div>
</main>

</body>
</html>
