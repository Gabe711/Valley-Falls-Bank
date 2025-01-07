<?php
//Created by Jessica Xayasane
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "test2");

function generateUniqueAcctNum($conn) {
    do {
        $acct_num = mt_rand(100000000, 999999999); 
        $stmt = $conn->prepare("SELECT acct_num FROM accounts WHERE acct_num = ?");
        $stmt->bind_param("i", $acct_num);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $stmt->close();
    return $acct_num;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $deposit = $_POST['deposit'];
    $acctType = $_POST['acct_type'];

    
    if ($deposit < 25) {
        $message = "Deposit must be at least $25.";
    } else {
        
        $stmt = $conn->prepare("SELECT username FROM accounts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            
            $acct_num = generateUniqueAcctNum($conn);

            $stmt = $conn->prepare("INSERT INTO accounts (acct_num, username, acct_type, balance, status) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param("ssss", $acct_num, $username, $acctType, $deposit);

            if ($stmt->execute()) {
                $message = "Account created successfully with account number $acct_num!";
            } else {
                $message = "Error: " . $stmt->error;
            }
        } else {
            $message = "Username not found.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Account</title>
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
        .account-form {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .account-form h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-top: 15px;
            color: green;
        }
        .error {
            color: red;
        }
        .back-home-btn {
            margin-top: 20px;
            text-align: center;
        }
        .back-home-btn a {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .back-home-btn a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <form class="account-form" method="POST" action="">
        <h2>Open New Account</h2>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>

        <div class="form-group">
            <label for="deposit">Initial Deposit ($25 minimum)</label>
            <input type="number" id="deposit" name="deposit" min="25" placeholder="Enter deposit amount" required>
        </div>

        <div class="form-group">
            <label for="acct_type">Account Type</label>
            <select id="acct_type" name="acct_type" required>
                <option value="checkings">Checking</option>
                <option value="savings">Savings</option>
            </select>
        </div>

        <button type="submit" class="submit-btn">Open Account</button>

    <div class="back-home-btn">
        <a href="welcome_admin.php">Back to Home</a>
    </div>

        <?php if (isset($message)) : ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

    </form>



</body>
</html>
