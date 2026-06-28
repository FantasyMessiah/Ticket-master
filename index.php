<?php
// index.php
require_once 'db.php';

// Dynamically fetch all upcoming events mapped alongside assigned artist details
$stmt = $pdo->query("SELECT e.*, a.name AS artist_name, a.image AS artist_image FROM events e JOIN artists a ON e.artist_id = a.id ORDER BY e.event_date ASC LIMIT 12");
$dynamic_events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<?php include "inc/head.php"; ?>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    <div id="__next">
        <?php include "inc/navbar1.php"; ?> 
        <?php include "inc/navbar2.php"; ?>        
        <?php include "inc/header.php"; ?>
        
        <?php include "body.php"; ?>        
        
        <?php include "inc/footer.php"; ?>
    </div>
