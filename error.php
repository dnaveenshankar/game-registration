<?php
// Start the session
session_start();

// Check if there is an error message set in the session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : 'An unexpected error occurred.';

// Clear the error message from the session
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #0d0d0d;
            color: #fff;
            text-align: center;
        }

        h1 {
            color: #ff4d4d;
            font-size: 2em;
        }

        p {
            font-size: 1.2em;
        }

        .back-button {
            background-color: #555;
            padding: 10px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <h1>Error</h1>
    <p><?php echo htmlspecialchars($error_message); ?></p>
    <a href="index.html" class="back-button">Back to Home</a>
</body>
</html>
