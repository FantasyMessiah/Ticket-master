<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="en">

<?php include "inc/head.php"; ?>
<?php include "inc/navbar.php"; ?> 
 
<body>
        
<?php include "inc/header.php"; ?>
<?php include "body.php"; ?>        
<?php include "inc/footer.php"; ?>

</div>

<div id="modals" data-testid="modals">
</div>
</body>
</html>
