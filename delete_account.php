<?php
//Created by Gabriel Herron
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "test2");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT acct_num, username, balance FROM accounts WHERE status = 0");
$stmt->execute();
$result = $stmt->get_result();
$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acct_num'])) {
    $accountNum = $_POST['acct_num'];

    $stmt = $conn->prepare("DELETE FROM accounts WHERE acct_num = ?");
    $stmt->bind_param("s", $accountNum);
    if ($stmt->execute()) {
        echo "<div class='success'>Account $accountNum has been deleted successfully.</div>";
    } else {
        echo "<div class='error'>Error deleting account $accountNum. Please try again.</div>";
    }

    $stmt = $conn->prepare("SELECT acct_num, username, balance FROM accounts WHERE status = 0");
    $stmt->execute();
    $result = $stmt->get_result();
    $accounts = [];
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Accounts</title>
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
            max-width: 600px;
        }
        .form-container h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #00000;
        }
        .accounts-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .accounts-table th, .accounts-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .accounts-table th {
            background-color: #4CAF50;
            color: white;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .submit-btn:hover {
            background-color: #c9302c;
        }
        .return-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        .return-home:hover {
            text-decoration: underline;
        }
        .error {
            color: #d9534f;
            text-align: center;
            margin-top: 10px;
        }
        .success {
            color: #5cb85c;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Delete Accounts</h2>
        <?php if (count($accounts) > 0): ?>
            <table class="accounts-table">
                <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Username</th>
                        <th>Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td><?= htmlspecialchars($account['acct_num']) ?></td>
                            <td><?= htmlspecialchars($account['username']) ?></td>
                            <td><?= number_format($account['balance'], 2) ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="acct_num" value="<?= htmlspecialchars($account['acct_num']) ?>">
                                    <button type="submit" class="submit-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">No closed accounts available for deletion.</p>
        <?php endif; ?>
        <a href="welcome_admin.php" class="return-home">Return Home</a>
    </div>
</body>
</html>