<?php
//Created by Jessica Xayasane
session_start();

if (!isset($_SESSION['username'])) {
   
    header("Location: login.php");
    exit();
}
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
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>What would you like to do today?</p>
        
        <ul class="nav-links">
            <li><a href="check-balance.php">Check Balances</a></li>
            <li><a href="manage_funds.php">Manage Funds</a></li>
            <li><a href="open_account.php">Open New Account</a></li>
            <li><a href="close_account.php">Request to Close Account</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

</body>
</html>
