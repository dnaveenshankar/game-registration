<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // Adjust the path to autoload.php

// Include the database connection file
include 'db.php';

$message = '';
$email_status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['team_id']) && !empty($_POST['team_id'])) {
        // Get the team_id from POST request
        $team_id = intval($_POST['team_id']);

        if ($team_id > 0) {
            // Prepare and execute the SQL query to get room and slot details
            $stmt = $conn->prepare("SELECT room_number, slot_number FROM room_details WHERE team_id = ?");
            $stmt->bind_param("i", $team_id);
            $stmt->execute();
            $stmt->bind_result($room_number, $slot_number);
            $stmt->fetch();
            $stmt->close();

            // Prepare and execute the SQL query to get leader's email using the `id` column
            $stmt = $conn->prepare("SELECT leader_email FROM team_details WHERE id = ?");
            $stmt->bind_param("i", $team_id);
            $stmt->execute();
            $stmt->bind_result($leader_email);
            $stmt->fetch();
            $stmt->close();

            if ($room_number !== null && $slot_number !== null && $leader_email) {
                $message = "Congratulations! Your team has been allocated the following details:\n\n";
                $message .= "Room Number: " . htmlspecialchars($room_number) . "\n";
                $message .= "Slot Number: " . htmlspecialchars($slot_number) . "\n";

                // Prepare PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP(); // Set mailer to use SMTP
                    $mail->Host       = 'smtp.gmail.com'; // Specify main and backup SMTP servers
                    $mail->SMTPAuth   = true; // Enable SMTP authentication
                    $mail->Username   = 'pixelvortex.gamming.yt@gmail.com'; // SMTP username
                    $mail->Password   = 'gczm vtli cfgt tqvg'; // Use the app password generated in Google Account
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
                    $mail->Port       = 587; // TCP port to connect to

                    // Recipients
                    $mail->setFrom('pixelvortex.gamming.yt@gmail.com', 'Pixel Vortex Gaming');
                    $mail->addAddress($leader_email); // Add a recipient

                    // Content
                    $mail->isHTML(false); // Set email format to HTML
                    $mail->Subject = "Your Team's Slot Details";
                    $mail->Body    = "Dear Team Leader,\n\n" .
                                     "Your team has been assigned to Room Number: " . htmlspecialchars($room_number) . "\n" .
                                     "Slot Number: " . htmlspecialchars($slot_number) . "\n\n" .
                                     "Best of luck!\n\n" .
                                     "- Pixel Vortex Gaming";

                    $mail->send();
                    $email_status = "An email with the allocation details has been sent to " . htmlspecialchars($leader_email) . ".";
                } catch (Exception $e) {
                    $email_status = "Failed to send email. Mailer Error: " . $mail->ErrorInfo;
                }
            } else {
                $message = "No allocation details found for this team.";
                $email_status = "";
            }
        } else {
            $message = "Invalid team ID.";
            $email_status = "";
        }
    } else {
        $message = "Team ID is required.";
        $email_status = "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            max-width: 600px;
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        h1 {
            color: #ff9933;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            line-height: 1.5;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: #fff;
            background-color: #ff4d4d;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registration Successful!</h1>
        <p><?php echo nl2br(htmlspecialchars($message)); ?></p>
        <?php if (!empty($email_status)): ?>
            <p><?php echo nl2br(htmlspecialchars($email_status)); ?></p>
        <?php endif; ?>
        <a href="index.html" class="back-button">Back to Home</a>
    </div>
</body>
</html>
