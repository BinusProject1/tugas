<?php
session_start();
include_once 'navbar.php';
if (!isset($_SESSION['email'])) {
    header("location: login/login.php");
    exit();
}

// Fungsi untuk mencari buku berdasarkan ID dari berbagai file JSON
function find_book_by_id($book_id) {
    // Gabungkan semua data buku dari file-file JSON
    $novel_data = json_decode(file_get_contents('fiction.json'), true);
    $knowledge_data = json_decode(file_get_contents('non_fiction.json'), true);
    $all_books = array_merge($novel_data['books'], $knowledge_data['books']);

    // Cari buku dengan ID yang cocok
    foreach ($all_books as $book) {
        if ($book['id'] == $book_id) {
            return $book;
        }
    }
    return null; // Kembalikan null jika buku tidak ditemukan
}

// Handle borrowing logic
$borrow_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow'])) {
    $borrowed_book_id = $_POST['book_id'];
    $book_to_borrow = find_book_by_id($borrowed_book_id);

    if ($book_to_borrow) {
        if (!isset($_SESSION['history'])) {
            $_SESSION['history'] = [];
        }

        // Cek apakah buku sudah ada di riwayat
        $is_in_history = array_search($borrowed_book_id, array_column($_SESSION['history'], 'id')) !== false;

        if (!$is_in_history) {
            $_SESSION['history'][] = $book_to_borrow;
            $borrow_message ="Successfully added to you borrowing history.";
        } else {
            $borrow_message = "This book is already in your borrowing history.";
        }
    }
}

$book_id = $_GET['id'] ?? null;
$book = null;

if ($book_id) {
    $book = find_book_by_id($book_id);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book ? htmlspecialchars($book['title']) : 'Book Not Found' ?></title>
    <link rel="stylesheet" href="book_detail.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php generate_navbar('Book'); ?>

    <main class="content">
        <?php if (!empty($borrow_message)): ?>
            <p class="borrow-success-message"><?= $borrow_message ?></p>
        <?php endif; ?>
        <?php if ($book): ?>
            <div class="book-detail-container">
                <div class="book-image">
                    <img src="<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                </div>
                <div class="book-details">
                    <h1><?= htmlspecialchars($book['title']) ?></h1>
                    <h2>by <?= htmlspecialchars($book['author']) ?></h2>
                    <p><strong>Publisher:</strong> <?= htmlspecialchars($book['publisher']) ?></p>
                    <p><strong>Year:</strong> <?= htmlspecialchars($book['year']) ?></p>
                    <strong>Synopsis:</strong>
                    <p class="synopsis"><?= nl2br(htmlspecialchars($book['sinopsis'])) ?></p>
                    <form method="post" action="book_detail.php?id=<?= urlencode($book['id']) ?>">
                        <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
                        <button type="submit" name="borrow" class="borrow-btn">Pinjam Buku</button>
                    </form>
                </div>
            </div>
            <div class="other-book">
                <div class="head-title">
                    <h2>
                        Other books
                    </h2>
                </div>
                <div class="recomendation-card">
                    <div class="recomendation-img">
                        <img src="https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/picture_meta/2023/3/26/ih86gbr4urzmibs3ah49hq.jpg">
                        <img src="https://cdn.gramedia.com/uploads/items/bumi-manusia-edit.jpg" alt="">                        
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="no-results">Book not found or invalid ID.</p>
            <p class="no-results">Buku tidak ditemukan atau ID tidak valid.</p>
        <?php endif; ?>
    </main>
</body>
</html>