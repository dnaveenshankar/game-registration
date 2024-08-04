<?php
// Include database connection
include 'db.php';

// Get the team_id from the request
$team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;

if ($team_id > 0) {
    // Fetch team details
    $stmt = $conn->prepare("SELECT * FROM team_details WHERE id = ?");
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $team_details = $result->fetch_assoc();
    $stmt->close();

    if ($team_details) {
        // Display team details in a table
        echo '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 800px; margin: 0 auto;">';
        echo '<thead>';
        echo '<tr style="background-color: #ff9933; color: #fff;">';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($team_details as $key => $value) {
            if ($value !== null) {
                $label = ucfirst(str_replace('_', ' ', $key));
                echo '<tr>';
                echo '<td style="font-weight: bold; padding: 10px;">' . htmlspecialchars($label) . '</td>';
                echo '<td style="padding: 10px;">' . htmlspecialchars($value) . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No details found for this team.</p>';
    }
} else {
    echo '<p>Invalid team ID.</p>';
}
?>
