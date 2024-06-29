<?php
include('dbcon.php');
include 'session.php';
$userId = $_SESSION['userId'];

if(isset($_POST['view'])){

    if($_POST["view"] != '')
    {
        $update_query = "UPDATE poolmappings SET isNew = 'no' WHERE isNew = 'yes'";
        mysqli_query($conn, $update_query);
    }

    $query = "SELECT poolmappings.isNew AS isNew, poolalerts.id AS alertId, poolrequests.id AS requestId, poolmappings.id, poolrequests.sourceAddress, poolrequests.destinationAddress, poolalerts.userId AS alertUserId, poolrequests.userId AS requestUserId FROM poolrequests INNER JOIN poolmappings ON poolrequests.id = poolmappings.poolRequestId INNER JOIN poolalerts ON poolmappings.poolAlertId = poolalerts.id WHERE poolmappings.status = 'unread' AND (poolrequests.userId = '$userId' OR poolalerts.userId = '$userId') AND poolmappings.completed = 'no' ORDER BY poolmappings.createdDate DESC";
    $result = mysqli_query($conn, $query);
    $output = '';
    $count = 0;
    if(mysqli_num_rows($result) > 0)
    {
        while($row = mysqli_fetch_array($result))
        {
            if($row['isNew'] == 'yes'){
                $count++;
            }

            if($row['alertUserId'] == $userId){
                $output .= '<a class="block px-4 py-2 text-gray-800 hover:bg-gray-200" href="./getAlertDetails.php?alertId=' . urlencode($row['alertId']) . '&status=booked">' . $row['sourceAddress'] . ': ' . $row['destinationAddress'] . '</a>';
            }
            else {
                $output .= '<a class="block px-4 py-2 text-gray-800 hover:bg-gray-200" href="./getRequestDetails.php?requestId=' . urlencode($row['requestId']) . '&status=booked">' . $row['sourceAddress'] . ': ' . $row['destinationAddress'] . '</a>';
            }
            $output .= '<div class="border-t border-gray-200"></div>'; // Add horizontal separator

        }
    }
    else {
        $output .= '<a class="block px-4 py-2 text-gray-800" href="#">No Notifications.</a>';
    }

    // $status_query = "SELECT * FROM poolmappings WHERE isNew = 'yes'";
    // $result_query = mysqli_query($conn, $status_query);
    // $count = mysqli_num_rows($result_query);
    $data = array(
        'notification' => $output,
        'unseen_notification'  => $count
    );

    echo json_encode($data);

}
?>
