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

  <nav class="bg-[#024DDF] text-white border-b border-blue-800 relative z-40">
    <div class="max-w-7xl mx-auto px-6">
      <div class="flex items-center justify-between h-14 lg:h-[88px]">
  
        <div class="flex items-center gap-4 lg:gap-5">
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

          <ul class="hidden lg:flex items-center gap-6 text-base font-bold">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="search.php?q=Concerts" class="nav-link">Concerts</a></li>
            <li><a href="search.php?q=Sports" class="nav-link">Sports</a></li>
            <li><a href="search.php?q=Art" class="nav-link">Arts, Theater &amp; Comedy</a></li>
            <li><a href="search.php?q=Family" class="nav-link">Family</a></li>
            <li><a href="search.php?q=Cities" class="nav-link">Cities</a></li>
          </ul>
          
        </div>
  
        <div class="flex items-center">
          <a href="auth.php"
             class="flex items-center gap-2 lg:gap-3 text-white hover:text-gray-200 transition-colors duration-200">
            <i class="fa-regular fa-user text-xl"></i>
            <span class="hidden md:inline font-bold text-sm lg:text-base">
              Sign In / Register
            </span>
          </a>
        </div>
  
      </div>
    </div>
  </nav>

  <div id="mobileNavigationDrawer" class="fixed inset-0 z-50 invisible transition-all duration-300">
    <div id="drawerBackdrop" class="absolute inset-0 bg-black/50 opacity-0 transition-opacity duration-300"></div>
    
    <div id="drawerContent" class="absolute inset-y-0 left-0 w-72 max-w-xs bg-white text-gray-900 shadow-2xl transform -translate-x-full transition-transform duration-300 flex flex-col">
      <div class="bg-[#024DDF] text-white px-5 py-4 flex items-center justify-between">
        <span class="font-black text-sm uppercase tracking-wider">Navigation Menu</span>
        <button id="mobileMenuClose" class="text-white hover:text-gray-200 text-xl p-1 focus:outline-none">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <ul class="flex-1 overflow-y-auto font-bold text-sm divide-y divide-gray-100 uppercase tracking-tight">
        <li>
          <a href="index.php" class="flex items-center gap-3 px-5 py-4 bg-blue-50 text-[#024DDF] hover:bg-blue-100 transition-colors">
            <i class="fas fa-home text-base w-5 text-center"></i> Homepage Portal
          </a>
        </li>
        <li>
          <a href="dashboard.php" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 text-gray-800 transition-colors">
            <i class="fas fa-columns text-base w-5 text-center text-gray-400"></i> User Dashboard
          </a>
        </li>
        <li>
          <a href="search.php?q=Concerts" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 text-gray-800 transition-colors">
            <i class="fas fa-music text-base w-5 text-center text-gray-400"></i> Concerts
          </a>
        </li>
        <li>
          <a href="search.php?q=Sports" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 text-gray-800 transition-colors">
            <i class="fas fa-football-ball w-5 text-center text-gray-400"></i> Sports
          </a>
        </li>
        <li>
          <a href="search.php?q=Art" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 text-gray-800 transition-colors">
            <i class="fas fa-theater-masks w-5 text-center text-gray-400"></i> Arts &amp; Theater
          </a>
        </li>
        <li>
          <a href="search.php?q=Family" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 text-gray-800 transition-colors">
            <i class="fas fa-child w-5 text-center text-gray-400"></i> Family
          </a>
        </li>
        <li>
          <a href="search.php?q=Cities" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 text-gray-800 transition-colors">
            <i class="fas fa-city w-5 text-center text-gray-400"></i> Cities Index
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="bg-[#024DDF] pt-2 pb-4 md:pt-4 md:pb-6">
    <div class="max-w-4xl mx-auto px-4 md:px-6">
  
      <div class="bg-white text-gray-900 rounded-2xl shadow-lg p-1 max-w-full mx-auto relative">
  
        <form action="search.php" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center">
  
          <div class="flex flex-row w-full flex-1 divide-x divide-gray-200">
  
            <div class="flex items-center gap-3 px-4 py-2.5 flex-1">
              <i class="fas fa-map-marker-alt text-[#024DDF] text-xl"></i>
              <div class="w-full">
                <label class="text-[10px] uppercase font-black tracking-wider text-gray-400 block">Location</label>
                <input type="text" 
                       name="location" 
                       value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>"
                       placeholder="City or Zip Code"
                       class="bg-transparent outline-none w-full text-xs font-bold text-gray-800 placeholder-gray-400">
              </div>
            </div>
  
            <div class="flex items-center gap-3 px-4 py-2.5 flex-1 cursor-pointer hover:bg-gray-50/50 transition-colors rounded-r-2xl md:rounded-none">
              <i class="fas fa-calendar-alt text-[#024DDF] text-xl"></i>
              <div>
                <label class="text-[10px] uppercase font-black tracking-wider text-gray-400 block">Dates</label>
                <span class="text-xs block text-gray-800 font-bold">All Dates Calendar</span>
              </div>
              <i class="fas fa-chevron-down text-gray-400 text-xs ml-auto"></i>
            </div>
  
          </div>
  
          <div class="flex items-center gap-3 px-4 py-3 flex-1 md:flex-[1.5] border-t md:border-t-0 border-gray-150">
            <i class="fas fa-search text-[#024DDF] text-xl"></i>
            <input type="text"
                   name="q"
                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                   placeholder="Artist, Event or Venue"
                   class="bg-transparent outline-none flex-1 text-xs font-bold text-gray-800 placeholder-gray-400"
                   required>
          </div>
  
          <div class="p-1 md:p-0 shrink-0">
            <button type="submit"
                    class="w-full md:w-auto bg-[#024DDF] hover:bg-[#013ba8] text-white px-6 py-3.5 rounded-xl font-black uppercase tracking-wider text-xs flex items-center justify-center gap-2 transition-colors">
              <i class="fas fa-search"></i> Search Events
            </button>
          </div>
  
        </form>
      </div>
    </div>
  </div>

  <script>
    const trigger = document.getElementById('mobileMenuTrigger');
    const closeBtn = document.getElementById('mobileMenuClose');
    const backdrop = document.getElementById('drawerBackdrop');
    const drawer = document.getElementById('mobileNavigationDrawer');
    const content = document.getElementById('drawerContent');

    function openMobileMenu() {
      drawer.classList.remove('invisible');
      // Trigger animations simultaneously via microscopic execution delay mappings
      setTimeout(() => {
        backdrop.classList.add('opacity-100');
        content.classList.remove('-translate-x-full');
      }, 10);
    }

    function closeMobileMenu() {
      backdrop.classList.remove('opacity-100');
      content.classList.add('-translate-x-full');
      
      // Clean visible window layout spaces upon completion tracking loops
      setTimeout(() => {
        drawer.classList.add('invisible');
      }, 300);
    }

    // Assign listeners arrays
    trigger.addEventListener('click', openMobileMenu);
    closeBtn.addEventListener('click', closeMobileMenu);
    backdrop.addEventListener('click', closeMobileMenu);
  </script>

</body>
</html>
