<?php
include 'dbcon.php'; // Make sure to include your database connection

// Retrieve JSON data from POST request
$data = json_decode(file_get_contents('php://input'), true);

// Check if requestId and alertId are set
if (isset($data['requestId']) && isset($data['alertId']) && isset($data['appliedSeats']) && isset($data['price']) && isset($data['vacantSeats'])) {
    $requestId = $data['requestId'];
    $alertId = $data['alertId'];
    $appliedSeats = $data['appliedSeats'];
    $price = $data['price'];
    $vacantSeats = $data['vacantSeats'];

    $requestSql = "UPDATE poolrequests SET status = 'booked' WHERE id = $requestId";
    $remainingSeats = $vacantSeats - $appliedSeats;

    if ($remainingSeats == 0) {
        $alertSql = "UPDATE poolalerts SET status = 'booked' WHERE id = $alertId";
    } else {
        $alertSql = "UPDATE poolalerts SET vacantSeats = $remainingSeats WHERE id = $alertId";
    }

    if ($conn->query($requestSql) && $conn->query($alertSql)) {
        $mappingSql = "INSERT INTO poolmappings (poolalertid, poolrequestid, price) VALUES ($alertId, $requestId, $price)";
        if ($conn->query($mappingSql)) {
            $response = array('success' => true, 'mappingId' => $conn->insert_id);
        } else {
            $response = array('success' => false, 'error' => $conn->error);
        }
    } else {
        $response = array('success' => false, 'error' => $conn->error);
    }
} else {
    $response = array('success' => false, 'error' => 'Invalid input');
}

echo json_encode($response);
?>
