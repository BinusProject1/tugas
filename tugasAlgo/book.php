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
    <title>Book</title>
    <link rel="stylesheet" href="book.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- side navbar start -->
     <nav>
        <div class="profile">
            <i class="material-icons">account_circle</i>
            <h1><?= $_SESSION['name']; ?></h1>
            <a class="detail" href="profile.php">see profile</a>
        </div>
        <div>
            <a class="menu" href="#">HOME</a>
            <a class="menu" href="#">Book</a>
            <a class="menu" href="#">History</a>
            <a class="menu" href="#">Finacial</a>
        </div>
        <div>
            <a class="menu" onclick="window.location.href='login/logout.php'">LogOut</a>
        </div>
     </nav>
    <!-- side navbar end -->
    <!-- serch bar start -->
    <div class="search"><i class="material-icons">search</i>
    </div>
    <!-- serch bar end -->
</body>
</html>