<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Top Navigation</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  
  <style>
    .nav-link {
      transition: color 0.2s ease;
    }
    .nav-link:hover {
      color: #fff;
    }
  </style>
</head>
<body class="bg-white text-gray-900">

  <!-- Navbar1 - Dark Background -->
  <div class="bg-[#121212] text-white border-b border-gray-800 h-10 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between text-sm">
  
      <!-- Left: Country Selector -->
      <div>
        <a href="countries.php"
           class="flex items-center gap-2 px-4 h-full rounded-lg border border-gray-600 hover:border-gray-500 transition-all hover:bg-gray-800">
          <svg width="22" height="22" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- SVG omitted for brevity -->
          </svg>
          <span class="font-semibold">US</span>
          <i class="fas fa-chevron-down text-xs ml-1"></i>
        </a>
      </div>
  
      <!-- Right Side -->
      <div class="flex items-center h-full">
  
        <!-- Navigation Links (Desktop Only) -->
        <nav class="hidden md:flex items-center gap-7 font-medium mr-8">
          <a href="#" class="nav-link flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 01-2-2H7a2 2 0 01-2 2v16m14 0h2m-2 0h-5m-4 0H3"/>
            </svg>
            Hotels
          </a>
          <a href="#" class="nav-link">Sell</a>
          <a href="#" class="nav-link flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-1a2 2 0 01-2-2H9a2 2 0 01-2-2v-1a2 2 0 012-2m0 0V9a2 2 0 012-2"/>
            </svg>
            Gift Cards
          </a>
          <a href="#" class="nav-link">Help</a>
          <a href="#" class="nav-link">VIP</a>
        </nav>
  
        <!-- PayPal -->
        <div class="flex items-center h-full md:pl-6 md:border-l border-gray-700">
          <span class="hidden md:inline text-xs text-gray-400 whitespace-nowrap mr-3">
            Preferred Payments Partner
          </span>
  
          <a href="#" class="h-full flex items-center">
            <img src="assets/paypal_small.svg"
                 alt="PayPal"
                 class="h-full w-auto">
          </a>
        </div>
  
      </div>
    </div>
  </div>

</body>
</html>
