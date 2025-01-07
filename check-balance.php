<?php
//Created by Jessica Xayasane
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "test2");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT acct_num, balance FROM accounts WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$accounts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }
} else {
    echo "No accounts found for user.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Balances</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .account-balance {
            margin: 20px auto;
            padding: 10px;
            max-width: 400px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo htmlspecialchars($username); ?>! Here are your account balances:</h2>

    <?php if (!empty($accounts)): ?>
        <?php foreach ($accounts as $account): ?>
            <div class="account-balance">
                <h3><?php echo htmlspecialchars($account['acct_num']); ?></h3>
                <p>Balance: $<?php echo htmlspecialchars($account['balance']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No accounts available.</p>
    <?php endif; ?>
    <a href="welcome.php"><button type="button" class="back-home-btn">Back to Home</button></a>
</body>
</html>
