<style>
/* Mobile-First + Ticketmaster Inspired */
* { box-sizing: border-box; }
.container {
    max-width: 1280px;
    margin: auto;
    padding: 15px;
}

/* Colors */
:root {
    --tm-red: #e4002b;
    --tm-dark: #121212;
}

/* BANNER / HERO */
.hero {
    height: 520px;
    background: linear-gradient(rgba(18,18,18,0.65), rgba(18,18,18,0.75)), 
                url('https://picsum.photos/id/1015/2000/1200') center/cover no-repeat;
    border-radius: 16px;
    display: flex;
    align-items: center;
    color: white;
    position: relative;
    margin-bottom: 60px;
}
.hero-content {
    max-width: 600px;
    padding-left: 50px;
}
.hero h1 {
    font-size: 48px;
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 16px;
}
.hero p {
    font-size: 20px;
    margin-bottom: 30px;
    opacity: 0.95;
}
.btn-find {
    background: var(--tm-red);
    color: white;
    padding: 16px 40px;
    font-size: 18px;
    font-weight: 700;
    border-radius: 50px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}
.btn-find:hover {
    background: #c40022;
    transform: scale(1.05);
}

/* SECTION TITLES */
.section-title {
    font-size: 28px;
    font-weight: 700;
    margin: 50px 0 20px;
    color: var(--tm-dark);
    display: flex;
    align-items: center;
    gap: 12px;
}

/* EVENT CARD */
.event-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}
.event-card:hover {
    transform: translateY(-6px);
}
.event-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.event-info {
    padding: 16px;
}
.event-info h3 {
    font-size: 18px;
    margin: 0 0 6px;
    line-height: 1.3;
}
.event-info p {
    color: #555;
    font-size: 14.5px;
    margin: 4px 0;
}
.date-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: white;
    color: var(--tm-red);
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 700;
    text-align: center;
    font-size: 13px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* GRID */
.grid-4 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 24px;
}

/* HORIZONTAL SCROLL CAROUSEL */
.carousel {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding-bottom: 20px;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}
.carousel::-webkit-scrollbar {
    height: 6px;
}
.carousel::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}
.carousel .event-card {
    min-width: 260px;
    flex-shrink: 0;
}

/* BUTTONS */
.btn {
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}
.btn-primary {
    background: var(--tm-red);
    color: white;
}
.btn-outline {
    border: 2px solid var(--tm-red);
    color: var(--tm-red);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .hero h1 { font-size: 36px; }
    .hero { height: 420px; }
    .section-title { font-size: 24px; }
}
</style>

<div class="container">

    <!-- 1. BANNER -->
    <div class="hero">
        <div class="hero-content">
            <h1>Live Music. Live Moments.</h1>
            <p>Buy tickets to the biggest concerts, tours &amp; festivals near you.</p>
            <a href="#" class="btn-find">Find Tickets</a>
        </div>
    </div>

    <!-- 2. UPCOMING EVENTS - 4 Grid -->
    <h2 class="section-title"><i class="fa-solid fa-calendar icon-red"></i> Upcoming Events</h2>
    <div class="grid-4">
        <!-- Event 1 -->
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/1015/600/400" alt="Taylor Swift">
                <div class="date-badge">
                    JUL<br><strong>15</strong>
                </div>
            </div>
            <div class="event-info">
                <h3>Taylor Swift - The Eras Tour</h3>
                <p>MetLife Stadium • East Rutherford, NJ</p>
                <p style="color:var(--tm-red); font-weight:600;">From $89</p>
                <a href="#" class="btn btn-primary" style="margin-top:12px; width:100%; text-align:center;">Get Tickets</a>
            </div>
        </div>

        <!-- Event 2 -->
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/201/600/400" alt="Drake">
                <div class="date-badge">
                    JUL<br><strong>22</strong>
                </div>
            </div>
            <div class="event-info">
                <h3>Drake - It's All A Blur Tour</h3>
                <p>Scotiabank Arena • Toronto, ON</p>
                <p style="color:var(--tm-red); font-weight:600;">From $65</p>
                <a href="#" class="btn btn-primary" style="margin-top:12px; width:100%; text-align:center;">Get Tickets</a>
            </div>
        </div>

        <!-- Event 3 -->
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/870/600/400" alt="Bad Bunny">
                <div class="date-badge">
                    AUG<br><strong>05</strong>
                </div>
            </div>
            <div class="event-info">
                <h3>Bad Bunny - Most Wanted Tour</h3>
                <p>SoFi Stadium • Los Angeles, CA</p>
                <p style="color:var(--tm-red); font-weight:600;">From $75</p>
                <a href="#" class="btn btn-primary" style="margin-top:12px; width:100%; text-align:center;">Get Tickets</a>
            </div>
        </div>

        <!-- Event 4 -->
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/133/600/400" alt="Billie Eilish">
                <div class="date-badge">
                    AUG<br><strong>12</strong>
                </div>
            </div>
            <div class="event-info">
                <h3>Billie Eilish - Hit Me Hard Tour</h3>
                <p>United Center • Chicago, IL</p>
                <p style="color:var(--tm-red); font-weight:600;">From $55</p>
                <a href="#" class="btn btn-primary" style="margin-top:12px; width:100%; text-align:center;">Get Tickets</a>
            </div>
        </div>
    </div>

    <!-- 3. TRENDING SEARCHES - Horizontal Scroll -->
    <h2 class="section-title"><i class="fa-solid fa-fire icon-red"></i> Trending Searches</h2>
    <div class="carousel">
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/1015/600/400" alt="">
                <div class="date-badge">JUL 15</div>
            </div>
            <div class="event-info">
                <h3>Taylor Swift</h3>
                <p>The Eras Tour</p>
            </div>
        </div>
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/201/600/400" alt="">
                <div class="date-badge">JUL 22</div>
            </div>
            <div class="event-info">
                <h3>Drake</h3>
                <p>It's All A Blur</p>
            </div>
        </div>
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/870/600/400" alt="">
                <div class="date-badge">AUG 05</div>
            </div>
            <div class="event-info">
                <h3>Bad Bunny</h3>
                <p>Most Wanted Tour</p>
            </div>
        </div>
        <div class="event-card">
            <div style="position:relative;">
                <img src="https://picsum.photos/id/133/600/400" alt="">
                <div class="date-badge">AUG 12</div>
            </div>
            <div class="event-info">
                <h3>Billie Eilish</h3>
                <p>Hit Me Hard</p>
            </div>
        </div>
    </div>

    <!-- 4. SPONSORED PRESALES & OFFERS -->
    <h2 class="section-title"><i class="fa-solid fa-star icon-red"></i> Sponsored Presales &amp; Offers</h2>
    <div class="grid-4" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
        <!-- Add 3 sponsored cards here (similar structure as above) -->
        <div class="event-card"> <!-- Card 1 --> </div>
        <div class="event-card"> <!-- Card 2 --> </div>
        <div class="event-card"> <!-- Card 3 --> </div>
    </div>

    <!-- 5. POPULAR NEAR YOU -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin:50px 0 20px;">
        <h2 class="section-title" style="margin:0;"><i class="fa-solid fa-location-dot icon-red"></i> Popular Near You</h2>
        <a href="#" class="btn btn-outline">See All</a>
    </div>
    <div class="carousel">
        <!-- Same structure as Trending Searches - repeat event cards -->
        <div class="event-card"> <!-- Event --> </div>
        <div class="event-card"> <!-- Event --> </div>
        <div class="event-card"> <!-- Event --> </div>
        <div class="event-card"> <!-- Event --> </div>
    </div>

</div>
