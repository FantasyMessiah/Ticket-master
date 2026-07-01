<?php
session_start();
require_once "config/db.php";

$pdo = (new Database())->connect();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}

$email = trim($_POST["email"]);
$password = $_POST["password"];

if (!$email || !$password) {
    die("Email and password required");
}

/* -------------------------
   FETCH USER
--------------------------*/
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Invalid credentials");
}

/* -------------------------
   VERIFY PASSWORD
--------------------------*/
if (!password_verify($password, $user["password_hash"])) {
    die("Invalid credentials");
}

/* -------------------------
   SESSION LOGIN
--------------------------*/
$_SESSION["user_id"] = $user["id"];
$_SESSION["email"] = $user["email"];

/* -------------------------
   REDIRECT LOGIC (RETURN TO PREVIOUS PAGE)
--------------------------*/
$redirect = $_SESSION["redirect_after_auth"] ?? "booking.php";
unset($_SESSION["redirect_after_auth"]);

header("Location: $redirect");
exit;
