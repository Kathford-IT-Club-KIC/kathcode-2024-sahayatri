<?php
error_reporting(0);
include 'Includes/session.php';
include 'Includes/dbcon.php';

if (isset($_GET['alertId']) && isset($_GET['status'])) {
    $alertId = $_GET['alertId'];
    $status = $_GET['status'];
    
    // Use prepared statements to prevent SQL injection
    $sql = "SELECT poolrequests.id AS requestId,
               poolrequests.userId AS requestUserId,
               poolrequests.sourceAddress AS requestSourceAddress,
               poolrequests.sourceLatitude AS requestSourceLatitude,
               poolrequests.sourceLongitude AS requestSourceLongitude,
               poolrequests.destinationAddress AS requestDestinationAddress,
               poolrequests.destinationLatitude AS requestDestinationLatitude,
               poolrequests.destinationLongitude AS requestDestinationLongitude,
               poolrequests.vehicleType AS requestVehicleType,
               poolrequests.appliedSeats AS requestAppliedSeats,
               poolrequests.time AS requestTime,
               price,
               poolrequests.date AS requestDate,
               poolrequests.status AS requestStatus,
               poolrequests.createdDate AS requestCreatedDate,
               poolrequests.updatedDate AS requestUpdatedDate,
               poolalerts.id AS alertId,
               poolalerts.userId AS alertUserId,
               poolalerts.sourceAddress AS alertSourceAddress,
               poolalerts.sourceLatitude AS alertSourceLatitude,
               poolalerts.sourceLongitude AS alertSourceLongitude,
               poolalerts.destinationAddress AS alertDestinationAddress,
               poolalerts.destinationLatitude AS alertDestinationLatitude,
               poolalerts.destinationLongitude AS alertDestinationLongitude,
               poolalerts.vehicleType AS alertVehicleType,
               poolalerts.vacantSeats AS alertVacantSeats,
               poolalerts.advertisedSeats AS alertAdvertisedSeats,
               poolalerts.time AS alertTime,
               poolalerts.date AS alertDate,
               poolalerts.status AS alertStatus,
               poolalerts.createdDate AS alertCreatedDate,
               poolalerts.updatedDate AS alertUpdatedDate,
               users.id AS userId,
               users.firstName AS userFirstName,
               users.lastName AS userLastName,
               users.email AS userEmail,
               users.phone AS userPhone
        FROM poolmappings
        INNER JOIN poolrequests ON poolmappings.poolRequestId = poolrequests.id
        INNER JOIN poolalerts ON poolmappings.poolAlertId = poolalerts.id
        INNER JOIN users ON poolrequests.userId = users.id
        WHERE poolmappings.poolAlertId = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $alertId); // "i" denotes the type of the parameter (integer)
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $alert = $result->fetch_assoc();
        } else {
            $alert = null; // No request found
        }
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
        exit;
    }
} else {
    $alert = null; // ID not set
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alertId'])) {
    $alertId = $_POST['alertId'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete from poolmappings
        $sql_delete_mappings = "DELETE FROM poolmappings WHERE poolAlertId = ?";
        $stmt_delete_mappings = $conn->prepare($sql_delete_mappings);

        if ($stmt_delete_mappings) {
            $stmt_delete_mappings->bind_param("i", $alertId);
            $stmt_delete_mappings->execute();
            $stmt_delete_mappings->close();
        } else {
            throw new Exception("Error preparing the statement for poolmappings: " . $conn->error);
        }

        // Delete from poolalerts
        $sql_delete_alert = "DELETE FROM poolalerts WHERE id = ?";
        $stmt_delete_alert = $conn->prepare($sql_delete_alert);

        if ($stmt_delete_alert) {
            $stmt_delete_alert->bind_param("i", $alertId);
            $stmt_delete_alert->execute();
            if ($stmt_delete_alert->affected_rows > 0) {
                // Commit transaction
                $conn->commit();
                header("Location: createPoolRequest.php");
            } else {
                throw new Exception("Failed to delete alert");
            }
            $stmt_delete_alert->close();
        } else {
            throw new Exception("Error preparing the statement for poolalerts: " . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header("Location: createPoolRequest.php");
    }
    exit; // Stop further execution after handling the form submission
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Alert Details</title>
    
    <link rel="stylesheet" href="styles/styles.css">
    
    <!-- Include Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/all.min.css">
    <link rel="icon" href="img/logo/favicon.png">

</head>

<body class="flex flex-col h-screen">
<?php include './Includes/topbar.php'; ?>

<div class="flex flex-1">
    <!-- Include the sidebar -->
    <?php include './Includes/sidebar.php'; ?>

    <!-- Main content -->
    <div class="ml-56 flex-1 p-4 relative">
    <?php if ($alert): ?>
        <!-- Alert Creator Details -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-2xl font-bold mb-4">Pool Alert Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold">Source Address:</span> <?php echo htmlspecialchars($alert['alertSourceAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Destination Address:</span> <?php echo htmlspecialchars($alert['alertDestinationAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Time & Date:</span>
                    <?php
                    // Convert time to 12-hour format
                    $time_12hr_alert = date("h:i A", strtotime($alert['alertTime']));
                    // Display formatted time and date
                    echo htmlspecialchars($alert['alertDate']) . " " . htmlspecialchars($time_12hr_alert);
                    ?>
                </div>
                <div>
                    <span class="font-semibold">Vehicle Type:</span> <?php echo htmlspecialchars($alert['alertVehicleType']); ?>
                </div>
                <div>
                    <span class="font-semibold">Advertised Seats:</span> <?php echo htmlspecialchars($alert['alertAdvertisedSeats']); ?>
                </div>
                <div>
                    <span class="font-semibold">Vacant Seats:</span> <?php echo htmlspecialchars($alert['alertVacantSeats']); ?>
                </div>

                <?php
                    if($status == 'booked'){
                ?>
                <!-- Cancel Button -->
                <form method="post" class="mt-4">
                    <input type="hidden" name="alertId" value="<?php echo htmlspecialchars($alert['alertId']); ?>">
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                </form>

                <?php
                    }
                ?>
            </div>
        </div>

         <!-- Pool Requests Details -->
         <div class="bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-2xl font-bold mb-4">Pool Request Creater Details</h2>
            <div class="flex items-center mb-4">
            <img src="<?php echo htmlspecialchars('img/user-' . $alert['userId'] . '.png'); ?>" 
     alt="Profile Picture" 
     class="w-16 h-16 rounded-full mr-4 cursor-pointer" 
     onclick="window.location.href = 'userDetail.php?userId=<?php echo htmlspecialchars($alert['userId']); ?>';">

                <div>
                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($alert['userFirstName']) . " " . htmlspecialchars($alert['userLastName']); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($alert['userEmail']); ?></p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($alert['userPhone']); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold">Source Address:</span> <?php echo htmlspecialchars($alert['requestSourceAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Destination Address:</span> <?php echo htmlspecialchars($alert['requestDestinationAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Time & Date:</span>
                    <?php
                    // Convert time to 12-hour format
                    $time_12hr = date("h:i A", strtotime($alert['requestTime']));
                    // Display formatted time and date
                    echo htmlspecialchars($alert['requestDate']) . " " . htmlspecialchars($time_12hr);
                    ?>
                </div>
                <div>
                    <span class="font-semibold">Vehicle Type:</span> <?php echo htmlspecialchars($alert['requestVehicleType']); ?>
                </div>
                <div>
                    <span class="font-semibold">Applied Seats:</span> <?php echo htmlspecialchars($alert['requestAppliedSeats']); ?>
                </div>
                <div>
                    <span class="font-semibold">Price:</span> Rs. <?php echo htmlspecialchars($alert['price']); ?>
    </div>
            </div>
        </div>

    <?php else: ?>
        <div class="bg-red-100 p-4 rounded-lg text-red-700">
            No requests details found for the given ID.
        </div>
    <?php endif; ?>
</div>

</div>
</body>

</html>
