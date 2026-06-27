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
      transition: all 0.2s ease;
    }
    .nav-link:hover {
      color: #fff;
      transform: translateY(-1px);
    }
    .country-btn {
      transition: all 0.2s ease;
    }
    .country-btn:hover {
      background-color: #2a2a2a;
    }
  </style>
</head>
<body class="bg-black text-white">

  <!-- Top Navigation Bar -->
  <div class="bg-[#1a1a1a] border-b border-gray-800 py-3">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
      
      <!-- Country Selector -->
      <div class="flex items-center">
        <button onclick="alert('Country selector would open here')"
                class="country-btn flex items-center gap-2 px-4 py-2 rounded-md border border-gray-700 hover:border-gray-500 text-sm font-medium">
          <svg width="24" height="24" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M503.2 322.8c5.7-21.3 8.8-43.7 8.8-66.8l-8.8-66.8a254.6 254.6 0 0 0-28.8-66.8l-59-66.7A255 255 0 0 0 256 0h-.2A255 255 0 0 0 96.6 55.7l-59 66.7a254.6 254.6 0 0 0-28.8 66.8L0 256v.1c0 23 3 45.4 8.8 66.7l28.8 66.8a257.3 257.3 0 0 0 59 66.7L256 512l159.4-55.7a257.3 257.3 0 0 0 59-66.7z" fill="#FFF"/>
            <path d="M503.2 189.2c5.7 21.3 8.8 43.7 8.8 66.8H0c0-23.1 3-45.5 8.8-66.8zM415.4 55.7a257.3 257.3 0 0 1 59 66.7H37.6a257.3 257.3 0 0 1 59-66.7zm59 333.9c12.6-20.6 22.4-43 28.8-66.8H8.8a254.6 254.6 0 0 0 28.8 66.8zm-59 66.7H96.6A255 255 0 0 0 255.8 512h.4a255 255 0 0 0 159.2-55.7" fill="#D80027"/>
            <path d="M0 245.6A256 256 0 0 1 256 0v256H0z" fill="#0052B4"/>
          </svg>
          <span class="font-medium">US</span>
          <i class="fas fa-chevron-down text-xs"></i>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="flex items-center gap-8 text-sm">
        <a href="#" class="nav-link flex items-center gap-2 font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 01-2-2H7a2 2 0 01-2 2v16m14 0h2m-2 0h-5m-4 0H3" />
          </svg>
          Hotels
        </a>
        
        <a href="#" class="nav-link font-medium">Sell</a>
        
        <a href="#" class="nav-link flex items-center gap-2 font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-1a2 2 0 01-2-2H9a2 2 0 01-2-2v-1a2 2 0 012-2m0 0V9a2 2 0 012-2" />
          </svg>
          Gift Cards
        </a>
        
        <a href="#" class="nav-link font-medium">Help</a>
        
        <a href="#" class="nav-link font-medium text-amber-400 hover:text-amber-300">VIP</a>
      </nav>

      <!-- PayPal Partner -->
      <div class="flex items-center">
        <a href="https://www.paypal.com" target="_blank" class="flex items-center gap-2 text-xs hover:opacity-80 transition-opacity">
          <span class="text-gray-400">Preferred Payments Partner</span>
          <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Paypal_2014_logo.svg" 
               alt="PayPal" class="h-6">
        </a>
      </div>

    </div>
  </div>

</body>
</html>
