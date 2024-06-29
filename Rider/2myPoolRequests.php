<?php
error_reporting(E_ALL);
include 'Includes/session.php';
include 'Includes/dbcon.php';

if(isset($_GET['mappingId']))
{
    $mappingId = $_GET['mappingId'];
    $sql = "SELECT * FROM poolmappings WHERE id = $mappingId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $mapping = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Pool Requests</title>
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
    <div class="flex-1 p-4">
        <div class="tabs mb-4">
            <button class="tabButton" onclick="showTab('booked')">Booked</button>
            <button class="tabButton" onclick="showTab('pending')">Pending</button>
            <button class="tabButton" onclick="showTab('completed')">Completed</button>
        </div>

        <div id="booked" class="tabContent">
            <?php
            $userId = $_SESSION['userId'];
            $sql = "SELECT * FROM poolrequests WHERE userId = $userId AND status = 'booked'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card bg-gray-900 text-white p-4 mb-4 rounded-lg shadow-md'>";
                    echo "<a href='getPoolDetail.php?requestId=" . $row['id'] . "'>";
                    echo "<span class='text-lg'>" . $row['sourceAddress'] . " - " . $row['destinationAddress'] . " | Seats : " . $row['appliedSeats'] . "</span>";
                    echo "</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No booked requests found.</p>";
            }
            ?>
        </div>

        <div id="pending" class="tabContent" style="display:none;">
            <?php
            $sql = "SELECT * FROM poolrequests WHERE userId = $userId AND status = 'available'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card bg-gray-900 text-white p-4 mb-4 rounded-lg shadow-md'>";
                    echo "<a href='getPoolDetail.php?requestId=" . $row['id'] . "'>";
                    echo "<span class='text-lg'>" . $row['sourceAddress'] . " - " . $row['destinationAddress'] . " | Seats : " . $row['appliedSeats'] . "</span>";
                    echo "</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No pending requests found.</p>";
            }
            ?>
        </div>

        <div id="completed" class="tabContent" style="display:none;">
            <?php
            $sql = "SELECT * FROM poolmappings INNER JOIN poolrequests ON poolmappings.poolRequestId = poolrequests.id WHERE poolrequests.userId = $userId AND poolmappings.completed = 'yes'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card bg-gray-900 text-white p-4 mb-4 rounded-lg shadow-md'>";
                    echo "<a href='getPoolDetail.php?requestId=" . $row['poolRequestId'] . "'>";
                    echo "<span class='text-lg'>" . $row['sourceAddress'] . " - " . $row['destinationAddress'] . " | Seats : " . $row['appliedSeats'] . "</span>";
                    echo "</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No completed requests found.</p>";
            }
            ?>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        var i;
        var x = document.getElementsByClassName("tabContent");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        document.getElementById(tabName).style.display = "block";
    }
</script>

</body>
</html>
