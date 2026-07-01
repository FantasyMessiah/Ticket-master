<?php
require_once "inc/countries.php";
?>
<!DOCTYPE html>
<html lang="en">

<?php include "inc/head.php"; ?>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">

<div class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <!-- HEADER -->
        <div class="p-6 text-center bg-white border-b">
            <h1 class="text-2xl font-black text-gray-900">Welcome Back</h1>
            <p class="text-sm text-gray-500 mt-1">
                Sign in or create an account to continue
            </p>
        </div>

        <!-- TABS -->
        <div class="flex relative bg-white border-b">

            <button onclick="showLogin()"
                id="loginTab"
                class="w-1/2 py-3 text-sm font-black text-[#024DDF] bg-blue-50 transition-all">
                Sign In
            </button>

            <button onclick="showRegister()"
                id="registerTab"
                class="w-1/2 py-3 text-sm font-black text-gray-500 hover:bg-gray-50 transition-all">
                Create Account
            </button>

            <!-- active underline indicator -->
            <div id="tabIndicator"
                class="absolute bottom-0 left-0 w-1/2 h-[3px] bg-[#024DDF] transition-all"></div>

        </div>

        <!-- ================= LOGIN ================= -->
        <form id="loginForm" action="login.php" method="POST" class="p-6 space-y-4">

            <input name="email" type="email" placeholder="Email Address"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

            <input name="password" type="password" placeholder="Password"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

            <button class="w-full bg-[#024DDF] text-white font-black py-3 rounded-lg
                           hover:bg-blue-800 transition shadow-sm">
                Sign In
            </button>

        </form>

        <!-- ================= REGISTER ================= -->
        <form id="registerForm" action="register.php" method="POST"
              class="p-6 space-y-4 hidden">

            <input name="full_name" type="text" placeholder="Full Name"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

            <input name="email" type="email" placeholder="Email Address"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

            <!-- COUNTRY -->
            <select name="country" id="countrySelect"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

                <option value="">Select Country</option>

                <?php foreach ($countries as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>">
                        <?= htmlspecialchars($c) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <!-- COUNTRY CODE -->
            <select name="country_code" id="codeSelect"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">
                <option value="">Code</option>
            </select>

            <!-- PHONE -->
            <input name="phone" type="tel" placeholder="Phone Number"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

            <input name="password" type="password" placeholder="Password"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

            <input name="confirm_password" type="password" placeholder="Confirm Password"
                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#024DDF]/30 focus:border-[#024DDF]">

            <button class="w-full bg-[#024DDF] text-white font-black py-3 rounded-lg
                           hover:bg-blue-800 transition shadow-sm">
                Create Account
            </button>

        </form>

    </div>

</div>

<script>
/* -----------------------------
   TAB SWITCHING (WITH ANIMATION)
------------------------------*/
function showLogin() {

    document.getElementById("loginForm").classList.remove("hidden");
    document.getElementById("registerForm").classList.add("hidden");

    document.getElementById("loginTab").classList.add("bg-blue-50", "text-[#024DDF]");
    document.getElementById("registerTab").classList.remove("bg-blue-50", "text-[#024DDF]");

    document.getElementById("tabIndicator").style.left = "0%";
}

function showRegister() {

    document.getElementById("registerForm").classList.remove("hidden");
    document.getElementById("loginForm").classList.add("hidden");

    document.getElementById("registerTab").classList.add("bg-blue-50", "text-[#024DDF]");
    document.getElementById("loginTab").classList.remove("bg-blue-50", "text-[#024DDF]");

    document.getElementById("tabIndicator").style.left = "50%";
}

/* -----------------------------
   COUNTRY → CODE MAP
------------------------------*/
const countryCodes = {
    "Nigeria": "+234",
    "Ghana": "+233",
    "Kenya": "+254",
    "South Africa": "+27",
    "United States": "+1",
    "Canada": "+1",
    "United Kingdom": "+44",
    "India": "+91",
    "Germany": "+49",
    "France": "+33",
    "Australia": "+61"
};

/* -----------------------------
   UPDATE CODE
------------------------------*/
document.getElementById("countrySelect").addEventListener("change", function () {
    const code = countryCodes[this.value] || "";
    document.getElementById("codeSelect").innerHTML =
        `<option value="${code}">${code}</option>`;
});

/* -----------------------------
   GEOLOCATION DETECT
------------------------------*/
function detectCountry() {

    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(async function (pos) {

        try {

            const res = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`
            );

            const data = await res.json();

            const country = data.address.country;

            if (country) {

                const select = document.getElementById("countrySelect");
                select.value = country;

                select.dispatchEvent(new Event("change"));
            }

        } catch (e) {
            console.log("Geo detection failed");
        }
    });
}

detectCountry();
</script>

</body>
</html>
