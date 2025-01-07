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

function generateAccountNumber($conn) {
    do {
        $acct_num = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("SELECT acct_num FROM accounts WHERE acct_num = ?");
        $stmt->bind_param("s", $acct_num);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $acct_num;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_SESSION['username'];
    $accountType = $_POST['account_type'];
    $initialDeposit = $_POST['initial_deposit'];

    if (!is_numeric($initialDeposit) || $initialDeposit < 25) {
        $error = "Please enter a valid initial deposit amount.";
    } else {
        $acct_num = generateAccountNumber($conn);

        $stmt = $conn->prepare("INSERT INTO accounts (acct_num, username, acct_type, balance, status) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("sssd", $acct_num, $username, $accountType, $initialDeposit);
        
        if ($stmt->execute()) {
            $success = "Account created successfully with account number $acct_num!";
        } else {
            $error = "Error opening account: " . $stmt->error;
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .form-container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container label {
            margin: 10px 0 5px;
            text-align: left;
        }
        .form-container input,
        .form-container select,
        .form-container button {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        .message {
            color: #d9534f;
            font-weight: bold;
        }
        .success {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Open New Account</h1>
        <?php if (isset($error)): ?>
            <p class="message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (isset($success)): ?>
            <p class="message success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" disabled>

            <label for="account_type">Select Account Type:</label>
            <select id="account_type" name="account_type" required>
                <option value="Checkings">Checkings</option>
                <option value="Savings">Savings</option>
            </select>

            <label for="initial_deposit">Initial Deposit: must be at least $25</label>
            <input type="number" id="initial_deposit" name="initial_deposit" step="1" min="25" required>

            <button type="submit">Open Account</button>
            <h2><a href="welcome.php">Return Home</a></h2>
        </form>
    </div>
</body>
</html>