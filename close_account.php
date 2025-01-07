<?php
//Created by Grant Jones and Gabriel Herron
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
$stmt = $conn->prepare("SELECT acct_num, balance FROM accounts WHERE username = ? AND status = 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accountNum = $_POST['account_num'];

    $stmt = $conn->prepare("SELECT balance FROM accounts WHERE acct_num = ? AND username = ?");
    $stmt->bind_param("ss", $accountNum, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $account = $result->fetch_assoc();
        if ($account['balance'] > 0) {
            echo "<div class='error'>Balance is not zero. Please withdraw all funds before closing the account.</div>";
        } else {
            $stmt = $conn->prepare("UPDATE accounts SET status = 0 WHERE acct_num = ?");
            $stmt->bind_param("s", $accountNum);
            if ($stmt->execute()) {
                echo "<div class='success'>Account closed successfully.</div>";
            } else {
                echo "<div class='error'>Error closing the account. Please try again.</div>";
            }
        }
    } else {
        echo "<div class='error'>Account not found or does not belong to you.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Close Account</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-container h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #4CAF50;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group select:focus {
            border-color: #4CAF50;
            outline: none;
        }
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-btn:hover {
            background-color: #005fa3;
        }
        .error {
            text-align: center;
            margin-top: 15px;
            color: #d9534f;
        }
        .success {
            text-align: center;
            margin-top: 15px;
            color: #5cb85c;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Request to Close Account</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="account_num">Select Account to Close</label>
                <select id="account_num" name="account_num" required>
                    <option value="" disabled selected>Choose an account</option>
                    <?php foreach ($accounts as $account): ?>
                        <option value="<?= htmlspecialchars($account['acct_num']) ?>">
                            <?= htmlspecialchars($account['acct_num']) ?> - Balance: <?= number_format($account['balance'], 2) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="submit-btn">Submit Request</button>
            <h2><a href="welcome.php">Return Home</a></h2>
        </form>
    </div>
</body>
</html>