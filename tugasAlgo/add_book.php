<?php
session_start();
include_once './user_file/navbar.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: ./login/login.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    $year = filter_var(trim($_POST['year'] ?? ''), FILTER_VALIDATE_INT);
    $image = filter_var(trim($_POST['image'] ?? ''), FILTER_VALIDATE_URL);
    $sinopsis = trim($_POST['sinopsis'] ?? '');
    $genre = trim($_POST['genre'] ?? '');

    // Validasi input
    if (empty($title) || empty($author) || empty($publisher) || $year === false || $image === false || empty($sinopsis) || empty($genre)) {
        $error = "Semua field harus diisi dan URL gambar harus valid.";
    } else {
        $file_path = './data_buku/' . $genre . '.json';

        if (file_exists($file_path)) {
            $data = json_decode(file_get_contents($file_path), true);

            // Cari ID tertinggi secara global untuk generate ID baru
            $max_id = 0;
            $data_files = glob('./data_buku/*.json'); // Ambil semua file .json di folder data_buku

            foreach ($data_files as $file) {
                $json_content = json_decode(file_get_contents($file), true);
                if (isset($json_content['books']) && is_array($json_content['books'])) {
                    foreach ($json_content['books'] as $book) {
                        if (isset($book['id']) && $book['id'] > $max_id) {
                            $max_id = $book['id'];
                        }
                    }
                }
            }

            $new_id = $max_id + 1;

            // Ambil kembali data spesifik untuk file genre yang akan diupdate
            $data = json_decode(file_get_contents($file_path), true);

            // Buat array buku baru
            $new_book = [
                'id' => $new_id,
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher,
                'year' => $year,
                'image' => $image,
                'sinopsis' => $sinopsis,
                'genre' => $genre,
            ];

            // Tambahkan buku baru ke array
            $data['books'][] = $new_book;

            // Simpan kembali ke file JSON
            if (file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
                $_SESSION['message'] = "Buku '" . htmlspecialchars($title) . "' telah berhasil ditambahkan.";
                header("Location: book_admin.php");
                exit();
            } else {
                $error = "Gagal menyimpan data buku. Periksa izin file.";
            }
        } else {
            $error = "File genre tidak ditemukan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Baru</title>
    <link rel="stylesheet" href="admin_book_detail.css"> <!-- Bisa re-use CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .form-container { padding: 20px; max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .submit-btn {
            background-color: #4CAF50; color: white; padding: 10px 15px;
            border: none; border-radius: 4px; cursor: pointer; font-size: 16px;
        }
        .submit-btn:hover { background-color: #45a049; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .message.error { background-color: #f44336; color: white; }
    </style>
</head>
<body>
    <?php generate_navbar('Book', true); ?>

    <main class="content">
        <div class="form-container">
            <h1>Tambah Buku Baru</h1>
            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="add_book.php" method="post">
                <div class="form-group">
                    <label for="title">Judul</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="author">Penulis</label>
                    <input type="text" id="author" name="author" required>
                </div>
                <div class="form-group">
                    <label for="publisher">Penerbit</label>
                    <input type="text" id="publisher" name="publisher" required>
                </div>
                <div class="form-group">
                    <label for="year">Tahun Terbit</label>
                    <input type="number" id="year" name="year" required>
                </div>
                <div class="form-group">
                    <label for="image">URL Gambar</label>
                    <input type="url" id="image" name="image" required>
                </div>
                <div class="form-group">
                    <label for="sinopsis">Sinopsis</label>
                    <textarea id="sinopsis" name="sinopsis" required></textarea>
                </div>
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre" required>
                        <option value="">Pilih Genre</option>
                        <option value="fiction">Fiction</option>
                        <option value="non_fiction">Non-Fiction</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Tambah Buku</button>
            </form>
        </div>
    </main>
</body>
</html>