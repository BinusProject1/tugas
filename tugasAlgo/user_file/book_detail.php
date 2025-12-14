<?php
session_start();
include_once 'navbar.php';
if (!isset($_SESSION['email'])) {
    header("location: ../login/login.php");
    exit();
}

// Fungsi untuk mencari buku berdasarkan ID dari berbagai file JSON
function find_book_by_id($book_id) {
    $all_books = [];
    // Muat data hanya dari sumber utama untuk menghindari duplikasi
    $fiction_books = json_decode(file_get_contents('../data_buku/fiction.json'), true)['books'] ?? [];
    $non_fiction_books = json_decode(file_get_contents('../data_buku/non_fiction.json'), true)['books'] ?? [];

    foreach ($fiction_books as $book) {
        $book['id'] = 'fiction-' . $book['id']; // ID unik: genre-id
        $all_books[] = $book;
    }
    foreach ($non_fiction_books as $book) {
        $book['id'] = 'non-fiction-' . $book['id']; // ID unik: genre-id
        $all_books[] = $book;
    }

    // Cari buku dengan ID yang cocok
    foreach ($all_books as $book) {
        if ($book['id'] == $book_id) {
            return $book;
        }
    }
    return null; // Kembalikan null jika buku tidak ditemukan
}

$book_id = $_GET['id'] ?? null;
$book = null;
$borrow_message = '';
$is_borrowed_by_user = false;

if ($book_id) {
    $book = find_book_by_id($book_id);
}

// Jika buku ditemukan, lanjutkan dengan logika pinjam/kembali
if ($book) {
    $history_file = '../borrowing_history.json';
    $history_data = file_exists($history_file) ? json_decode(file_get_contents($history_file), true) : [];
    if (!is_array($history_data)) {
        $history_data = []; // Pastikan ini adalah array
    }
    $user_email = $_SESSION['email'];

    // Periksa status peminjaman saat ini untuk pengguna
    foreach ($history_data as $record) {
        if ($record['user_email'] == $user_email && $record['book_id'] == $book_id && $record['status'] == 'borrowed') {
            $is_borrowed_by_user = true;
            break;
        }
    }
}

// Handle borrowing logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow'])) {
    if ($book && !$is_borrowed_by_user) {
            $borrow_date = date('Y-m-d H:i:s');
            $due_date = date('Y-m-d H:i:s', strtotime('+3 minutes'));

            $history_data[] = ['user_email' => $user_email, 'book_id' => $book_id, 'book_title' => $book['title'], 'book_image' => $book['image'], 'borrow_date' => $borrow_date, 'due_date' => $due_date, 'status' => 'borrowed'];
            file_put_contents($history_file, json_encode($history_data, JSON_PRETTY_PRINT));

            $borrow_message ="Successfully added to you borrowing history.";
            $is_borrowed_by_user = true; // Update status setelah berhasil meminjam
    } else {
        $borrow_message = "You have already borrowed this book.";
    }
}

// Handle return logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return'])) {
    if ($book && $is_borrowed_by_user) {
        $updated_history = [];
        $book_returned = false;
        foreach ($history_data as $record) {
            // Cari catatan peminjaman yang aktif untuk dihapus
            if ($record['user_email'] == $user_email && $record['book_id'] == $book_id && $record['status'] == 'borrowed' && !$book_returned) {
                // Lewati rekaman ini untuk menghapusnya dari riwayat
                $book_returned = true;
            } else {
                // Simpan rekaman lainnya
                $updated_history[] = $record;
            }
        }

        if ($book_returned) {
            file_put_contents($history_file, json_encode($updated_history, JSON_PRETTY_PRINT));
            $borrow_message = "Buku berhasil dikembalikan dan data peminjaman telah dihapus.";
            $is_borrowed_by_user = false; // Update status setelah berhasil mengembalikan
        }
    }
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
                    <?php if ($is_borrowed_by_user): ?>
                        <form method="post" action="book_detail.php?id=<?= urlencode($book['id']) ?>">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
                            <button type="submit" name="return" class="return-btn">Kembalikan Buku</button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="book_detail.php?id=<?= urlencode($book['id']) ?>">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
                            <button type="submit" name="borrow" class="borrow-btn">Pinjam Buku</button>
                        </form>
                    <?php endif; ?>
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