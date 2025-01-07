<!DOCTYPE html>
<!-- Created by Jessica Xayasane -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
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
        .register-form {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .register-form h2 {
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
        .form-group input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .register-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .register-btn:hover {
            background-color: #45a049;
        }
        .password-description {
            font-size: 0.9em;
            color: #555;
            margin-top: 5px;
        }
        .error {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>

    <form class="register-form" method="POST" action="">
        <h2>Register</h2>

        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
        </div>

        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
        </div>

        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required>
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>
            <small class="password-description">Your password must be at least 8 characters long and include at least 1 uppercase letter, 1 lowercase letter, and one of the following special characters: $#@%&.</small>
        </div>

        <button type="submit" class="register-btn">Register</button>

        <?php
        // Database connection
        $conn = mysqli_connect("localhost", "root", "", "test2");

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $dob = $_POST['dob'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            $dobDate = new DateTime($dob);
            $today = new DateTime();
            $age = $today->diff($dobDate)->y;

            if ($age < 18) {
                echo "<div class='error'>You must be at least 18 years old to register.</div>";
            } elseif (!validatePassword($password)) {
                echo "<div class='error'>Password must be at least 8 characters long, contain at least 1 uppercase letter, 1 lowercase letter, and one special character ($#@%&).</div>";
            } else {
                
                $stmt = $conn->prepare("SELECT * FROM customers WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<div class='error'>Username already exists. Please choose another.</div>";
                } else {
                    
                    $stmt = $conn->prepare("INSERT INTO customers (username, password, f_name, l_name, dob, role) VALUES (?, ?, ?, ?, ?, 'user')");
                    $stmt->bind_param("sssss", $username, $password, $firstName, $lastName, $dob);
                    if ($stmt->execute()) {
                        echo "<div class='success'>Registration successful!</div>";
                        echo "<div class='login-link'><a href='login.php'>Go to Login Page</a></div>";
                    } else {
                        echo "<div class='error'>Error: " . $stmt->error . "</div>";
                    }
                }
            }

            $stmt->close();
        }

        function validatePassword($password) {
            return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[$#@%&]).{8,}$/', $password);
        }

        $conn->close();
        ?>
    </form>

</body>
</html>
