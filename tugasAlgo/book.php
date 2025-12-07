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
            <a class="menu" href="user_page.php">HOME</a>
            <a class="menu" href="book.php">Book</a>
            <a class="menu" href="#">History</a>
            <a class="menu" href="#">Finacial</a>
        </div>
        <div>
            <a class="menu" onclick="window.location.href='login/logout.php'">LogOut</a>
        </div>
     </nav>
    <!-- side navbar end -->
    <!-- serch bar start -->
    <main class="content">
        <div class="search-filter-container">
            <form action="book.php" method="get" class="search-form">
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
                    <a href="?search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=year_desc">Year of Publication(Latest)</a>
                    <a href="?search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&sort=year_asc">Year of Publication(Latest)</a>
                </div>
            </div>
        </div>
    <!-- serch bar end -->
    
    <!-- all book show -->
    <div class="book-container">
        <?php
            // Load and decode JSON files
            $novel_data = json_decode(file_get_contents('fiction.json'), true);
            $knowledge_data = json_decode(file_get_contents('non_fiction.json'), true);

            // Merge books from both files
            $all_books = array_merge($novel_data['books'], $knowledge_data['books']);

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
                    echo '<div class="book-card">';
                    echo '<img src="' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
                    echo '<div class="book-info">';
                    echo '<h3>' . htmlspecialchars($book['title']) . '</h3>';
                    echo '<p>' . htmlspecialchars($book['author']) . '</p>';
                    echo '</div>';
                    echo '</div>';
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