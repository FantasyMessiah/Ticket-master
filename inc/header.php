<?php
// Ensure session layer variables are active prior to layout verification checks
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// MOCK ACTIVE SESSION CHECK: Uncomment the next line to test the signed-in view state
// $_SESSION['user_id'] = 1; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticketmaster Style Header</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  
  <style>
    .nav-link {
      transition: all 0.2s ease;
    }
    .nav-link:hover {
      color: #fff;
      border-bottom: 3px solid #fff;
    }
  </style>
</head>
<body class="bg-gray-50">

  <!-- Main Navigation Bar Entry Module -->
  <nav class="bg-[#024DDF] text-white border-b border-blue-800 relative z-40">
    <div class="max-w-7xl mx-auto px-6">
      <div class="flex items-center justify-between h-14 lg:h-[88px]">
  
        <div class="flex items-center gap-4 lg:gap-5">
          <!-- HAMBURGER BUTTON -->
          <button id="mobileMenuTrigger" class="block lg:hidden hover:bg-[#013ba8] p-1 rounded-md transition-colors focus:outline-none" aria-label="Toggle Navigation Menu">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-8 h-8"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h7" />
            </svg>
          </button>
  
          <a href="index.php" class="flex items-center">
            <img src="assets/images/logo.png"
                 alt="Ticketmaster"
                 class="h-6 lg:h-[26px] w-auto">
          </a>

          <!-- Desktop Navigation Link Array Layout -->
          <ul class="hidden lg:flex items-center gap-6 text-base font-bold">
            <li><a href="search.php?q=Concerts" class="nav-link">Concerts</a></li>
            <li><a href="search.php?q=Sports" class="nav-link">Sports</a></li>
            <li><a href="search.php?q=Art" class="nav-link">Arts, Theater &amp; Comedy</a></li>
            <li><a href="search.php?q=Family" class="nav-link">Family</a></li>
            <li><a href="search.php?q=Cities" class="nav-link">Cities</a></li>
          </ul>
          
        </div>
  
        <!-- Dynamic Session Authentication Link Channel Module -->
        <div class="flex items-center">
          <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Signed In View: Shows User Profile Icon linking to Dashboard -->
            <a href="dashboard.php"
               class="flex items-center gap-2 lg:gap-3 text-white hover:text-gray-200 transition-colors duration-200"
               title="Access Dashboard Portfolio">
              <i class="fa-regular fa-user text-xl bg-blue-800 p-2 rounded-full border border-blue-700 shadow-sm"></i>
              <span class="hidden md:inline font-bold text-sm lg:text-base">Dashboard</span>
            </a>
          <?php else: ?>
            <!-- Signed Out View: Displays Default Sign In Registration Links -->
            <a href="auth.php"
               class="flex items-center gap-2 lg:gap-3 text-white hover:text-gray-200 transition-colors duration-200">
              <i class="fa-regular fa-user text-xl"></i>
              <span class="hidden md:inline font-bold text-sm lg:text-base">
                Sign In / Register
              </span>
            </a>
          <?php endif; ?>
        </div>
  
      </div>
    </div>
  </nav>

  <!-- MOBILE NAVIGATION DRAWER: Redesigned Dark Layout Element Shell -->
  <div id="mobileNavigationDrawer" class="fixed inset-0 z-50 invisible transition-all duration-300">
    <!-- Dark Backdrop Shield Overlay -->
    <div id="drawerBackdrop" class="absolute inset-0 bg-black/60 opacity-0 transition-opacity duration-300"></div>
    
    <!-- Floating Lateral Slide Dark Content Card Container Context -->
    <div id="drawerContent" class="absolute inset-y-0 left-0 w-72 max-w-xs bg-slate-900 text-slate-100 shadow-2xl transform -translate-x-full transition-transform duration-300 flex flex-col border-r border-slate-800">
      <div class="bg-slate-950 text-white px-5 py-4 flex items-center justify-between border-b border-slate-800">
        <span class="font-black text-xs uppercase tracking-widest text-blue-400">Navigation Menu</span>
        <button id="mobileMenuClose" class="text-slate-400 hover:text-white text-xl p-1 focus:outline-none">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Core Link Matrix Population Layer -->
      <ul class="flex-1 overflow-y-auto font-bold text-sm divide-y divide-slate-800/60 uppercase tracking-tight">
        <li>
          <a href="search.php?q=Concerts" class="flex items-center gap-3 px-5 py-4 hover:bg-slate-800/50 text-slate-200 transition-colors">
            <i class="fas fa-music text-base w-5 text-center text-slate-400"></i> Concerts
          </a>
        </li>
        <li>
          <a href="search.php?q=Sports" class="flex items-center gap-3 px-5 py-4 hover:bg-slate-800/50 text-slate-200 transition-colors">
            <i class="fas fa-football-ball w-5 text-center text-slate-400"></i> Sports
          </a>
        </li>
        <li>
          <a href="search.php?q=Art" class="flex items-center gap-3 px-5 py-4 hover:bg-slate-800/50 text-slate-200 transition-colors">
            <i class="fas fa-theater-masks w-5 text-center text-slate-400"></i> Arts &amp; Theater
          </a>
        </li>
        <li>
          <a href="search.php?q=Family" class="flex items-center gap-3 px-5 py-4 hover:bg-slate-800/50 text-slate-200 transition-colors">
            <i class="fas fa-child w-5 text-center text-slate-400"></i> Family
          </a>
        </li>
        <li>
          <a href="search.php?q=Cities" class="flex items-center gap-3 px-5 py-4 hover:bg-slate-800/50 text-slate-200 transition-colors">
            <i class="fas fa-city w-5 text-center text-slate-400"></i> Cities
          </a>
        </li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Responsive Active Dashboard link vector for mobile drawer -->
          <li class="border-t border-slate-700/50">
            <a href="dashboard.php" class="flex items-center gap-3 px-5 py-4 bg-slate-950 text-blue-400 hover:bg-slate-800 transition-colors">
              <i class="fas fa-columns text-base w-5 text-center"></i> User Dashboard
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <!-- Central Search Bar Container Module Panel -->
  <div class="bg-[#024DDF] py-6 md:py-11">
    <div class="max-w-5xl mx-auto px-4 md:px-6">
  
      <div class="bg-white text-gray-900 rounded shadow-md p-1.5 max-w-full mx-auto relative">
  
        <form action="search.php" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center">
  
          <div class="flex flex-row w-full flex-1 divide-x divide-gray-200">
  
            <!-- Location -->
            <div class="flex items-center gap-3.5 px-4 py-2 flex-1">
              <i class="fa-solid fa-location-dot text-[#024DDF] text-lg"></i>
              <div class="w-full">
                <label class="text-[11px] uppercase font-bold tracking-wider text-black block leading-none mb-0.5">Location</label>
                <input type="text" 
                       name="location" 
                       value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>"
                       placeholder="City or Zip Code"
                       class="bg-transparent outline-none w-full text-sm font-medium text-gray-600 placeholder-gray-400 py-0">
              </div>
            </div>
  
            <!-- Dates -->
            <div class="flex items-center gap-3.5 px-4 py-2 flex-1 cursor-pointer hover:bg-gray-50/50 transition-colors">
              <i class="fa-regular fa-calendar-days text-[#024DDF] text-lg"></i>
              <div class="w-full">
                <label class="text-[11px] uppercase font-bold tracking-wider text-black block leading-none mb-0.5">Dates</label>
                <span class="text-sm block text-gray-500 font-medium leading-none mt-0.5">All Dates</span>
              </div>
              <i class="fas fa-chevron-down text-gray-400 text-xs ml-auto"></i>
            </div>
  
          </div>
  
          <!-- Search Input -->
          <div class="flex items-center gap-3.5 px-4 py-2.5 flex-1 md:flex-[1.8] border-t md:border-t-0 border-gray-200">
            <i class="fa-solid fa-magnifying-glass text-[#024DDF] text-lg"></i>
            <div class="w-full">
              <label class="text-[11px] uppercase font-bold tracking-wider text-black block leading-none mb-0.5">Search</label>
              <input type="text"
                     name="q"
                     value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                     placeholder="Artist, Event or Venue"
                     class="bg-transparent outline-none w-full text-sm font-medium text-gray-600 placeholder-gray-400 py-0"
                     required>
            </div>
          </div>
  
          <!-- Action Search Button -->
          <div class="p-1 md:p-0 shrink-0">
            <button type="submit"
                    class="w-full md:w-auto bg-[#024DDF] hover:bg-[#013ba8] text-white px-8 py-3 rounded font-bold text-sm flex items-center justify-center gap-2 transition-colors">
              Search
            </button>
          </div>
  
        </form>
      </div>
    </div>
  </div>

  <!-- INTERACTIVE RESPONSIVE JAVASCRIPT ANIMATION ROUTINES -->
  <script>
    const trigger = document.getElementById('mobileMenuTrigger');
    const closeBtn = document.getElementById('mobileMenuClose');
    const backdrop = document.getElementById('drawerBackdrop');
    const drawer = document.getElementById('mobileNavigationDrawer');
    const content = document.getElementById('drawerContent');

    function openMobileMenu() {
      drawer.classList.remove('invisible');
      setTimeout(() => {
        backdrop.classList.add('opacity-100');
        content.classList.remove('-translate-x-full');
      }, 10);
    }

    function closeMobileMenu() {
      backdrop.classList.remove('opacity-100');
      content.classList.add('-translate-x-full');
      setTimeout(() => {
        drawer.classList.add('invisible');
      }, 300);
    }

    trigger.addEventListener('click', openMobileMenu);
    closeBtn.addEventListener('click', closeMobileMenu);
    backdrop.addEventListener('click', closeMobileMenu);
  </script>

</body>
</html>
