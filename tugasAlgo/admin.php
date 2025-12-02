<?php
$conn = mysqli_connect("localhost", "root", "", "user_client");

if(!$conn){
    die("connection feild: " . mysqli_connect_error());
}

$sql ="SELECT id, name, email, role FROM users";
$result = mysqli_query($conn, $sql); 

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
    <title>Library</title>
    <link rel="stylesheet" href="admin_style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- side navbar start -->
    <nav>
        <div class="profile">
            <i class="material-icons">account_circle</i>
            <h1><?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <a class="detail" href="profile.php">see profile</a>
        </div>
        <div>
            <a class="menu" href="#">HOME</a>
            <a class="menu" href="#">Book</a>
            <a class="menu" href="#">History</a>
            <a class="menu" href="#">Finacial</a>
        </div>
        <div>
            <a class="menu" onclick="window.location.href='login/logout.php'">LogOut</a>
        </div>
    </nav>
    <!-- side navbar end -->
    
    <!-- Main Content Start -->
    <main class="content">
        <div class="welcome">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> </h1>
        </div>
        <div class="finance">
            <h2>Financial</h2>
            <h3 class="cash">Cash in Bank : 0$</h3>
            <h3 class="receivable">Accounts Receivable : 0$</h3>
        </div>
        <h2 class="add-words">Add a Book</h2>
        <div class="button">
            <button id="add">Add</button>
        </div>
        <h2 class="delete-words">Delete a Book</h2>
        <div class="button">
            <button id="delete">Delete</button>
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
                        <!-- <th>Accounts Receivable</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($row = mysqli_fetch_assoc($result)){
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['role'] . "</td>";
                        echo "</tr>";

                    }
                    
                    mysqli_close($conn);
                    
                    
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- Main Content End -->
</body>
</html>