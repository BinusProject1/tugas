<?php

session_start();
include_once 'navbar.php';
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
    <title>History</title>
    <link rel="stylesheet" href="history.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- side navbar start -->
     <?php generate_navbar('History'); ?>
    <!-- side navbar end -->

    <!-- Main Content Start -->
    <main class="content">
        <h1>Riwayat Peminjaman</h1>

        <div class="history-container">
            <?php
            // Cek apakah ada riwayat peminjaman di session
            if (isset($_SESSION['history']) && !empty($_SESSION['history'])):
                $borrowed_books = $_SESSION['history'];
            ?>
                <div class="book-grid">
                    <?php foreach ($borrowed_books as $book): ?>
                        <a href="book_detail.php?id=<?= urlencode($book['id']) ?>" class="book-card-link">
                            <div class="book-card">
                                <img src="<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                                <div class="book-info">
                                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                                    <p><?= htmlspecialchars($book['author']) ?></p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-history">Anda belum meminjam buku apapun.</p>
            <?php endif; ?>
        </div>
    </main>
    <!-- Main Content End -->
</body>
</html>