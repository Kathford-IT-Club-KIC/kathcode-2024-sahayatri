<?php
include 'Includes/dbcon.php';

$sql = "SELECT source_latitude, source_longitude, destination_latitude, destination_longitude FROM poolrequests";
$result = $conn->query($sql);

$locations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $locations[] = [
            'source' => [
                'lat' => $row["source_latitude"],
                'lng' => $row["source_longitude"]
            ],
            'destination' => [
                'lat' => $row["destination_latitude"],
                'lng' => $row["destination_longitude"]
            ]
        ];
    }
} else {
    echo "0 results";
}
?>