<!DOCTYPE html>
<!-- created by Jessica Xayasane -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #333;
            padding: 10px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            font-size: 18px;
        }

        .navbar a:hover {
            background-color: #575757;
            border-radius: 5px;
        }

        .navbar .login-btn {
            background-color: #4CAF50;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .navbar .login-btn:hover {
            background-color: #45a049;
        }

        .main-content {
          text-align: center;
            color: white;
            position: relative; 
            z-index: 1; 
            height: 100vh; 
            display: flex;
            flex-direction: column;
            justify-content: center; 
        }

        
        .main-content::before {
            content: "";
            background-image: url('windows.jpg'); 
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1; 
            opacity: 0.8; 
        }

        
        .main-content h1 {
            font-size: 100px; 
            padding: 100px 0;  
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
        }

        .main-content p {
            font-size: 18px;
            margin-top: 20px;
        }

        
        @media (max-width: 768px) {
            .main-content h1 {
                font-size: 40px;
                padding: 60px 0;
            }
        }
    </style>
</head>
<body>

   
    <div class="navbar">
        <a href="login.php" class="login-btn">Login</a>
        <div>
            <a href="index.php">Home</a>
        </div>
    </div>

    
    <div class="main-content">
        <h1>Valley Falls Bank</h1>
        <p>At Valley Falls Bank, we believe in safe and efficient banking. Bank with Valley Falls Bank to receive exceptional care and service.</p>
    </div>

</body>
</html>
