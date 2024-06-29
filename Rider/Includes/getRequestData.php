<?php
include 'dbcon.php';

$requestId = $_GET['requestId'];

$sql = "SELECT * FROM poolrequests INNER JOIN users ON poolrequests.userId = users.id WHERE poolrequests.id = $requestId";
$result = $conn->query($sql);

$response = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
}

echo json_encode($response);
?>
