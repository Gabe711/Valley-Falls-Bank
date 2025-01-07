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

$balance = '';
$acct_num = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acct_num = $_POST['acct_num'];

    if (empty($acct_num)) {
        $error_message = "Account number is required.";
    } else {
        $stmt = $conn->prepare("SELECT balance FROM accounts WHERE acct_num = ?");
        $stmt->bind_param("s", $acct_num);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($balance);
        
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
        } else {
            $error_message = "Account number not found.";
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
    <title>Check Balance</title>
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
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
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
        .error, .success {
            color: red;
            text-align: center;
        }
        .balance-container {
            margin-top: 20px;
            font-size: 1.2em;
        }
        .back-home-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .back-home-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Check Account Balance</h2>

        <form method="POST" action="">
            <div class="form-group">
                <label for="acct_num">Account Number</label>
                <input type="text" id="acct_num" name="acct_num" placeholder="Enter account number" required>
            </div>

            <button type="submit" class="submit-btn">Check Balance</button>

            <a href="welcome_admin.php"><button type="button" class="back-home-btn">Back to Home</button></a>
        </form>

        <?php if ($error_message): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif ($balance !== ''): ?>
            <div class="balance-container">
                <p><strong>Balance for Account #<?php echo htmlspecialchars($acct_num); ?>: $<?php echo number_format($balance, 2); ?></strong></p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
