<?php
//Created by Gabriel Herron
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$conn = mysqli_connect("localhost", "root", "", "test2");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $amount = $_POST['amount'];
    $accountNumSource = $_POST['account_num_source'];
    $accountNumTarget = isset($_POST['account_num_target']) ? $_POST['account_num_target'] : null;
    $trxnDate = date('Y-m-d H:i:s');

    if ($amount <= 0) {
        echo "<div class='error'>Amount must be greater than zero.</div>";
    } else {
        switch ($action) {
            case 'Withdraw':
                $stmt = $conn->prepare("SELECT balance FROM accounts WHERE acct_num = ?");
                $stmt->bind_param("s", $accountNumSource);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row['balance'] >= $amount) {
                        
                        $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE acct_num = ?");
                        $stmt->bind_param("ds", $amount, $accountNumSource);
                        $stmt->execute();

                        $stmt = $conn->prepare("INSERT INTO trxns (trxn_date, amount, acct_num_source, acct_num_target, type) VALUES (?, ?, ?, ?, 'Withdraw')");
                        $stmt->bind_param("sdss", $trxnDate, $amount, $accountNumSource, $accountNumSource);
                        $stmt->execute();

                        echo "<div class='success'>Withdrawal successful!</div>";
                    } else {
                        echo "<div class='error'>Insufficient balance for withdrawal.</div>";
                    }
                } else {
                    echo "<div class='error'>Source account not found.</div>";
                }
                break;

            case 'Deposit':
                $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE acct_num = ?");
                $stmt->bind_param("ds", $amount, $accountNumSource);
                if ($stmt->execute()) {
                    
                    $stmt = $conn->prepare("INSERT INTO trxns (trxn_date, amount, acct_num_source, acct_num_target, type) VALUES (?, ?, ?, ?, 'Deposit')");
                    $stmt->bind_param("sdss", $trxnDate, $amount, $accountNumSource, $accountNumSource);
                    $stmt->execute();

                    echo "<div class='success'>Deposit successful!</div>";
                } else {
                    echo "<div class='error'>Error processing deposit.</div>";
                }
                break;

            case 'Transfer':
                
                $stmt = $conn->prepare("SELECT balance FROM accounts WHERE acct_num = ?");
                $stmt->bind_param("s", $accountNumSource);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row['balance'] >= $amount) {
                        
                        $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE acct_num = ?");
                        $stmt->bind_param("ds", $amount, $accountNumSource);
                        $stmt->execute();

                        $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE acct_num = ?");
                        $stmt->bind_param("ds", $amount, $accountNumTarget);
                        $stmt->execute();

                        $stmt = $conn->prepare("INSERT INTO trxns (trxn_date, amount, acct_num_source, acct_num_target, type) VALUES (?, ?, ?, ?, 'Transfer')");
                        $stmt->bind_param("sdss", $trxnDate, $amount, $accountNumSource, $accountNumTarget);
                        $stmt->execute();

                        echo "<div class='success'>Transfer successful!</div>";
                    } else {
                        echo "<div class='error'>Insufficient balance for transfer.</div>";
                    }
                } else {
                    echo "<div class='error'>Source account not found.</div>";
                }
                break;

            default:
                echo "<div class='error'>Invalid action selected.</div>";
                break;
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Funds</title>
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
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
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
            margin-top: 10px;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .error, .success {
            text-align: center;
            margin-top: 15px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        function toggleFields() {
            const action = document.getElementById("action").value;
            const accountNumSourceField = document.getElementById("account-num-source-group");
            const accountNumTargetField = document.getElementById("account-num-target-group");

            if (action === "Transfer") {
                accountNumSourceField.style.display = "block";
                accountNumTargetField.style.display = "block";
            } else {
                accountNumSourceField.style.display = "block";
                accountNumTargetField.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Manage Funds</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="action">Select Action</label>
                <select id="action" name="action" onchange="toggleFields()" required>
                    <option value="Withdraw">Withdraw</option>
                    <option value="Deposit">Deposit</option>
                    <option value="Transfer">Transfer</option>
                </select>
            </div>
            <div id="account-num-source-group" class="form-group">
                <label for="account_num_source">Account Number (From)</label>
                <input type="text" id="account_num_source" name="account_num_source" placeholder="Enter source account number" required>
            </div>
            <div id="account-num-target-group" class="form-group" style="display: none;">
                <label for="account_num_target">Account Number (To)</label>
                <input type="text" id="account_num_target" name="account_num_target" placeholder="Enter target account number">
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" step="0.01" id="amount" name="amount" placeholder="Enter amount" required>
            </div>
            <button type="submit" class="submit-btn">Submit</button>
            <h2><a href="<?php echo $isAdmin ? 'welcome_admin.php' : 'welcome.php'; ?>">Return Home</a></h2>
        </form>
    </div>
</body>
</html>