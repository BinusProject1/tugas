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
    <title>Home</title>
    <link rel="stylesheet" href="user.css">
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
            <a class="menu" href="book.php">Book</a>
            <a class="menu" href="#">History</a>
            <a class="menu" href="#">Finacial</a>
        </div>
        <div>
            <a class="menu" onclick="window.location.href='login/logout.php'">LogOut</a>
        </div>
     </nav>
    <!-- side navbar end -->
    
    <!-- Main Content Start -->
    <main class="content">
        <h1>WELCOME TO B LIBRARY, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <p>Ini adalah halaman utama perpustakaan Anda.</p>

        <section class="recomendation" id="recomendation">
            <h1>Book recomendation</h1>
            <div class="recomendation-content">
                <?php
                    $json_data = file_get_contents('novel.json');
                    $books = json_decode($json_data, true)['books'];
                    $recommended_books = array_slice($books, 0, 15);

                    foreach ($recommended_books as $book) {
                        echo '<div class="recomendation-card">';
                        echo '<img src="' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                        echo '</div>';
                    }
                ?>
            </div>
        </section>

    </main>
    <!-- Main Content End -->
</body>
</html>