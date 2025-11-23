<?php

session_start();
if(!isset($_SESSION['email'])){
    header("location: login/login.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- navbar start -->
     <nav>
        <div class="logo">
            <a href="#" alt="BINUS JAYA LIBRARY">BJL</a>
        </div>
     </nav>
    <!-- navbar end -->
    
</body>
</html>