<?php
//Created by Jessica Xayasane
session_start(); 

$conn = new mysqli("localhost", "root", "", "test2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, role FROM customers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];
        $role = $row['role']; 

        if ($password === $storedPassword) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: welcome_admin.php");
            } elseif ($role === 'user') {
                header("Location: welcome.php");
            } else {
                header("Location: login.php?error=Unexpected+role");
            }
            exit();
        } else {
            header("Location: login.php?error=Incorrect+password");
            exit();
        }
    } else {
        header("Location: login.php?error=Username+not+found");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
