<?php
//Created by Jessica Xayasane
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "test2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT f_name FROM customers WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($f_name);
$stmt->fetch();
$stmt->close();

if (!$f_name) {
    $f_name = "User";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .welcome-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .welcome-container h1 {
            color: #333;
        }
        .nav-links {
            list-style-type: none;
            padding: 0;
        }
        .nav-links li {
            margin: 15px 0;
        }
        .nav-links a {
            text-decoration: none;
            color: #4CAF50;
            font-size: 16px;
            padding: 10px 20px;
            border: 1px solid #4CAF50;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s, color 0.3s;
        }
        .nav-links a:hover {
            background-color: #4CAF50;
            color: #fff;
        }
        .logout-btn {
            color: #d9534f;
            border: 1px solid #d9534f;
        }
        .logout-btn:hover {
            background-color: #d9534f;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="welcome-container">
        <h1>Welcome, Admin <?php echo htmlspecialchars($f_name); ?>!</h1>
        <p>What would you like to do today?</p>
        
        <ul class="nav-links">
            <li><a href="check_balance_admin.php">Check Balances</a></li>
            <li><a href="manage_funds_admin.php">Manage Funds</a></li>
            <li><a href="open_account_admin.php">Open New Account</a></li>
            <li><a href="delete_account.php">Delete Accounts</a></li>
            <li><a href="summary.php">Transaction Histories</a></li>
            <li><a href="empty_user.php">Customers With No Accounts</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

</body>
</html>
