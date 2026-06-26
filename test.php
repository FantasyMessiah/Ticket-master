
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletos para BTS | Ticketmaster MX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:ital,wght@0,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --tm-blue: #026cdf;
            --tm-dark-blue: #0150a7;
            --bg-body: #f4f5f7;
            --text-dark: #1f262d;
            --text-muted: #767676;
            --top-bar-bg: #111111;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-dark);
        }

        /* --- 1. TOP BAR (Delgada negra) --- */
        .top-bar {
            background-color: var(--top-bar-bg);
            color: #d1d1d1;
            font-size: 11px;
            height: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            border-bottom: 1px solid #222;
        }

        .top-bar-left {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .top-bar-left span {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .top-bar-right a {
            color: #d1d1d1;
            text-decoration: none;
        }

        .top-bar-right a:hover {
            text-decoration: underline;
        }

        /* --- 2. MAIN HEADER (Azul Ticketmaster) --- */
        .main-header {
            background-color: var(--tm-blue);
            height: 84px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            color: white;
        }

        /* Emulación del logo con CSS puro */
        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-style: italic;
            font-size: 26px;
            letter-spacing: -1.2px;
            cursor: pointer;
        }

        .nav-links {
            display: flex;
            gap: 22px;
            font-size: 14px;
            font-weight: 600;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* El truco del buscador doble renglón */
        .search-container {
            background-color: rgba(0, 0, 0, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 4px;
            display: flex;
            align-items: center;
            width: 290px;
            height: 46px;
            padding: 6px 14px;
            transition: all 0.2s;
        }

        .search-container:focus-within {
            background-color: white;
            border-color: white;
        }

        .search-texts {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .search-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2px;
        }

        .search-container:focus-within .search-label { color: var(--tm-blue); }

        .search-container input {
            background: transparent;
            border: none;
            color: white;
            font-size: 13px;
            outline: none;
            width: 100%;
        }

        .search-container:focus-within input { color: var(--text-dark); }
        .search-container input::placeholder { color: rgba(255, 255, 255, 0.6); }
        .search-container:focus-within input::placeholder { color: #aaa; }

        .search-container i {
            font-size: 16px;
            color: white;
            cursor: pointer;
        }

        .search-container:focus-within i { color: var(--tm-blue); }

        .btn-ingresa {
            background: transparent;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* --- 3. HERO BANNER (La foto de BTS) --- */
        .hero {
            position: relative;
            height: 430px;
            background-color: #0b0f19;
            background-image: url('bts.jpg');
            background-size: cover;
            background-position: center top;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 25px 40px 40px 40px;
            color: white;
        }

        /* Gradiente oscuro sutil para que el texto sea perfectamente legible */
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .breadcrumbs {
            font-size: 12px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .breadcrumbs span {
            color: white;
            font-weight: 500;
        }

        .hero-bottom {
            position: relative;
            z-index: 2;
            margin-top: auto;
        }

        .tag-kpop {
            font-size: 16px;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 52px;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }

        .hero-interactions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-fav {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(0, 0, 0, 0.4);
            color: white;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(4px);
        }

        .rating-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
        }

        .rating-badge i { color: #f5c518; }

        /* --- 4. SUB NAVEGACIÓN (Sticky blanca) --- */
        .sub-nav {
            background-color: white;
            border-bottom: 1px solid #e2e4e8;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .sub-nav-list {
            display: flex;
            gap: 35px;
            list-style: none;
        }

        .sub-nav-list li a {
            display: inline-block;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.2px;
            padding: 22px 0;
            transition: color 0.2s;
        }

        .sub-nav-list li a.active {
            color: var(--text-dark);
            border-bottom: 3px solid var(--text-dark);
        }

        /* --- 5. CONTENIDO PRINCIPAL --- */
        .main-container {
            max-width: 1300px;
            padding: 40px;
        }

        .section-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .title-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .title-left h2 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            position: relative;
        }

        /* El pequeño guion negro superior de Ticketmaster */
        .title-left h2::before {
            content: '';
            position: absolute;
            top: -12px;
            left: 0;
            width: 22px;
            height: 3px;
            background-color: var(--text-dark);
        }

        .title-left .bullet {
            color: var(--text-muted);
            font-size: 14px;
        }

        .title-left .results-count {
            font-size: 18px;
            color: #444;
            font-weight: 400;
        }

        .view-switcher {
            display: flex;
            border: 1px solid #dcdfe4;
            border-radius: 24px;
            overflow: hidden;
            background: white;
            padding: 2px;
        }

        .view-switcher button {
            border: none;
            background: transparent;
            width: 44px;
            height: 34px;
            border-radius: 20px;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 15px;
            transition: all 0.2s;
        }

        .view-switcher button.active {
            background-color: var(--text-dark);
            color: white;
        }

        /* Tarjeta de Filtro */
        .filter-wrapper {
            background-color: white;
            border: 1px solid #e2e4e8;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            min-height: 180px;
        }

        .filter-group {
            display: inline-block;
        }

        .filter-group label {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .dropdown {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #b0b5bd;
            border-radius: 4px;
            padding: 0 14px;
            width: 180px;
            height: 40px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            background: white;
        }

        .dropdown i.fa-calendar-days { color: var(--tm-blue); font-size: 15px; }
        .dropdown span { margin-right: auto; margin-left: 10px; }
        .dropdown i.fa-chevron-down { font-size: 11px; color: var(--text-muted); }

    </style>
</head>
<body>

    <div class="top-bar">
        <div class="top-bar-left">
            <span><i class="fa-solid fa-flag" style="color:#2e7d32;"></i> MX</span>
            <span><i class="fa-regular fa-comment-dots"></i> ES</span>
            <span><i class="fa-solid fa-location-arrow" style="font-size: 9px;"></i> Todo México</span>
        </div>
        <div class="top-bar-right">
            <a href="#">Ayuda</a>
        </div>
    </div>

    <header class="main-header">
        <div class="logo">ticketmaster®</div>
        <nav class="nav-links">
            <a href="#">Conciertos y Festivales</a>
            <a href="#">Teatro y Cultura</a>
            <a href="#">Deportes</a>
            <a href="#">Familiares</a>
            <a href="#">Especiales</a>
            <a href="#">Ciudades</a>
        </nav>
        <div class="header-actions">
            <div class="search-container">
                <div class="search-texts">
                    <span class="search-label">BUSCAR</span>
                    <input type="text" placeholder="Artista, evento o inmueble" spellcheck="false">
                </div>
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <button class="btn-ingresa">
                <i class="fa-regular fa-user" style="font-size: 18px;"></i>
                Ingresa
            </button>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content breadcrumbs">
            Inicio / Conciertos / K-Pop / <span>Boletos para BTS</span>
        </div>

        <div class="hero-bottom">
            <span class="tag-kpop">K-Pop</span>
            <h1>Boletos para BTS</h1>
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
            <li><a href="#" class="active">CONCIERTOS</a></li>
            <li><a href="#">GALERÍA</a></li>
            <li><a href="#">ACERCA DE</a></li>
            <li><a href="#">SETLIST</a></li>
            <li><a href="#">PREGUNTAS FRECUENTES</a></li>
            <li><a href="#">RESEÑAS</a></li>
            <li><a href="#">LOS FANS TAMBIÉN VIERON</a></li>
        </ul>
    </nav>

    <main class="main-container">
        <div class="section-title-row">
            <div class="title-left">
                <h2>CONCIERTOS</h2>
                <span class="bullet">•</span>
                <span class="results-count">48 RESULTADOS</span>
            </div>
            
            <div class="view-switcher">
                <button class="active"><i class="fa-solid fa-list-ul"></i></button>
                <button><i class="fa-regular fa-calendar"></i></button>
            </div>
        </div>

        <div class="filter-wrapper">
            <div class="filter-group">
                <label>Fechas</label>
                <div class="dropdown">
                    <i class="fa-regular fa-calendar-days"></i>
                    <span>Todas las f...</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
