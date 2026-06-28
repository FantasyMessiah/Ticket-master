<?php
// index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

// Safe Database Fetch Loop
try {
    $stmt = $pdo->query("SELECT e.*, a.name AS artist_name, a.image AS artist_image FROM events e JOIN artists a ON e.artist_id = a.id ORDER BY e.event_date ASC LIMIT 12");
    $dynamic_events = $stmt->fetchAll();
} catch (Exception $e) {
    // If your database tables aren't created yet, fallback to an empty array so the page doesn't crash
    $dynamic_events = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include "inc/head.php"; ?>
<?php include "inc/navbar1.php"; ?> 
 
<body>
    <div id="__next">
        <div class="sc-d727d306-0 llCpYZ">
         Your browser is not supported. For the best experience, use any of these supported browsers:
         <a class=\"Link__StyledLink-sc-pudy0l-0 bfasNL\" href=\"https://www.google.com/chrome/\">Chrome</a>, 
         <a class=\"Link__StyledLink-sc-pudy0l-0 bfasNL\" href=\"https://www.mozilla.org/firefox/new/\">Firefox</a>, 
         <a class=\"Link__StyledLink-sc-pudy0l-0 bfasNL\" href=\"https://support.apple.com/downloads/safari\">Safari</a>, 
         <a class=\"Link__StyledLink-sc-pudy0l-0 bfasNL\" href=\"https://www.microsoft.com/edge\">Edge</a>.
        </div>
        <section class="sc-7f6df46b-0 frSDhw">
         <a class="Link__StyledLink-sc-pudy0l-0 eYZQRC sc-7f6df46b-1 firzHb" href="#main-content">
          Skip to main content
         </a>
        </section>    
        
        <?php include "inc/header.php"; ?>
        <?php include "body.php"; ?>        
        <?php include "inc/footer.php"; ?>
    </div>
</body>
</html>
