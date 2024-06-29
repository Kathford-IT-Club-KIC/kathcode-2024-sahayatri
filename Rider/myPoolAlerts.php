<?php
session_start();
if (!isset($_SESSION['userId'])) {
    die('User not logged in');
}
$userId = $_SESSION['userId'];

include 'Includes/dbcon.php';
include 'Includes/sidebar.php';
include 'Includes/topbar.php';

$bookedRequests = [];
$availableRequests = [];
$completedRequests = [];

$sqlBooked = "SELECT * FROM poolalerts WHERE status = 'booked' AND userId= $userId";
$resultBooked = $conn->query($sqlBooked);
if ($resultBooked->num_rows > 0) {
    while ($row = $resultBooked->fetch_assoc()) {
        $bookedRequests[] = $row;
    }
}

$sqlAvailable = "SELECT * FROM poolalerts WHERE status = 'available' AND userId= $userId";
$resultAvailable = $conn->query($sqlAvailable);
if ($resultAvailable->num_rows > 0) {
    while ($row = $resultAvailable->fetch_assoc()) {
        $availableRequests[] = $row;
    }
}

$sqlCompleted = "SELECT * FROM poolmappings INNER JOIN poolalerts ON poolmappings.poolAlertId = poolalerts.id WHERE poolmappings.completed = 'yes' AND poolalerts.userId=$userId";
$resultCompleted = $conn->query($sqlCompleted);
if ($resultCompleted->num_rows > 0) {
    while ($row = $resultCompleted->fetch_assoc()) {
        $completedRequests[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pool Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        .tabcontent {
            display: none;
        }

        .tabcontent.active {
            display: block;
        }

        .request-card {
            background-color: #4a5568;
            color: white;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center; 
            margin: 10 auto; 
            width: 50%; 
            cursor: pointer;
        }
    </style>
    <link rel="icon" href="img/logo/favicon.png">

</head>
<body class="bg-gray-900 text-white">

<div class="flex h-screen">
    <div class="flex-1 flex flex-col">
        <div class="container mx-auto py-10 flex-1">
            <!-- <div class="text-center">
                <h1 class="text-4xl font-bold">सहयात्री</h1>
            </div> -->
            <div class="mt-10">
                <div class="flex justify-center">
                    <div class="flex">
                        <div class="tab px-6 py-3 bg-gray-800 cursor-pointer" onclick="openTab(event, 'Booked')">Booked</div>
                        <div class="tab px-6 py-3 bg-gray-800 cursor-pointer" onclick="openTab(event, 'Pending')">Pending</div>
                        <div class="tab px-6 py-3 bg-gray-800 cursor-pointer" onclick="openTab(event, 'Completed')">Completed</div>
                    </div>
                </div>
                <div id="Booked" class="tabcontent mt-4">
                    <h3 class="text-2xl font-bold text-center mb-4">Booked Alerts</h3>
                    <?php foreach ($bookedRequests as $request): ?>
                        <div class="request-card" onclick="navigateToDetails('booked', <?php echo $request['id']; ?>)">
                            <p><strong>Alert ID:</strong> <?php echo $request['id']; ?></p>
                            <p><strong>Source:</strong> <?php echo $request['sourceAddress']; ?></p>
                            <p><strong>Destination:</strong> <?php echo $request['destinationAddress']; ?></p>
                            <p><strong>Date:</strong> <?php echo $request['date']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="Pending" class="tabcontent mt-4">
                    <h3 class="text-2xl font-bold text-center mb-4">Pending Alerts</h3>
                    <?php foreach ($availableRequests as $request): ?>
                        <div class="request-card" onclick="navigateToDetails('pending', <?php echo $request['id']; ?>)">
                            <p><strong>Alert ID:</strong> <?php echo $request['id']; ?></p>
                            <p><strong>Source:</strong> <?php echo $request['sourceAddress']; ?></p>
                            <p><strong>Destination:</strong> <?php echo $request['destinationAddress']; ?></p>
                            <p><strong>Date:</strong> <?php echo $request['date']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="Completed" class="tabcontent mt-4">
                    <h3 class="text-2xl font-bold text-center mb-4">Completed Alerts</h3>
                    <?php foreach ($completedRequests as $request): ?>
                        <div class="request-card" onclick="navigateToDetails('completed', <?php echo $request['id']; ?>)">
                            <p><strong>Alert ID:</strong> <?php echo $request['id']; ?></p>
                            <p><strong>Source:</strong> <?php echo $request['sourceAddress']; ?></p>
                            <p><strong>Destination:</strong> <?php echo $request['destinationAddress']; ?></p>
                            <p><strong>Date:</strong> <?php echo $request['date']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        tablinks = document.getElementsByClassName("tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    function navigateToDetails(status, requestId) {
        if (status === 'booked') {
            var url = `getAlertDetails.php?alertId=${requestId}&status=booked`;
        } else if (status === 'pending') {
            var url = `poolMapDetail.php?id=${requestId}&type=alert`;
        } else if (status === 'completed') {
            var url = `getAlertDetails.php?alertId=${requestId}&status=completed`;
        }
        window.location.href = url;
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".tab").click(); // Clicks the first tab on page load
    });
</script>

</body>
</html>
