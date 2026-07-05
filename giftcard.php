<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: Include your regular website architecture configuration files if needed
// require_once 'config/db.php';

// Dynamic variable definitions (Customize these paths/links for your specific vendor site)
$target_website_url = "https://www.amazon.com/giftcards"; // Replace with your actual affiliate or partner site link
$youtube_video_id   = "dQw4w9WgXcQ"; // Replace with your target YouTube Video ID (e.g., the part after watch?v=)
?>
<!DOCTYPE html>
<html lang="en">

<?php 
// Include headers and navigation menus if they exist in your project structure
if (file_exists("inc/head.php")) { include "inc/head.php"; } else {
    echo '<head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Buy Gift Cards — Step-by-Step Purchase Guide</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
          </head>';
}
?>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    
    <?php if (file_exists("inc/navbar.php")) { include "inc/navbar.php"; } ?> 

    <div id="__next">
        <!-- Hero Header Block Section -->
        <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white py-12 px-4 md:px-8 shadow-md">
            <div class="max-w-5xl mx-auto text-center md:text-left">
                <span class="text-xs font-black uppercase tracking-widest text-blue-400 bg-blue-950/60 px-3 py-1 rounded-full">Official Purchase Hub</span>
                <h1 class="text-3xl md:text-5xl font-black tracking-tight mt-3">
                    How to Buy Your Gift Card
                </h1>
                <p class="text-xs md:text-sm text-gray-300 mt-2 max-w-xl leading-relaxed font-medium mx-auto md:mx-0">
                    Follow our simplified digital onboarding blueprint below to safely buy, secure, and claim valid credit codes instantly through verified merchant platforms.
                </p>
            </div>
        </div>

        <!-- Main Workspace Core Layout Container -->
        <main class="max-w-5xl mx-auto px-4 md:px-8 py-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Grid Layout: Step-by-Step Instructions -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-black text-gray-900 tracking-tight uppercase border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                        <i class="fas fa-list-check text-[#024DDF]"></i> Purchase Instructions
                    </h2>

                    <!-- Step 1 -->
                    <div class="flex gap-4 mb-6 relative">
                        <div class="absolute left-4 top-10 bottom-0 w-0.5 bg-gray-100 hidden sm:block"></div>
                        <div class="w-9 h-9 bg-blue-50 border border-blue-200 text-[#024DDF] font-black rounded-full flex items-center justify-center shrink-0 shadow-sm">
                            1
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Access the Marketplace Portal</h4>
                            <p class="text-xs text-gray-500 leading-relaxed mt-1">
                                Click the primary <span class="font-bold text-gray-700">"Proceed to Purchase Website"</span> button located on the sidebar layout panel to navigate to the secure payment clearing vendor page safely.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex gap-4 mb-6 relative">
                        <div class="absolute left-4 top-10 bottom-0 w-0.5 bg-gray-100 hidden sm:block"></div>
                        <div class="w-9 h-9 bg-blue-50 border border-blue-200 text-[#024DDF] font-black rounded-full flex items-center justify-center shrink-0 shadow-sm">
                            2
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Select Value & Customization</h4>
                            <p class="text-xs text-gray-500 leading-relaxed mt-1">
                                Choose your desired digital denomination capacity or input a manual pricing balance value parameter. Choose between standard eGift delivery variants or dynamic print-at-home models.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex gap-4 mb-6 relative">
                        <div class="absolute left-4 top-10 bottom-0 w-0.5 bg-gray-100 hidden sm:block"></div>
                        <div class="w-9 h-9 bg-blue-50 border border-blue-200 text-[#024DDF] font-black rounded-full flex items-center justify-center shrink-0 shadow-sm">
                            3
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Provide Delivery Configuration</h4>
                            <p class="text-xs text-gray-500 leading-relaxed mt-1">
                                Carefully input the functional receiving destination email box address. Double-check all spellings to ensure the unique digital cryptographic security token prints out to the correct destination account ledger.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex gap-4">
                        <div class="w-9 h-9 bg-blue-50 border border-blue-200 text-[#024DDF] font-black rounded-full flex items-center justify-center shrink-0 shadow-sm">
                            4
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Finalize Payment & Checkout</h4>
                            <p class="text-xs text-gray-500 leading-relaxed mt-1">
                                Execute standard authorization processing via your preferred settlement mechanism (Debit/Credit lines). Code delivery cycles resolve to active processing states within 5-15 window parameters.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert/Notice Banner Notification Box -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
                    <i class="fas fa-circle-exclamation text-amber-500 text-lg mt-0.5 shrink-0"></i>
                    <div>
                        <h5 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider">Important Security Reminder</h5>
                        <p class="text-xs text-amber-700 leading-relaxed mt-0.5">
                            Never disclose complete serialized numeric redemption characters to unverified third-party platforms under any circumstances. Staff operations parameters will never request system authorization passcode arrays.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Grid Layout: Media Embed Container Box & Action Redirect Buttons -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Action Execution Terminal Side Panel Block -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-center">
                    <i class="fas fa-wallet text-4xl text-[#024DDF] mb-3 block"></i>
                    <h3 class="text-lg font-black text-gray-900 tracking-tight uppercase">Ready to Purchase?</h3>
                    <p class="text-xs text-gray-500 mt-1 mb-6 leading-relaxed max-w-xs mx-auto">
                        Redirect instantly to our integrated certified merchant system instance to load your dynamic cash-balance allocations.
                    </p>
                    
                    <a href="<?php echo htmlspecialchars($target_website_url); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="block text-center w-full bg-[#024DDF] hover:bg-blue-800 text-white font-black text-sm uppercase tracking-wider py-4 px-6 rounded-xl transition-all shadow-md focus:outline-none hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        Proceed to Purchase Website <i class="fas fa-external-link-alt text-xs"></i>
                    </a>
                </div>

                <!-- Guided Tutorial Explainer Video Frame Block Module -->
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight mb-3 flex items-center gap-2">
                        <i class="fab fa-youtube text-red-600 text-base"></i> Video Walkthrough Guide
                    </h3>
                    
                    <!-- Aspect Ratio Framework 16:9 Video Box Wrapper Block -->
                    <div class="relative w-full aspect-video rounded-xl overflow-hidden shadow-inner bg-slate-900 border border-gray-100">
                        <iframe 
                            class="absolute top-0 left-0 w-full h-full"
                            src="https://www.youtube.com/embed/<?php echo htmlspecialchars($youtube_video_id); ?>?rel=0" 
                            title="YouTube video player" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    
                    <p class="text-[11px] text-gray-400 mt-3 text-center italic leading-relaxed">
                        Having issues processing your transaction window? Watch this quick visual execution demo layout module.
                    </p>
                </div>

            </div>
        </main>

        <?php if (file_exists("inc/footer.php")) { include "inc/footer.php"; } ?>
    </div>
</body>
</html>
