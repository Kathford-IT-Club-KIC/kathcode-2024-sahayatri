<?php
error_reporting(0);
include 'Includes/session.php';
include 'Includes/dbcon.php';

if (isset($_GET['requestId']) && isset($_GET['status'])) {
    $requestId = $_GET['requestId'];
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
        INNER JOIN users ON poolalerts.userId = users.id
        WHERE poolmappings.poolRequestId = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $requestId); // "i" denotes the type of the parameter (integer)
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $requests = $result->fetch_assoc();
        } else {
            $requests = null; // No request found
        }
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
        exit;
    }
} else {
    $requests = null; // ID not set
}

// Handle cancel request button click
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_request'])) {
    // Ensure requestId is set and numeric
    if (isset($_POST['requestId']) && is_numeric($_POST['requestId'])) {
        $requestId = $_POST['requestId'];
        
        // Delete from poolmappings first to maintain referential integrity
        $deleteMappingsSql = "DELETE FROM poolmappings WHERE poolRequestId = ?";
        $deleteMappingsStmt = $conn->prepare($deleteMappingsSql);
        
        if ($deleteMappingsStmt) {
            $deleteMappingsStmt->bind_param("i", $requestId);
            $deleteMappingsStmt->execute();
            $deleteMappingsStmt->close();
            
            // Now delete from poolrequests
            $deleteRequestSql = "DELETE FROM poolrequests WHERE id = ?";
            $deleteRequestStmt = $conn->prepare($deleteRequestSql);
            
            if ($deleteRequestStmt) {
                $deleteRequestStmt->bind_param("i", $requestId);
                $deleteRequestStmt->execute();
                $deleteRequestStmt->close();
                
                // Redirect or perform further actions after deletion
                header("Location: createPoolRequest.php");
                exit;
            } else {
                echo "Error deleting request: " . $conn->error;
            }
        } else {
            echo "Error deleting mappings: " . $conn->error;
        }
    } else {
        echo "Invalid request ID.";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Request Details</title>
    
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
    <?php if ($requests): ?>
        <!-- Pool Requests Details -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-2xl font-bold mb-4">Pool Requests Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold">Source Address:</span> <?php echo htmlspecialchars($requests['requestSourceAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Destination Address:</span> <?php echo htmlspecialchars($requests['requestDestinationAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Time & Date:</span>
                    <?php
                    // Convert time to 12-hour format
                    $time_12hr = date("h:i A", strtotime($requests['requestTime']));
                    // Display formatted time and date
                    echo htmlspecialchars($requests['requestDate']) . " " . htmlspecialchars($time_12hr);
                    ?>
                </div>
                <div>
                    <span class="font-semibold">Vehicle Type:</span> <?php echo htmlspecialchars($requests['requestVehicleType']); ?>
                </div>
                <div>
                    <span class="font-semibold">Applied Seats:</span> <?php echo htmlspecialchars($requests['requestAppliedSeats']); ?>
                </div>
                <div>
                    <span class="font-semibold">Price:</span> Rs. <?php echo htmlspecialchars($requests['price']); ?>
                </div>
                <?php
                    if($status == 'booked'){
                ?>
                <div>
                    <a style='width:30%;' href="khaltiPayment.php?requestId=<?php echo htmlspecialchars($requests['requestId']); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <img src="img/khaltiIcon.png" alt="Khalti Icon" class="w-7 h-8 mr-2"> Pay Via Khalti
                    </a>
                </div>

                <div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <input type="hidden" name="requestId" value="<?php echo htmlspecialchars($requests['requestId']); ?>">
                        <button type="submit" name="cancel_request" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Cancel Request
                        </button>
                    </form>
                </div>
                <?php
                    }
                ?>

            </div>
        </div>

        <!-- Alert Creator Details -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Pool Alert Creator Details</h2>
            <div class="flex items-center mb-4">
                <img src="<?php echo htmlspecialchars('img/user-' . $requests['userId'] . '.png'); ?>" 
                    alt="Profile Picture" 
                    class="w-16 h-16 rounded-full mr-4 cursor-pointer" 
                    onclick="window.location.href = 'userDetail.php?userId=<?php echo htmlspecialchars($requests['userId']); ?>';">
                <div>
                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($requests['userFirstName']) . " " . htmlspecialchars($requests['userLastName']); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($requests['userEmail']); ?></p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($requests['userPhone']); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="font-semibold">Source Address:</span> <?php echo htmlspecialchars($requests['alertSourceAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Destination Address:</span> <?php echo htmlspecialchars($requests['alertDestinationAddress']); ?>
                </div>
                <div>
                    <span class="font-semibold">Time & Date:</span>
                    <?php
                    // Convert time to 12-hour format
                    $time_12hr_alert = date("h:i A", strtotime($requests['alertTime']));
                    // Display formatted time and date
                    echo htmlspecialchars($requests['alertDate']) . " " . htmlspecialchars($time_12hr_alert);
                    ?>
                </div>
                <div>
                    <span class="font-semibold">Vehicle Type:</span> <?php echo htmlspecialchars($requests['alertVehicleType']); ?>
                </div>
                <div>
                    <span class="font-semibold">Advertised Seats:</span> <?php echo htmlspecialchars($requests['alertAdvertisedSeats']); ?>
                </div>
                <div>
                    <span class="font-semibold">Vacant Seats:</span> <?php echo htmlspecialchars($requests['alertVacantSeats']); ?>
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
