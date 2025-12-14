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
        <h1>Borrowing History</h1>

        <div class="history-container">
            <?php
            $history_file = '../borrowing_history.json';
            $all_history = json_decode(file_get_contents($history_file), true);
            $user_history = [];
            if ($all_history) {
                foreach ($all_history as $record) {
                    if ($record['user_email'] == $_SESSION['email'] && $record['status'] != 'returned') {
                        $user_history[] = $record;
                    }
                }
            }

            if (!empty($user_history)):
            ?>
                <div class="book-grid">
                    <?php foreach (array_reverse($user_history) as $record): ?>
                        <div class="book-card">
                            <img src="<?= htmlspecialchars($record['book_image'] ?? 'https://via.placeholder.com/150x220.png?text=No+Image') ?>" alt="<?= htmlspecialchars($record['book_title']) ?>" class="book-cover">
                            <div class="book-info">
                                <h3><?= htmlspecialchars($record['book_title']) ?></h3>
                                <p>Dipinjam: <?= date('d M Y', strtotime($record['borrow_date'])) ?></p>
                                <p>Jatuh Tempo: <?= date('d M Y', strtotime($record['due_date'])) ?></p>
                                <p>Status: <span class="<?= $record['status'] == 'borrowed' && strtotime('now') > strtotime($record['due_date']) ? 'overdue' : '' ?>"><?= $record['status'] == 'borrowed' && strtotime('now') > strtotime($record['due_date']) ? 'OVERDUE' : strtoupper($record['status']) ?></span></p>
                            </div>
                        </div>
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