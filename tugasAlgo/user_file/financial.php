<?php
session_start();
include_once 'navbar.php';
if (!isset($_SESSION['email'])) {
    header("location: ../login/login.php");
    exit();
}

// Logika untuk menghitung denda
$total_fine = 0;
$overdue_books = [];
$fine_per_book = 10000; // Denda Rp 10.000 per buku (bisa disesuaikan)

$history_file = '../borrowing_history.json';
if (file_exists($history_file)) {
    $all_history = json_decode(file_get_contents($history_file), true);
    if (is_array($all_history)) {
        foreach ($all_history as $record) {
            // Cari buku yang dipinjam oleh user saat ini dan statusnya masih 'borrowed'
            if ($record['user_email'] == $_SESSION['email'] && $record['status'] == 'borrowed') {
                $due_date = strtotime($record['due_date']);
                $now = time();

                // Cek apakah buku sudah melewati jatuh tempo
                if ($now > $due_date) {
                    $total_fine += $fine_per_book;
                    $overdue_books[] = [
                        'title' => $record['book_title'],
                        'due_date' => $record['due_date'],
                        'fine' => $fine_per_book
                    ];
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial</title>
    <link rel="stylesheet" href="user.css"> <!-- Re-use user.css -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .financial-summary {
            background-color: #fff1f0;
            border: 1px solid #ffccc7;
            color: #cf1322;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .financial-summary h2 {
            margin-top: 0;
            font-size: 1.2rem;
            font-weight: normal;
        }
        .financial-summary .total-fine {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .fine-details h2, .payment-info h2 {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f8f8;
        }
        .payment-info {
            background-color: #e6f7ff;
            border: 1px solid #91d5ff;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0px;
        }
    </style>
</head>
<body>
    <?php generate_navbar('Financial'); ?>

    <main class="content">
        <h1>Financial Overview</h1>

        <div class="financial-summary">
            <h2>Current Total Fines</h2>
            <p class="total-fine">Rp <?= number_format($total_fine, 0, ',', '.') ?></p>
        </div>

        <div class="fine-details">
            <h2>Details of Fines</h2>
            <?php if (!empty($overdue_books)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Due Date</th>
                            <th>Fines</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdue_books as $book): ?>
                            <tr>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= date('d M Y', strtotime($book['due_date'])) ?></td>
                                <td>Rp <?= number_format($book['fine'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You don't have any fines. Thank you for returning the book on time.</p>
            <?php endif; ?>
        </div>

        <div class="payment-info">
            <h2>Payment Information</h2>
            <p>To pay your fine, please visit the library service counter. Show this page to the staff to facilitate the payment process.</p>
        </div>
    </main>
</body>
</html>
