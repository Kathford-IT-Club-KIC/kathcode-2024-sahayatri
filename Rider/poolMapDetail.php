<?php
error_reporting(0);
include 'Includes/session.php';
include 'Includes/dbcon.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];

    if (isset($_GET['action']) && $_GET['action'] == 'delete') {
        if ($type == 'request') {
            $sql = "DELETE FROM poolrequests WHERE id = ?";
        } else {
            $sql = "DELETE FROM poolalerts WHERE id = ?";
        }

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: createPoolRequest.php");
        exit();
    } else {
        if ($type == 'request') {
            $sql = "SELECT * FROM poolrequests WHERE id = $id";
        } else {
            $sql = "SELECT * FROM poolalerts WHERE id = $id";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $mapping = $result->fetch_assoc();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Pool Detail</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBP7f6xDXdAIWFyvv42s6nu35qwem7nMQ4&libraries=places,geometry&callback=getUserLocation" defer></script>
    
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
            <?php if (isset($mapping)): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Pool Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-semibold">Source Address:</span> <?php echo htmlspecialchars($mapping['sourceAddress']); ?>
                        </div>
                        <div>
                            <span class="font-semibold">Destination Address:</span> <?php echo htmlspecialchars($mapping['destinationAddress']); ?>
                        </div>
                        <div>
                            <span class="font-semibold">Vehicle Type:</span> <?php echo htmlspecialchars($mapping['vehicleType']); ?>
                        </div>
                        <div>
                            <span class="font-semibold">Time:</span> <?php echo htmlspecialchars($mapping['time']); ?>
                        </div>
                        <div>
                            <span class="font-semibold">Date:</span> <?php echo htmlspecialchars($mapping['date']); ?>
                        </div>
                        <div>
                            <span class="font-semibold">Created Date:</span> <?php echo htmlspecialchars($mapping['createdDate']); ?>
                        </div>
                    </div>
                    <a href="?id=<?php echo $id; ?>&type=<?php echo $type; ?>&action=delete" class="mt-4 bg-red-500 text-white px-4 py-2 rounded inline-block">Cancel</a>
                </div>
            <?php else: ?>
                <div class="bg-red-100 p-4 rounded-lg text-red-700">
                    No mapping details found for the given ID.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
