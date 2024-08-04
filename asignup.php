<?php
// Include database connection
include 'db.php';

// Initialize variables
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $security_code = $_POST['security_code'];

    // Check if the provided security code is correct
    if ($security_code !== '22522024') {
        $error_message = 'Invalid security code.';
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the SQL query to insert the new admin
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $success_message = 'Account created successfully! Redirecting to the admin dashboard...';
            header("refresh:2;url=admin_dashboard.php"); // Redirect after 2 seconds
            exit();
        } else {
            $error_message = 'Failed to create account. Please try again.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #0d0d0d;
            color: #fff;
        }

        .container {
            width: 90%;
            max-width: 400px;
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        h1 {
            color: #ff9933;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 4px;
        }

        .error-message {
            color: #ff4d4d;
            margin-bottom: 15px;
        }

        .success-message {
            color: #4dff4d;
            margin-bottom: 15px;
        }

        button {
            padding: 10px;
            color: #fff;
            background-color: #ff4d4d;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #cc0000;
        }

        .back-button {
            background-color: #555;
            margin-top: 10px;
            display: inline-block;
            padding: 10px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Signup</h1>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="security_code">Security Code</label>
            <input type="password" id="security_code" name="security_code" required>

            <button type="submit">Create Account</button>
            <a href="login.php" class="back-button">Login</a>
            <a href="index.html" class="back-button">Home</a>
        </form>
    </div>
</body>
</html>
