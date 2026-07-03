<?php
// login.php - Handles structural credential matching and the 'YES' modal selection flow
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config/db.php";

$pdo = (new Database())->connect();

/* -------------------------------------------------------------------------
   1. MODAL ROUTING LINK FORK (Catching the 'YES' Response Click from auth.php)
   ------------------------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action']) && $_GET['action'] === 'login_sim') {
    $email = trim($_GET['email'] ?? '');
    
    if (empty($email)) {
        $_SESSION['auth_error'] = "Authentication failed. No email context provided.";
        header("Location: auth.php");
        exit;
    }

    // Lookup structural registration existence
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Hydrate session targets to trigger header update states
        $_SESSION["user_id"] = $user['id'];
        $_SESSION["email"] = $user['email'];

        // Choose fallback pathing securely
        $redirect = !empty($_GET['redirect']) ? htmlspecialchars_decode($_GET['redirect']) : "auth/dashboard.php";
        header("Location: " . $redirect);
        exit;
    } else {
        // If they said YES but do not exist in your DB, send back with alert notice
        $_SESSION['auth_error'] = "No account found matching '$email'. Please choose 'NO' to register.";
        header("Location: auth.php");
        exit;
    }
}

/* -------------------------------------------------------------------------
   2. STANDARD POST FALLBACK HANDLING (If processing explicit field inputs)
   ------------------------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['auth_error'] = "Invalid request method.";
    header("Location: auth.php");
    exit;
}

$email = trim($_POST["email"] ?? '');
$password = $_POST["password"] ?? '';

if (!$email || !$password) {
    $_SESSION['auth_error'] = "Email and password are required fields.";
    header("Location: auth.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user["password_hash"])) {
    $_SESSION['auth_error'] = "The credentials you entered do not match our records.";
    header("Location: auth.php");
    exit;
}

// Successful Verification Login Hydration
$_SESSION["user_id"] = $user["id"];
$_SESSION["email"] = $user["email"];

$redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : "auth/dashboard.php";
header("Location: " . $redirect);
exit;
