<?php

session_start();
include_once './user_file/navbar.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: ./login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book</title>
    <link rel="stylesheet" href="book_admin.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- side navbar start -->
     <?php generate_navbar('Book', true); ?>
    <!-- side navbar end -->
    <!-- serch bar start -->
    <main class="content">
        <div class="search-filter-container">
            <form action="book_admin.php" method="get" class="search-form">
                <div class="search">
                    <i class="material-icons">search</i>
                    <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </form>
            <div class="filter-container">
                <button class="filter-button" id="filter-btn">
                    <i class="material-icons">filter_list</i>
                    <span>Filter</span>
                </button>
                <div class="filter-dropdown" id="filter-dropdown">
                    <a href="?search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=title_asc">Titel (A-Z)</a>
                    <a href="?search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=year_desc">Year of Publication(Newest)</a>
                    <a href="?search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=year_asc">Year of Publication(Latest)</a>
                </div>
            </div>
            <a href="add_book.php" class="add-book-btn">
                <i class="material-icons">add</i>
                <span>Add book</span>
            </a>
        </div>
    <!-- serch bar end -->
    
    <!-- all book show -->
    <div class="book-container">
        <?php
            // Load and decode JSON files
            $all_books = [];
            $fiction_books = json_decode(file_get_contents('./data_buku/fiction.json'), true)['books'] ?? [];
            $non_fiction_books = json_decode(file_get_contents('./data_buku/non_fiction.json'), true)['books'] ?? [];

            // Buat ID unik dengan format 'genre-id'
            foreach ($fiction_books as $book) {
                $book['id'] = 'fiction-' . $book['id'];
                $all_books[] = $book;
            }
            foreach ($non_fiction_books as $book) {
                $book['id'] = 'non-fiction-' . $book['id'];
                $all_books[] = $book;
            }

            // Filter books based on search query
            $search_query = strtolower(trim($_GET['search'] ?? ''));
            $sort_option = $_GET['sort'] ?? '';

            $filtered_books = $all_books;

            if (!empty($search_query)) {
                $filtered_books = array_filter($all_books, function($book) use ($search_query) {
                    return str_contains(strtolower($book['title']), $search_query) || str_contains(strtolower($book['author']), $search_query);
                });
            }

            // Sort books based on filter option
            if ($sort_option) {
                usort($filtered_books, function($a, $b) use ($sort_option) {
                    if ($sort_option === 'title_asc') {
                        return strcmp($a['title'], $b['title']);
                    }
                    if ($sort_option === 'year_asc') {
                        return $a['year'] <=> $b['year'];
                    }
                    if ($sort_option === 'year_desc') {
                        return $b['year'] <=> $a['year'];
                    }
                    return 0;
                });
            }

            // Display books
            if (count($filtered_books) > 0) {
                foreach ($filtered_books as $book) {
                    echo '<a href="admin_book_detail.php?id=' . urlencode($book['id']) . '" class="book-card-link">';
                    echo '<div class="book-card">';
                    echo '<img src="' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                    echo '<div class="book-info">';
                    echo '<h3>' . htmlspecialchars($book['title']) . '</h3>';
                    echo '<p>' . htmlspecialchars($book['author']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                }
            } else {
                echo '<p class="no-results">No books found./p>';
                echo '<p class="no-results">Tidak ada buku yang ditemukan.</p>';
            }
        ?>
    </div>
    <!-- all book show -->
    </main>
    
    <script src="login/login.js"></script>
</body>

</html>