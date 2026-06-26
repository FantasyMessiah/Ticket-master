<?php
session_start();
include 'db.php';
$site_name = getSetting('site_name', $conn);
$alert_out = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $chk = $conn->query("SELECT id FROM users WHERE email='$email'");
    if($chk && $chk->num_rows > 0) {
        $alert_out = "<div class='card' style='background:#F8D7DA; color:#842029; padding:12px;'>Identity tracking duplicate allocation entry error: Email record exists.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $pass);
        if($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $_SESSION['user_auth'] = true;
            $_SESSION['user_id'] = $new_id;
            $_SESSION['user_name'] = $name;
            
            // Render High Fidelity Direct JavaScript System Pop Alert Banner Configuration Notice Module
            $alert_out = "
            <script>
            alert('Profile Framework Activation Node Sequence Success! Operational dispatch token notification has been dispatched from our system administrative support center core: Contact.MasterTickets.Official@gmail.com.');
            window.location.href='index.php';
            </script>";
        } else {
            $alert_out = "<div class='card' style='background:#F8D7DA; color:#842029; padding:12px;'>Internal storage structure write execution failure exception.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Dynamic Account Profile | <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background:var(--dark); display:flex; align-items:center; min-height:100vh; padding:20px 0;">
<div class="container" style="max-width:420px;">
    <?php echo $alert_out; ?>
    <div class="card" style="padding:25px; background:white;">
        <h2 style="font-weight:800; text-align:center; color:var(--dark);">Initialize Secure Identity Profile</h2>
        <p style="text-align:center; font-size:0.8rem; color:var(--gray); margin-bottom:20px;">Unlock full P2P marketplace transaction execution pipelines instantly.</p>
        
        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Legal Identification Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
            </div>
            <div class="form-group">
                <label>Electronic Mailing Coordinates (Email Address)</label>
                <input type="email" name="email" class="form-control" placeholder="name@domain.com" required>
            </div>
            <div class="form-group">
                <label>Active Mobile Link Telephone Line</label>
                <input type="text" name="phone" class="form-control" placeholder="+234..." required>
            </div>
            <div class="form-group">
                <label>Security Encrypted Access Password Signature</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn" style="margin-top:10px;">Activate Identity Access Matrix</button>
        </form>
        <p style="font-size:0.8rem; text-align:center; margin-top:15px; color:var(--gray);">Already structured registered profile? <a href="login.php" style="color:var(--primary); font-weight:700; text-decoration:none;">Log In Here</a></p>
    </div>
</div>
</body>
</html>
