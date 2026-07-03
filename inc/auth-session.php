<?php
session_start();

if (!isset($_SESSION['user_id'])) {

    $_SESSION['auth_error'] = "Please login to continue booking your seats.";
    $_SESSION['redirect_after_auth'] = $_SERVER['REQUEST_URI'];

    header("Location: auth.php");
    exit;
}
