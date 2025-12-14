<?php

session_start();
include_once 'navbar.php';
if(!isset($_SESSION['email'])){
    header("location: ../login/login.php");
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
    <?php generate_navbar('HOME'); ?>
    <!-- side navbar end -->
    
    <!-- Main Content Start -->
    <main class="content">
        <h1>WELCOME TO B LIBRARY, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>

        <section class="recomendation" id="recomendation">
            <h1>Fiction</h1>
            <div class="recomendation-content">
                <?php
                    $json_data = file_get_contents('../data_buku/fiction.json');
                    $books = json_decode($json_data, true)['books'];
                    $recommended_books = array_slice($books, 0, 15);

                    foreach ($recommended_books as $book) {
                        echo '<a href="book_detail.php?id=' . urlencode('fiction-' . $book['id']) . '">';
                        echo '<div class="recomendation-card">';
                        echo '<img src="' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                        echo '</div>';
                        echo '</a>';
                    }
                ?>
            </div>
        </section>

        <section class="recomendation" id="recomendation">
            <h1>Non-Fiction</h1>
            <div class="recomendation-content">
                <?php
                    $json_data = file_get_contents('../data_buku/non_fiction.json');
                    $books = json_decode($json_data, true)['books'];
                    $recommended_books = array_slice($books, 0, 15);

                    foreach ($recommended_books as $book) {
                        echo '<a href="book_detail.php?id=' . urlencode('non-fiction-' . $book['id']) . '">';
                        echo '<div class="recomendation-card">';
                        echo '<img src="' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                        echo '</div>';
                        echo '</a>';
                    }
                ?>
            </div>
        </section>

        <section class="recomendation" id="recomendation">
            <h1>Most Read </h1>
            <div class="recomendation-content">
                <?php
                    $json_data = file_get_contents('../data_buku/most_read.json');
                    $books = json_decode($json_data, true)['books'];
                    $recommended_books = array_slice($books, 0, 15);

                    foreach ($recommended_books as $book) {
                        echo '<a href="book_detail.php?id=' . urlencode($book['genre'] . '-' . $book['id']) . '">';
                        echo '<div class="recomendation-card">';
                        echo '<img src="' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                        echo '</div>';
                        echo '</a>';
                    }
                ?>
            </div>
        </section>

        <section class="recomendation" id="recomendation">
            <h1>Your Next Read</h1>
            <div class="recomendation-content">
                <?php
                    $json_data = file_get_contents('../data_buku/next_read.json');
                    $books = json_decode($json_data, true)['books'];
                    $recommended_books = array_slice($books, 0, 15);

                    foreach ($recommended_books as $book) {
                        echo '<a href="book_detail.php?id=' . urlencode($book['genre'] . '-' . $book['id']) . '">';
                        echo '<div class="recomendation-card">';
                        echo '<img src="' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                        echo '</div>';
                        echo '</a>';
                    }
                ?>
            </div>
        </section>
    </main>
    <!-- Main Content End -->
</body>
</html>