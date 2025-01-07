<?php
//created by Jessica Xayasane
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "test2");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$transactions = [];
$acct_num = '';
$start_date = '';
$end_date = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acct_num = $_POST['acct_num'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (empty($acct_num) || empty($start_date) || empty($end_date)) {
        echo "<div class='error'>All fields are required.</div>";
    } else {
        
        $stmt = $conn->prepare("SELECT trxn_id, trxn_date, amount, acct_num_source, acct_num_target, type 
                                FROM trxns 
                                WHERE (acct_num_source = ? OR acct_num_target = ?) 
                                AND trxn_date BETWEEN ? AND ?");
        $stmt->bind_param("ssss", $acct_num, $acct_num, $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
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
    <title>Transaction History</title>
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
            max-width: 600px;
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
        .back-home-btn {
            width: 100%;
            padding: 12px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .back-home-btn:hover {
            background-color: #e53935;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Transaction History</h2>

        <form method="POST" action="">
            <div class="form-group">
                <label for="acct_num">Account Number</label>
                <input type="text" id="acct_num" name="acct_num" placeholder="Enter account number" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>

            <button type="submit" class="submit-btn">View Transactions</button>
            <a href="welcome_admin.php"><button type="button" class="back-home-btn">Back to Home</button></a>
        </form>

        <?php if (count($transactions) > 0): ?>
            <h3>Transactions for Account #<?php echo htmlspecialchars($acct_num); ?> from <?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?>:</h3>
            <table>
                <tr>
                    <th>Transaction ID</th>
                    <th>Transaction Date</th>
                    <th>Amount</th>
                    <th>Source Account</th>
                    <th>Target Account</th>
                    <th>Type</th>
                </tr>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['trxn_id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['trxn_date']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['acct_num_source']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['acct_num_target']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="error">No transactions found for the given account and date range.</div>
        <?php endif; ?>
    </div>

</body>
</html>
