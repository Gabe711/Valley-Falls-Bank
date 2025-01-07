<?php
//Created by Anastasia Vorobyova
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

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$stmt = $conn->prepare("
  SELECT username
  FROM customers
  WHERE username NOT IN (SELECT username FROM accounts)
");

$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empty Accounts</title>
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
        .home-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .home-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo htmlspecialchars($username); ?>! Here are empty accounts:</h2>

    <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
            <div class="account-balance">
                <h3>Username: <?php echo htmlspecialchars($user['username']); ?></h3>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No accounts available.</p>
    <?php endif; ?>

    
    <a href="<?php echo $isAdmin ? 'welcome_admin.php' : 'welcome.php'; ?>" class="home-button">Back to Home</a>

</body>
</html>