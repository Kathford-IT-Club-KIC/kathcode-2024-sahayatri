<?php
    include 'Includes/dbcon.php';
    include 'Includes/session.php';

    $user_id = $_SESSION['userId'];
    $_SESSION['userId'] = 1;


    $bookedRequests = [];
    $availableRequests = [];
    $completedRequests = [];

    $sqlBooked = "SELECT * FROM poolrequests WHERE status = 'booked' AND userId=$user_id";
    $resultBooked = $conn->query($sqlBooked);
    if ($resultBooked->num_rows > 0) {
        while ($row = $resultBooked->fetch_assoc()) {
            $bookedRequests[] = $row;
        }
    }

    $sqlAvailable = "SELECT * FROM poolrequests  WHERE status = 'available' AND userId=$user_id";
    $resultAvailable = $conn->query($sqlAvailable);
    if ($resultAvailable->num_rows > 0) {
        while ($row = $resultAvailable->fetch_assoc()) {
            $availableRequests[] = $row;
        }
    }

    $sqlCompleted = "SELECT * FROM poolmappings INNER JOIN poolrequests on poolmappings.poolRequestId=poolrequests.id WHERE poolmappings.status = 'read' AND poolRequests.userId=$user_id";
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
    <title>My Pool Requests</title>
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 70%;
            margin-top: 0;
        }

        
        #controls {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            display: flex;
            gap: 0.5rem;
            background: rgba(109, 109, 109, 0.8);
            padding: 0.5rem;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .input-field {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 0.375rem;
            width: 400px;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #45a049;
        }

        
        .pac-container {
            background-color: #fff;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .pac-item {
            padding: 0.5rem;
            border-bottom: 1px solid #eaeaea;
            transition: background-color 0.3s;
        }

        .pac-item:hover {
            background-color: #f0f0f0;
        }

        .pac-item:first-child {
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .pac-item:last-child {
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }

        .pac-item-query {
            font-weight: bold;
        }

        #notificationDropdownMenu {
        position: absolute;
        top: 3rem; 
        right: 1rem;
        z-index: 1000; 
        width: 20rem; 
        background-color: white;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #cards-container {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        }

        .card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #2D2D2D;
            color: white;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #45a049;
        }

        body {
    font-family: Arial, sans-serif;
    background-color: #061D37;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#centeredTab {
    text-align: center;
    margin-bottom: 20px;
}

.tab {
    display: inline-block;
    padding: 10px 20px;
    cursor: pointer;
    background-color: #f0f0f0;
    border-radius: 5px 5px 0 0;
    margin-right: 10px;
}

.tab.active {
    background-color: #ddd;
}

.tabcontent {
    display: none;
}

.tabcontent.active {
    display: block;
}

.tabcontent h3 {
    margin-top: 0;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    cursor: pointer;
}

ul li:hover {
    background-color: #e9e9e9;
}

.details {
    cursor: pointer;
}

.details strong {
    font-weight: bold;
}

.h1{
   background-color:white;
   padding: 10px;
   margin:10px;
   font-family: Arial, sans-serif;

}
.content{
    max-width: 800px;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;

}

    </style>
</head>
<body >




<div class="content">
    <div class="centered-headline">
        <h1>सहयात्री</h1>
    </div>
    <div class="ml-56 flex-1 p-4 relative">
<div class="container">
    <div id="centeredTab">
        <div class="tab" onclick="openTab(event, 'Booked')">Booked</div>
        <div class="tab" onclick="openTab(event, 'Pending')">Pending</div>
        <div class="tab" onclick="openTab(event, 'Completed')">Completed</div>
    </div>

    <div id="Booked" class="tabcontent">
        <h3>Booked Requests</h3>
        <ul id="bookedList">
            <?php foreach ($bookedRequests as $request): ?>
                <li>
                    <div class="details" onclick="navigateToDetails(<?php echo $request['id']; ?>)">
                        <strong>Request ID:</strong> <?php echo $request['id']; ?><br>
                        <strong>Source:</strong> <?php echo $request['sourceAddress']; ?><br>
                        <strong>Destination:</strong> <?php echo $request['destinationAddress']; ?><br>
                        <strong>Date:</strong> <?php echo $request['date']; ?><br>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="Pending" class="tabcontent">
        <h3>Pending Requests</h3>
        <ul id="pendingList">
            <?php foreach ($availableRequests as $request): ?>
                <li>
                    <div class="details" onclick="navigateToDetails(<?php echo $request['id']; ?>)">
                        <strong>Request ID:</strong> <?php echo $request['id']; ?><br>
                        <strong>Source:</strong> <?php echo $request['sourceAddress']; ?><br>
                        <strong>Destination:</strong> <?php echo $request['destinationAddress']; ?><br>
                        <strong>Date:</strong> <?php echo $request['date']; ?><br>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="Completed" class="tabcontent">
        <h3>Completed Requests</h3>
        <ul id="completedList">
            <?php foreach ($completedRequests as $request): ?>
                <li>
                    <div class="details" onclick="navigateToDetails(<?php echo $request['id']; ?>)">
                        <strong>Request ID:</strong> <?php echo $request['id']; ?><br>
                        <strong>Source:</strong> <?php echo $request['sourceAddress']; ?><br>
                        <strong>Destination:</strong> <?php echo $request['destinationAddress']; ?><br>
                        <strong>Date:</strong> <?php echo $request['date']; ?><br>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
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
        
        window.location.href = 'getRequestDetails.php?id=' + requestId;
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".tab").click(); 

        var detailsBoxes = document.querySelectorAll(".details");
        detailsBoxes.forEach(function(box) {
            box.addEventListener("click", function(event) {
                event.stopPropagation();
                var requestId = this.querySelector('strong:first-child').textContent.trim().replace("Request ID: ", "");
                navigateToDetails(requestId);
            });
        });
    });
</script>

</body>
</html>