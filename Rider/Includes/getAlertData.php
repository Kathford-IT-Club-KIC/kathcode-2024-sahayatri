<?php
include 'dbcon.php';

$alertId = $_GET['alertId'];

$sql = "SELECT * FROM poolalerts INNER JOIN users ON poolalerts.userId = users.id WHERE poolalerts.id = $alertId";
$result = $conn->query($sql);

$response = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
}

echo json_encode($response);
?>
