<?php
session_start();
include_once './user_file/navbar.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: ./login/login.php");
    exit();
}

// Fungsi untuk mencari buku berdasarkan ID dari berbagai file JSON
function find_book_by_id($book_id) {
    $all_books = [];
    // Menggunakan path relatif yang benar dari lokasi file ini
    $fiction_books = json_decode(file_get_contents('./data_buku/fiction.json'), true)['books'] ?? [];
    $non_fiction_books = json_decode(file_get_contents('./data_buku/non_fiction.json'), true)['books'] ?? [];

    foreach ($fiction_books as $book) {
        $book['id'] = 'fiction-' . $book['id'];
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
$message = '';

if ($book_id) {
    $book = find_book_by_id($book_id);
}

// Logika untuk menghapus buku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book'])) {
    if ($book) {
        // Memecah ID untuk mendapatkan genre dan ID asli
        list($genre, $original_id) = explode('-', $book['id'], 2);
        $file_path = './data_buku/' . $genre . '.json';

        if (file_exists($file_path)) {
            $data = json_decode(file_get_contents($file_path), true);

            // Filter array untuk menghapus buku yang dipilih
            $updated_books = array_filter($data['books'], function($b) use ($original_id) {
                return $b['id'] != $original_id;
            });

            // Re-index array untuk menjaga format JSON array
            $data['books'] = array_values($updated_books);

            // Tulis kembali data yang sudah diperbarui ke file JSON
            file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));

            // Atur pesan sukses dan alihkan ke halaman daftar buku admin
            $_SESSION['message'] = "Buku '" . htmlspecialchars($book['title']) . "' telah berhasil dihapus.";
            header("Location: book_admin.php");
            exit();
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
    <link rel="stylesheet" href="admin_book_detail.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php generate_navbar('Book', true); ?>

    <main class="content">
        <?php if (!empty($message)): ?>
            <p class="borrow-success-message"><?= $message ?></p>
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
                    <!-- Form untuk menghapus buku -->
                    <form method="post" action="admin_book_detail.php?id=<?= urlencode($book['id']) ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini secara permanen?');">
                        <button type="submit" name="delete_book" class="return-btn" style="background-color: #dc3545; border-color: #dc3545;">Hapus Buku</button>
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