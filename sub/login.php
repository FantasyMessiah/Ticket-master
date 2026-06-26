<?php
session_start();
include 'db.php';
$site_name = getSetting('site_name', $conn);
$alert_out = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($res && $row = $res->fetch_assoc()) {
        if(password_verify($password, $row['password'])) {
            $_SESSION['user_auth'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: index.php");
            exit;
        }
    }
    $alert_out = "<div class='card' style='background:#F8D7DA; color:#842029; padding:12px; margin-bottom:15px;'>Authentication authorization reject signature error match coordinates invalid.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorize Profile Connection Gateway | <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background:var(--dark); display:flex; align-items:center; height:100vh;">
<div class="container" style="max-width:400px;">
    <?php echo $alert_out; ?>
    <div class="card" style="padding:25px; background:white;">
        <h2 style="font-weight:800; text-align:center; color:var(--dark);">Welcome Back Platform Portal</h2>
        <p style="text-align:center; font-size:0.8rem; color:var(--gray); margin-bottom:20px;">Input authorization access key to entry active workspace pipelines.</p>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Profile Registered Electronic Email Coordinates</label>
                <input type="email" name="email" class="form-control" placeholder="name@domain.com" required>
            </div>
            <div class="form-group">
                <label>Security Vault Account Password Key</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn" style="margin-top:10px;">Authorize Workspace Integration</button>
        </form>
        <p style="font-size:0.8rem; text-align:center; margin-top:15px; color:var(--gray);">New identity unit cluster mapping? <a href="register.php" style="color:var(--primary); font-weight:700; text-decoration:none;">Register Identity Matrix</a></p>
    </div>
</div>
</body>
</html>
