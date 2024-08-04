<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Fetch team and room details
$sql = "SELECT td.id, td.team_name, rd.room_number, rd.slot_number 
        FROM team_details td 
        JOIN room_details rd ON td.id = rd.team_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #0d0d0d;
            color: #fff;
        }

        h1 {
            color: #ff9933;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #555;
            text-align: center;
        }

        th {
            background-color: #333;
        }

        .view-button {
            background-color: #ff4d4d;
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .view-button:hover {
            background-color: #cc0000;
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
            text-align: center;
            margin: 20px auto;
            display: block;
            width: 100px;
        }

        .back-button:hover {
            background-color: #333;
        }

        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            max-height: 80%;
            overflow-y: auto;
            width: 90%;
            max-width: 600px;
        }

        .popup h2 {
            margin-top: 0;
        }

        .popup-content {
            margin-top: 10px;
        }

        .close-popup {
            background-color: #ff4d4d;
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            float: right;
        }

        .close-popup:hover {
            background-color: #cc0000;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .view-button, .back-button {
                font-size: 14px;
                padding: 8px;
            }

            th, td {
                font-size: 14px;
                padding: 8px;
            }

            .popup {
                width: 95%;
                max-width: none;
                padding: 10px;
            }

            .popup-content {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 24px;
            }

            table {
                font-size: 12px;
            }

            .view-button, .back-button {
                font-size: 12px;
                padding: 6px;
            }

            .popup-content {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="index.html" class="back-button">Back</a>
    <table>
        <tr>
            <th>Team ID</th>
            <th>Team Name</th>
            <th>Room Number</th>
            <th>Slot Number</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                <td><?php echo htmlspecialchars($row['slot_number']); ?></td>
                <td>
                    <button class="view-button" onclick="showDetails(<?php echo htmlspecialchars($row['id']); ?>)">View</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="overlay" id="overlay"></div>

    <div class="popup" id="popup">
        <button class="close-popup" onclick="closePopup()">Close</button>
        <h2>Team Details</h2>
        <div class="popup-content" id="popup-content"></div>
    </div>

    <script>
        function showDetails(teamId) {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            const popupContent = document.getElementById('popup-content');

            fetch(`get_team_details.php?team_id=${teamId}`)
                .then(response => response.text())
                .then(data => {
                    popupContent.innerHTML = data;
                    popup.style.display = 'block';
                    overlay.style.display = 'block';
                });
        }

        function closePopup() {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');

            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
    </script>
</body>
</html>
