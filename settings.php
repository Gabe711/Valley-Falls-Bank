<?php
//Created by Gabriel Herron
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "test2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
        $error = "Password does not meet the requirements.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM customers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedPassword = $row['password'];

            if ($currentPassword === $storedPassword) {
                $stmt = $conn->prepare("UPDATE customers SET password = ? WHERE username = ?");
                $stmt->bind_param("ss", $newPassword, $username);

                if ($stmt->execute()) {
                    $success = "Password reset successfully!";
                } else {
                    $error = "Error resetting password: " . $stmt->error;
                }
            } else {
                $error = "Current password is incorrect.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Reset Password</title>
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
        .password-requirements {
            text-align: left;
            font-size: 14px;
            color: #666;
        }
        .password-requirements span {
            display: block;
            margin: 5px 0;
        }
        .password-requirements span.valid {
            color: #28a745;
        }
    </style>
    <script>
        function validatePassword() {
            const newPassword = document.getElementById("new_password").value;
            const requirements = {
                length: newPassword.length >= 8,
                uppercase: /[A-Z]/.test(newPassword),
                number: /\d/.test(newPassword),
                specialChar: /[@$!%*?&]/.test(newPassword)
            };

            Object.keys(requirements).forEach(key => {
                const element = document.getElementById(key);
                element.classList.toggle("valid", requirements[key]);
                element.classList.toggle("invalid", !requirements[key]);
            });
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h1>Reset Password</h1>
        <?php if (isset($error)): ?>
            <p class="message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (isset($success)): ?>
            <p class="message success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" oninput="validatePassword()" required>
            <div class="password-requirements">
                <span id="length">Must be at least 8 characters</span>
                <span id="uppercase">Must contain at least one uppercase letter</span>
                <span id="number">Must contain at least one number</span>
                <span id="specialChar">Must contain at least one special character (@, $, !, %, *, ?, &)</span>
            </div>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Reset Password</button>
        </form>

        <h2>
            <a href="<?php echo $isAdmin ? 'welcome_admin.php' : 'welcome.php'; ?>">Return Home</a>
        </h2>
    </div>
</body>
</html>