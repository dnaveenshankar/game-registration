<?php
// Include the database connection file
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['team_name'])) {
        // Team leader details
        $team_name = $_POST['team_name'];
        $leader_name = $_POST['leader_name'];
        $leader_email = $_POST['leader_email'];
        $leader_mobile = $_POST['leader_mobile'];
        $leader_player_id = intval($_POST['leader_player_id']);
        $leader_in_game_name = $_POST['leader_in_game_name'];
        $leader_id_level = intval($_POST['leader_id_level']);

        // Team member details
        $player1_id = intval($_POST['player1_id']);
        $player1_name = $_POST['player1_name'];
        $player1_level = intval($_POST['player1_level']);

        $player2_id = intval($_POST['player2_id']);
        $player2_name = $_POST['player2_name'];
        $player2_level = intval($_POST['player2_level']);

        $player3_id = intval($_POST['player3_id']);
        $player3_name = $_POST['player3_name'];
        $player3_level = intval($_POST['player3_level']);

        $player4_id = intval($_POST['player4_id']);
        $player4_name = $_POST['player4_name'];
        $player4_level = intval($_POST['player4_level']);

        // Substitute details - Handle null values for optional fields
        $sub1_id = !empty($_POST['sub1_id']) ? intval($_POST['sub1_id']) : null;
        $sub1_name = empty($_POST['sub1_name']) ? null : $_POST['sub1_name'];

        $sub2_id = !empty($_POST['sub2_id']) ? intval($_POST['sub2_id']) : null;
        $sub2_name = empty($_POST['sub2_name']) ? null : $_POST['sub2_name'];

        // Insert into team_details table
        $stmt = $conn->prepare("INSERT INTO team_details (
            team_name, leader_name, leader_email, leader_mobile, leader_player_id, leader_in_game_name, leader_id_level,
            player1_id, player1_name, player1_level,
            player2_id, player2_name, player2_level,
            player3_id, player3_name, player3_level,
            player4_id, player4_name, player4_level,
            sub1_id, sub1_name, sub2_id, sub2_name
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
        
        // Bind parameters (22 total)
        $stmt->bind_param("sssssssssssssssssssssss", 
            $team_name, $leader_name, $leader_email, $leader_mobile, $leader_player_id, $leader_in_game_name, $leader_id_level,
            $player1_id, $player1_name, $player1_level,
            $player2_id, $player2_name, $player2_level,
            $player3_id, $player3_name, $player3_level,
            $player4_id, $player4_name, $player4_level,
            $sub1_id, $sub1_name, $sub2_id, $sub2_name);
        
        if ($stmt->execute()) {
            $team_id = $stmt->insert_id; // Get the last inserted team_id

            // Retrieve last allocated slot and room
            $stmt = $conn->prepare("SELECT room_number, slot_number FROM room_details ORDER BY room_number DESC, slot_number DESC LIMIT 1");
            $stmt->execute();
            $stmt->bind_result($last_room_number, $last_slot_number);
            $stmt->fetch();
            $stmt->close();

            // Determine the next slot and room number
            if ($last_slot_number === null || $last_room_number === null) {
                // No previous slots, start from room 1, slot 1
                $slot_number = 1;
                $room_number = 1;
            } else {
                if ($last_slot_number >= 25) {
                    // Slot 25 is full, move to the next room
                    $slot_number = 1;
                    $room_number = $last_room_number + 1;
                } else {
                    // Continue in the current room
                    $slot_number = $last_slot_number + 1;
                    $room_number = $last_room_number;
                }
            }

            // Insert into room_details table
            $stmt = $conn->prepare("INSERT INTO room_details (team_id, room_number, slot_number) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $team_id, $room_number, $slot_number);
            $stmt->execute();
            $stmt->close();

            // Output HTML form for redirect with POST method
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Redirecting...</title>
                <script>
                    function submitForm() {
                        document.getElementById("redirectForm").submit();
                    }
                </script>
                <style>
                    body {
                        font-family: \'Arial\', sans-serif;
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        background-color: #0d0d0d;
                        color: #fff;
                        overflow: hidden;
                    }
                    .container {
                        text-align: center;
                        background-color: #1a1a1a;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                        animation: fadeIn 1s ease-out;
                        overflow: auto; /* Allows scrolling */
                        scrollbar-width: none; /* Hides scrollbar in Firefox */
                    }
                    .container::-webkit-scrollbar {
                        display: none; /* Hides scrollbar in WebKit browsers */
                    }
                    .container h1 {
                        margin-bottom: 30px;
                        font-size: 2.5em;
                        color: #ff9933;
                        animation: slideInFromLeft 1s ease-out;
                    }
                    .button {
                        display: block;
                        width: 200px;
                        padding: 15px;
                        margin: 10px 0;
                        background-color: #ff9933;
                        color: #0d0d0d;
                        text-align: center;
                        text-decoration: none;
                        border-radius: 5px;
                        transition: background-color 0.3s ease, transform 0.3s ease;
                        font-weight: bold;
                        animation: slideInFromRight 1.5s ease-out;
                    }
                    .button:hover {
                        background-color: #cc7a00;
                        transform: translateY(-5px);
                    }
                    .social-icons {
                        margin-top: 20px;
                    }
                    .social-icons a {
                        color: #ff9933;
                        margin: 0 10px;
                        font-size: 1.5em;
                        transition: color 0.3s ease, transform 0.3s ease;
                    }
                    .social-icons a:hover {
                        color: #cc7a00;
                        transform: translateY(-5px);
                    }
                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                    }
                    @keyframes slideInFromLeft {
                        from {
                            transform: translateX(-100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                    @keyframes slideInFromRight {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                </style>
            </head>
            <body onload="submitForm()">
                <form id="redirectForm" method="post" action="success.php">
                    <input type="hidden" name="team_id" value="' . htmlspecialchars($team_id) . '">
                </form>
            </body>
            </html>';
            exit();
        } else {
            echo "Error: " . $stmt->error;
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
    <title>BGMI Team Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0d0d0d;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 800px;
            height: 90vh; /* Adjust height to ensure scrolling works */
            overflow-y: auto; /* Allows vertical scrolling */
            scrollbar-width: thin; /* Style scrollbar in Firefox */
        }

        .container::-webkit-scrollbar {
            width: 8px; /* Width of the scrollbar in WebKit browsers */
        }

        .container::-webkit-scrollbar-thumb {
            background-color: #ff9933; /* Color of the scrollbar handle */
            border-radius: 4px;
        }

        .container::-webkit-scrollbar-thumb:hover {
            background-color: #cc7a00; /* Color of the scrollbar handle on hover */
        }

        h1 {
            color: #ff9933;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0;
        }

        input[type="text"], input[type="number"], input[type="email"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
        }

        input[type="submit"] {
            background-color: #ff9933;
            color: #0d0d0d;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #cc7a00;
            transform: translateY(-2px);
        }

        .social-icons {
            text-align: center;
            margin-top: 20px;
        }

        .social-icons a {
            color: #ff9933;
            margin: 0 10px;
            font-size: 24px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .social-icons a:hover {
            color: #cc7a00;
            transform: translateY(-2px);
        }

        .icons {
            text-align: center;
            margin-top: 20px;
        }

        .icons a {
            color: #ff9933;
            margin: 0 10px;
            font-size: 24px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .icons a:hover {
            color: #cc7a00;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>BGMI Team Registration</h1>
        <form method="POST" action="">
            <fieldset>
                <legend>Team Leader Details</legend>
                <label for="team_name">Team Name:</label>
                <input type="text" id="team_name" name="team_name" required>

                <label for="leader_name">Leader Name:</label>
                <input type="text" id="leader_name" name="leader_name" required>

                <label for="leader_email">Leader Email:</label>
                <input type="email" id="leader_email" name="leader_email" required>

                <label for="leader_mobile">Leader Mobile:</label>
                <input type="text" id="leader_mobile" name="leader_mobile" required>

                <label for="leader_player_id">Leader Player ID:</label>
                <input type="number" id="leader_player_id" name="leader_player_id" required>

                <label for="leader_in_game_name">Leader In-Game Name:</label>
                <input type="text" id="leader_in_game_name" name="leader_in_game_name" required>

                <label for="leader_id_level">Leader ID Level:</label>
                <input type="number" id="leader_id_level" name="leader_id_level" required>
            </fieldset>

            <fieldset>
                <legend>Team Member Details</legend>
                <!-- Repeat for each player -->
                <label for="player1_id">Player 1 ID:</label>
                <input type="number" id="player1_id" name="player1_id" required>

                <label for="player1_name">Player 1 Name:</label>
                <input type="text" id="player1_name" name="player1_name" required>

                <label for="player1_level">Player 1 Level:</label>
                <input type="number" id="player1_level" name="player1_level" required>

                <label for="player2_id">Player 2 ID:</label>
                <input type="number" id="player2_id" name="player2_id" required>

                <label for="player2_name">Player 2 Name:</label>
                <input type="text" id="player2_name" name="player2_name" required>

                <label for="player2_level">Player 2 Level:</label>
                <input type="number" id="player2_level" name="player2_level" required>

                <label for="player3_id">Player 3 ID:</label>
                <input type="number" id="player3_id" name="player3_id" required>

                <label for="player3_name">Player 3 Name:</label>
                <input type="text" id="player3_name" name="player3_name" required>

                <label for="player3_level">Player 3 Level:</label>
                <input type="number" id="player3_level" name="player3_level" required>

                <label for="player4_id">Player 4 ID:</label>
                <input type="number" id="player4_id" name="player4_id" required>

                <label for="player4_name">Player 4 Name:</label>
                <input type="text" id="player4_name" name="player4_name" required>

                <label for="player4_level">Player 4 Level:</label>
                <input type="number" id="player4_level" name="player4_level" required>
            </fieldset>

            <fieldset>
                <legend>Substitute Player Details</legend>
                <label for="sub1_id">Substitute 1 ID:</label>
                <input type="number" id="sub1_id" name="sub1_id">

                <label for="sub1_name">Substitute 1 Name:</label>
                <input type="text" id="sub1_name" name="sub1_name">

                <label for="sub2_id">Substitute 2 ID:</label>
                <input type="number" id="sub2_id" name="sub2_id">

                <label for="sub2_name">Substitute 2 Name:</label>
                <input type="text" id="sub2_name" name="sub2_name">
            </fieldset>

            <input type="submit" value="Register Team">
        </form>
        <div class="icons">
            <a href="https://youtube.com/@pixelvortex?si=7VKyZeQ2PazY-4KK" target="_blank"><i class="fab fa-youtube"></i></a>
            <a href="https://discord.com/invite/G3ZxR5FzBW" target="_blank"><i class="fab fa-discord"></i></a>
        </div>
    </div>
</body>
</html>
