<?php
include('Includes/dbcon.php');
include('Includes/session.php');
$sourceAddress = '';
$sourceLatitude = '';
$sourceLongitude ='';
$destinationAddress = '';
$destinationLatitude ='';
$destinationLongitude='';
$vehicleType = '';
$createdDate = '';
$updatedDate = '';
$vacantSeats='';
$status='';
$advertisedSeats='';

if (isset($_GET['AlertId'])) {
    $id = $_GET['AlertId'];
    $query = "SELECT * FROM poolalerts WHERE id=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $sourceAddress = $user['sourceAddress'];
        $sourceLatitude = $user['sourceLatitude'];
        $sourceLongitude = $user['sourceLongitude'];
        $destinationAddress = $user['destinationAddress'];
        $destinationLatitude = $user['destinationLatitude'];
        $destinationLongitude=$user['destinationLongitude'];
        $vehicleType = $user['vehicleType'];
        $vacantSeats=$user['vacantSeats'];
        $advertisedSeats=$user['advertisedSeats'];
        $time = new DateTime($user['time']);
        $time_12hr_format = $time->format('g:i A');
        $date = $user['date'].' '.$time_12hr_format;
        $updatedDate = $user['updatedDate'];
        $status=$user['status'];
    }
    else {
        echo "User not found."; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - <?php echo $first_name . ' ' . $last_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen bg-gray-900 text-white"
    <?php include './Includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <!-- Include the top bar -->
        <?php include './Includes/topbar.php'; ?>

        <!-- User Detail Content -->
        <div class="flex flex-1 justify-center items-center p-6">
    <div class="max-w-md w-full bg-gray-800 rounded-lg shadow-lg p-8">
        <div class="flex flex-col items-center">
            <table class="table-auto w-full text-left">
                <tbody>
                    <tr>
                        <th class="px-4 py-2 text-lg font-semibold">Source Address:</th>
                        <td class="px-4 py-2 text-lg"><?php echo htmlspecialchars($sourceAddress); ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-lg font-semibold">Destination Address:</th>
                        <td class="px-4 py-2 text-lg"><?php echo htmlspecialchars($destinationAddress); ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-lg font-semibold">Vehicle Type:</th>
                        <td class="px-4 py-2 text-lg"><?php echo htmlspecialchars($vehicleType); ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-lg font-semibold">Vacant Seats:</th>
                        <td class="px-4 py-2 text-lg"><?php echo htmlspecialchars($vacantSeats); ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-lg font-semibold">Advertised Seats:</th>
                        <td class="px-4 py-2 text-lg"><?php echo htmlspecialchars($advertisedSeats); ?></td>
                    </tr>

                    <tr>
                        <th class="px-4 py-2 text-lg font-semibold">Date:</th>
                        <td class="px-4 py-2 text-lg"><?php echo htmlspecialchars($date); ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-lg font-semibold">Status:</th>
                        <td class="px-4 py-2 text-lg"><?php echo htmlspecialchars($status); ?></td>
                    </tr>
                    
                </tbody>
            </table>
            <div class="container mx-auto">
    <div class="flex justify-center pt-4">
        <form method="POST" action="">
            <button type="submit" name="delete" class="w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:bg-blue-700">
                Cancel
            </button>
        </form>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $alertId = $id;

    // Function to delete record
    function deleteRecord($conn, $id_name, $table, $alertId) {
        $sql = "DELETE FROM $table WHERE $id_name = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $alertId);
            if ($stmt->execute()) {
                // echo "Record deleted successfully from $table<br>";
            } else {
                echo "Error deleting record from $table: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            echo "Error preparing statement for $table: " . $conn->error . "<br>";
        }
    }

    // Delete records from both tables
    deleteRecord($conn, "poolAlertId", "poolmappings", $alertId);
    deleteRecord($conn, "id", "poolalerts", $alertId);



    // Close connection
    $conn->close();

    echo '<script>window.location.href="createPoolRequest.php";</script>';
}
?>

</body>

</html>