<?php
    include 'dbcon.php';
    include 'session.php';
    header('Content-Type: application/json');

    // Get the JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    $userId = $_SESSION['userId'];
    $sourceAddress = $data['sourceAddress'];
    $sourceLatitude = $data['sourceLatitude'];
    $sourceLongitude = $data['sourceLongitude'];
    $destinationAddress = $data['destinationAddress'];
    $destinationLatitude = $data['destinationLatitude'];
    $destinationLongitude = $data['destinationLongitude'];
    $vehicleType = $data['vehicleType'];
    $advertisedSeats = $data['advertisedSeats'];
    $date = $data['date'];
    $time = $data['time'];

    $sql = "INSERT INTO poolalerts (userId, sourceAddress, sourceLatitude, sourceLongitude, destinationAddress, destinationLatitude, destinationLongitude, vehicleType, vacantSeats, advertisedSeats, date, time)
    VALUES ('$userId', '$sourceAddress', '$sourceLatitude', '$sourceLongitude', '$destinationAddress', '$destinationLatitude', '$destinationLongitude', '$vehicleType', '$advertisedSeats', '$advertisedSeats', '$date', '$time')";

    if ($conn->query($sql)) {
        $alertId = $conn->insert_id;
        echo json_encode(array("status" => "success", "message" => "Record added successfully", "alertId" => $alertId));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
    }

    $conn->close();
?>
