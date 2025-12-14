<?php
$conn = mysqli_connect("localhost", "root", "", "user_client");
if (!$conn) {
    die("connection feild: " . mysqli_connect_error());
}

session_start();
include_once './user_file/navbar.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("location: ./login/login.php"); // Pastikan role adalah admin
    exit();
}

// --- PEMROSESAN DATA ---
$sql = "SELECT id, name, email, role FROM users WHERE role != 'admin'";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);

$history_file = './borrowing_history.json';
$borrowing_data = file_exists($history_file) ? json_decode(file_get_contents($history_file), true) : [];
if (!is_array($borrowing_data)) $borrowing_data = [];

$grand_total_fine = 0;
$total_borrowed_books = 0;
$fine_per_book = 10000;

// Menghitung total denda dan buku yang dipinjam
foreach ($borrowing_data as $record) {
    if ($record['status'] == 'borrowed') {
        $total_borrowed_books++;
        if (strtotime('now') > strtotime($record['due_date'])) {
            $grand_total_fine += $fine_per_book;
        }
    }
}

// Menyiapkan data pengguna untuk ditampilkan di tabel
$user_table_data = [];
foreach ($users as $user) {
    $user['borrowed_titles'] = [];
    $user['total_fine'] = 0;
    foreach ($borrowing_data as $record) {
        if ($record['user_email'] == $user['email'] && $record['status'] == 'borrowed') {
            $user['borrowed_titles'][] = htmlspecialchars($record['book_title']);
            if (strtotime('now') > strtotime($record['due_date'])) {
                $user['total_fine'] += $fine_per_book;
            }
        }
    }
    $user_table_data[] = $user;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>
    <link rel="stylesheet" href="admin_style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-card .icon {
            font-size: 48px;
            padding: 15px;
            border-radius: 50%;
            color: #fff;
        }
        .stat-card.receivable .icon { background-color: #dc3545; }
        .stat-card.users .icon { background-color: #007bff; }
        .stat-card.borrowed .icon { background-color: #ffc107; }

        .stat-card .info h3 {
            margin: 0;
            font-size: 1rem;
            color: #6c757d;
            font-weight: normal;
        }
        .stat-card .info p { margin: 5px 0 0; font-size: 1.8rem; font-weight: bold; color: #343a40;}
    </style>
</head>
<body>
    <!-- side navbar start -->
    <?php generate_navbar('HOME', true); ?>
    <!-- side navbar end -->
    
    <!-- Main Content Start -->
    <main class="content">
        <div class="welcome">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> </h1>
        </div>
        
        <!-- Bagian Statistik/Finansial yang Diperbarui -->
        <div class="stats-container">
            <div class="stat-card receivable">
                <i class="material-icons icon">error_outline</i>
                <div class="info">
                    <h3>Total Fines Receivables</h3>
                    <p>Rp <?= number_format($grand_total_fine, 0, ',', '.') ?></p>
                </div>
            </div>
            <div class="stat-card users">
                <i class="material-icons icon">group</i>
                <div class="info">
                    <h3>Registered Users</h3>
                    <p><?= count($users) ?></p>
                </div>
            </div>
            <div class="stat-card borrowed">
                <i class="material-icons icon">book</i>
                <div class="info">
                    <h3>Book Currently on Loan</h3>
                    <p><?= $total_borrowed_books ?></p>
                </div>
            </div>
        </div>
        <div class="table">
            <h2>User Data</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Borrowed Books</th>
                        <th>Fines</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($user_table_data as $user) {
                        echo "<tr>";
                        echo "<td>" . $user['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                        echo "<td>" . (empty($user['borrowed_titles']) ? 'Tidak ada' : implode(', ', $user['borrowed_titles'])) . "</td>";
                        echo "<td>Rp " . number_format($user['total_fine'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- Main Content End -->
</body>
</html>