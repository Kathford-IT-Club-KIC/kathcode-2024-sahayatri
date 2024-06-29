<?php
include 'Includes/dbcon.php';
include 'Includes/sidebar.php';
include 'Includes/topbar.php';
include 'Includes/session.php';

$bookedRequests = [];
$availableRequests = [];
$completedRequests = [];

$sqlBooked = "SELECT * FROM poolrequests WHERE status = 'booked' AND userId=user_id";
$resultBooked = $conn->query($sqlBooked);
if ($resultBooked->num_rows > 0) {
    while ($row = $resultBooked->fetch_assoc()) {
        $bookedRequests[] = $row;
    }
}

$sqlAvailable = "SELECT * FROM poolrequests WHERE status = 'available' AND userId=user_id";
$resultAvailable = $conn->query($sqlAvailable);
if ($resultAvailable->num_rows > 0) {
    while ($row = $resultAvailable->fetch_assoc()) {
        $availableRequests[] = $row;
    }
}

$sqlCompleted = "SELECT * FROM poolmappings INNER JOIN poolrequests ON poolmappings.poolRequestId = poolrequests.id WHERE poolmappings.status = 'read' AND poolRequests.userId=user_id";
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
    <title>Tabbed Interface</title>
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
                    <h3 class="text-2xl font-bold text-center mb-4">Booked Requests</h3>
                    <?php foreach ($bookedRequests as $request): ?>
                        <div class="request-card" onclick="navigateToDetails(<?php echo $request['id']; ?>)">
                            <p><strong>Request ID:</strong> <?php echo $request['id']; ?></p>
                            <p><strong>Source:</strong> <?php echo $request['sourceAddress']; ?></p>
                            <p><strong>Destination:</strong> <?php echo $request['destinationAddress']; ?></p>
                            <p><strong>Date:</strong> <?php echo $request['date']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="Pending" class="tabcontent mt-4">
                    <h3 class="text-2xl font-bold text-center mb-4">Pending Requests</h3>
                    <?php foreach ($availableRequests as $request): ?>
                        <div class="request-card" onclick="navigateToDetails(<?php echo $request['id']; ?>)">
                            <p><strong>Request ID:</strong> <?php echo $request['id']; ?></p>
                            <p><strong>Source:</strong> <?php echo $request['sourceAddress']; ?></p>
                            <p><strong>Destination:</strong> <?php echo $request['destinationAddress']; ?></p>
                            <p><strong>Date:</strong> <?php echo $request['date']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="Completed" class="tabcontent mt-4">
                    <h3 class="text-2xl font-bold text-center mb-4">Completed Requests</h3>
                    <?php foreach ($completedRequests as $request): ?>
                        <div class="request-card" onclick="navigateToDetails(<?php echo $request['id']; ?>)">
                            <p><strong>Request ID:</strong> <?php echo $request['id']; ?></p>
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

    function navigateToDetails(requestId) {
        console.log("Navigating to details for Request ID:", requestId); 
        var url = `myPoolRequestsDetailss.php?id=${requestId}`;
        console.log("Navigating to:", url); 
        
        window.location.href = url;
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".tab").click(); // Clicks the first tab on page load
    });
</script>

</body>
</html>
