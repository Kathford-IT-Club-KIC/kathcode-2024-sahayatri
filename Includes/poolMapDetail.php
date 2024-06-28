<?php
error_reporting(E_ALL);
include 'Includes/session.php';
include 'Includes/dbcon.php';

if(isset($_GET['mappingId']))
{
    $mappingId = $_GET['mappingId'];
    $sql = "SELECT * FROM poolmapping WHERE id = $mappingId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $mapping = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Pool Alert</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBP7f6xDXdAIWFyvv42s6nu35qwem7nMQ4&libraries=places,geometry&callback=getUserLocation" defer></script>
    
    <link rel="stylesheet" href="styles/styles.css">
    
    <!-- Include Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/all.min.css">

</head>

<body class="flex flex-col h-screen">
<?php include './Includes/topbarAlert.php'; ?>

<div class="flex flex-1">
    <!-- Include the sidebar -->
    <?php include './Includes/sidebar.php'; ?>

    <!-- Main content -->
    <div class="ml-56 flex-1 p-4 relative">
        <?php if ($mapping): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Pool Mapping Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-semibold">ID:</span> <?php echo htmlspecialchars($mapping['id']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Pool Request ID:</span> <?php echo htmlspecialchars($mapping['poolRequestId']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Pool Alert ID:</span> <?php echo htmlspecialchars($mapping['poolAlertId']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Price:</span> <?php echo htmlspecialchars($mapping['price']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Completed:</span> <?php echo htmlspecialchars($mapping['completed']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Status:</span> <?php echo htmlspecialchars($mapping['status']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Is New:</span> <?php echo htmlspecialchars($mapping['isNew']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Created Date:</span> <?php echo htmlspecialchars($mapping['createdDate']); ?>
                    </div>
                    <div>
                        <span class="font-semibold">Updated Date:</span> <?php echo htmlspecialchars($mapping['updatedDate']); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-red-100 p-4 rounded-lg text-red-700">
                No mapping details found for the given ID.
            </div>
        <?php endif; ?>

        <div id="map" class="mt-4"></div>
        <div id="cards-container" class="mt-4 space-y-4"></div>

        <div id="controls" class="flex flex-col md:flex-row items-center justify-center space-y-2 md:space-y-0 md:space-x-4 p-4 bg-white bg-opacity-75 rounded-lg shadow-lg">
            <input id="origin" type="text" placeholder="Enter origin" class="input-field px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input id="destination" type="text" placeholder="Enter destination" class="input-field px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="datetime-local" id="datetime" name="datetime" required class="mt-1 block w-64 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <button id="show-route" class="button px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="showRoute()">Show Route</button>
        </div>
    </div>
</div>


    <script>
        var i = -1;
        var map;
        var inputOrigin;
        var inputDestination;
        var autocompleteOrigin;
        var autocompleteDestination;
        var currentDirectionRendererMain = null;
        var currentDirectionRenderer = null;
        var highlightedCard = null;
        var pricePerKM = 10;

        function resetButton() {
            var confirmRouteButton = document.getElementById('confirm-route');
            confirmRouteButton.id = 'show-route';
            confirmRouteButton.innerText = 'Show Route';
            confirmRouteButton.classList.remove('bg-green-500', 'hover:bg-green-600', 'focus:ring-green-500');
            confirmRouteButton.classList.add('bg-blue-500', 'hover:bg-blue-600', 'focus:ring-blue-500');
            confirmRouteButton.onclick = showRoute;
        }

        function resetInputs() {
            document.getElementById('origin').value = '';
            document.getElementById('destination').value = '';

            // Reset the autocomplete objects
            autocompleteOrigin = new google.maps.places.Autocomplete(inputOrigin);
            autocompleteDestination = new google.maps.places.Autocomplete(inputDestination);
        }

        function renderPolyline(source, destination) {
            if (currentDirectionRenderer) {
                currentDirectionRenderer.setMap(null); // Remove the current direction renderer
            }

            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                polylineOptions: {  
                    strokeColor: 'red',
                    strokeWeight: 6
                }
            });

            directionsService.route({
                origin: source,
                destination: destination,
                travelMode: 'DRIVING'
            }, function(result, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    currentDirectionRenderer = directionsRenderer;
                } else {
                    console.log('Directions request failed due to ' + status);
                }
            });
        }

        function highlightCard(card) {
            if (highlightedCard) {
                highlightedCard.classList.remove('bg-yellow-500'); // Remove highlight from the previous card
                highlightedCard.classList.add('bg-gray-900');
            }

            card.classList.remove('bg-gray-900');
            card.classList.add('bg-yellow-500'); // Highlight the current card
            highlightedCard = card;
        }

        function displayRequest(alertId, requestId, source, destination, overlapDistance, vacantSeats) {
            fetch(`Includes/getRequestData.php?requestId=${requestId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        var cardsContainer = document.getElementById('cards-container');
                        
                        data.forEach((request, i) => {
                            var card = document.createElement('div');
                            card.className = 'card bg-gray-900 text-white p-4 rounded-lg shadow-md opacity-0 transform translate-y-10 animate-slide-up';

                            // Create user profile picture
                            var profilePic = document.createElement('img');
                            profilePic.src = `img/user-${request.userId}.png`; // Replace with actual image format
                            profilePic.className = 'rounded-full h-10 w-10 cursor-pointer'; // Adjust size as needed
                            profilePic.onclick = function() {
                                window.location.href = `userDetail.php?userId=${request.userId}`;
                            };

                            // Create card text content
                            var cardText = document.createElement('span');
                            var price = overlapDistance * pricePerKM;
                            cardText.innerText = `${request.firstName} ${request.lastName} | ${request.sourceAddress} - ${request.destinationAddress} | Seats : ${request.appliedSeats} | Price: Rs. ${price.toFixed(2)}`;
                            cardText.className = 'text-lg';

                            // Create accept button
                            var acceptButton = document.createElement('button');
                            acceptButton.innerText = 'Accept';
                            acceptButton.className = 'button px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500';
                            acceptButton.onclick = function() {
                                fetch('Includes/mapPool.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ requestId: requestId, alertId: alertId, appliedSeats: request.appliedSeats, price: price.toFixed(2), vacantSeats: vacantSeats})
                                })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.success) {
                                        window.location.href = `poolMapDetail.php?id=${result.mappingId}`;
                                    } else {
                                        alert(`Error: ${result.error}`);
                                    }
                                });
                            };

                            card.onclick = function() {
                                renderPolyline(source, destination);
                                highlightCard(card);
                            };

                            // Append elements to card
                            card.appendChild(profilePic);
                            card.appendChild(cardText);
                            card.appendChild(acceptButton);

                            cardsContainer.appendChild(card);

                            // Delay animation for each card to create a sequential effect
                            setTimeout(() => {
                                card.classList.remove('opacity-0', 'translate-y-10');
                            }, i * 100);
                        });
                    }
                });
        }

        function calculateOverlap(route1, route2, tolerance = 10) {
            let overlapDistance = 0.0;
        
            route1.forEach((point1, index1) => {
            route2.forEach((point2, index2) => {
                const distance = google.maps.geometry.spherical.computeDistanceBetween(point1, point2);
                if (distance < tolerance) {
                // Use the distance between sequential points to estimate segment length
                if (index1 < route1.length - 1 && index2 < route2.length - 1) {
                    const nextPoint1 = route1[index1 + 1];
                    const segmentDistance = google.maps.geometry.spherical.computeDistanceBetween(point1, nextPoint1);
                    overlapDistance += segmentDistance / 1000; // Convert to kilometers
                }
                }
            });
            });
        
            return overlapDistance;
        }

        // Get the route from the Directions API
        function getRoute(directionsService, origin, destination, callback) {
            directionsService.route(
            {
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
            },
            (response, status) => {
                if (status === "OK") {
                callback(response.routes[0].overview_path);
                } else {
                console.error("Directions request failed due to " + status);
                }
            }
            );
        }

        function bestPoolRequests(alertId, origin_lat, origin_lng, destination_lat, destination_lng) {
            fetch(`Includes/getAlertData.php?alertId=${alertId}`)
                .then(response => response.json())
                .then(poolAlert => {
                    if (poolAlert.length === 0) {
                        console.log('No information for the Alert found.');
                        return;
                    }

                    poolAlert = poolAlert[0]; // Assuming there's only one alert and it's the first item

                    var poolRequests = <?php
                        $sql = "SELECT * FROM poolrequests";
                        $result = $conn->query($sql);

                        $locations = [];
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $locations[] = [
                                    'source' => [
                                        'lat' => $row["sourceLatitude"],
                                        'lng' => $row["sourceLongitude"]
                                    ],
                                    'destination' => [
                                        'lat' => $row["destinationLatitude"],
                                        'lng' => $row["destinationLongitude"]
                                    ],
                                    'id' => $row["id"],
                                    'date' => $row["date"],
                                    'time' => $row["time"],
                                    'vehicleType' => $row["vehicleType"],
                                    'appliedSeats' => $row["appliedSeats"],
                                    'status' => $row["status"]
                                ];
                            }
                            echo json_encode($locations);
                        } else {
                            echo json_encode([]);
                        }
                    ?>;

                    var alertOrigin = new google.maps.LatLng(origin_lat, origin_lng);
                    var alertDestination = new google.maps.LatLng(destination_lat, destination_lng);

                    var directionsService = new google.maps.DirectionsService();
                    var alert = {
                        origin: alertOrigin,
                        destination: alertDestination,
                        travelMode: 'DRIVING'
                    };

                    directionsService.route(alert, function (result, status) {
                        if (status === 'OK') {
                            var path = result.routes[0].overview_path;
                            var polyline = new google.maps.Polyline({ path: path });

                            var requestsWithOverlap = [];

                            var processRequest = function(index) {
                                if (index >= poolRequests.length) {
                                    // Sort the requests based on the overlap distance in descending order
                                    requestsWithOverlap.sort((a, b) => b.overlapDistance - a.overlapDistance);

                                    // Display the sorted requests
                                    var cardsContainer = document.getElementById('cards-container');
                                    requestsWithOverlap.forEach(function(request) {
                                        displayRequest(alertId, request.poolRequest.id, request.source, request.destination, request.overlapDistance, poolAlert.vacantSeats);
                                    });
                                    return;
                                }

                                var poolRequest = poolRequests[index];

                                var requestDateTime = new Date(poolRequest.date + ' ' + poolRequest.time);
                                var alertDateTime = new Date(poolAlert.date + ' ' + poolAlert.time);


                                // Calculate the time difference in minutes
                                var timeDifference = Math.abs(requestDateTime - alertDateTime) / (1000 * 60);


                                if (poolRequest.status === 'booked') {
                                    processRequest(index + 1); // Move to the next request
                                    return;
                                }

                                if (timeDifference > 10) {
                                    processRequest(index + 1); // Move to the next request
                                    return;
                                }

                                if (poolRequest.vehicleType !== poolAlert.vehicleType) {
                                    processRequest(index + 1); // Move to the next request
                                    return;
                                }

                                if (poolRequest.appliedSeats > poolAlert.vacantSeats) {
                                    processRequest(index + 1); // Move to the next request
                                    return;
                                }

                                var source = new google.maps.LatLng(poolRequest.source.lat, poolRequest.source.lng);
                                var destination = new google.maps.LatLng(poolRequest.destination.lat, poolRequest.destination.lng);

                                // Check if the given location is within 5km of the polyline
                                if (google.maps.geometry.poly.isLocationOnEdge(source, polyline, 0.01133) && google.maps.geometry.poly.isLocationOnEdge(destination, polyline, 0.01133)) {
                                    // Get routes for both sets of origins and destinations
                                    getRoute(directionsService, alertOrigin, alertDestination, (route1) => {
                                        getRoute(directionsService, source, destination, (route2) => {
                                            const overlapDistance = calculateOverlap(route1, route2);
                                            requestsWithOverlap.push({
                                                poolRequest: poolRequest,
                                                source: source,
                                                destination: destination,
                                                overlapDistance: overlapDistance
                                            });
                                            processRequest(index + 1); // Move to the next request
                                        });
                                    });
                                } else {
                                    console.log('Location is not within 5km of this polyline');
                                    processRequest(index + 1); // Move to the next request
                                }
                            };

                            processRequest(0); // Start processing requests
                        } else {
                            console.error('Directions request failed due to ' + status);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching pool alert:', error);
                });
        }



        function confirmRoute() {
            var datetimeValue = document.getElementById('datetime').value;
            var vehicleType = document.getElementById('vehicleType').value;
            var advertisedSeats = document.getElementById('advertisedSeats').value;

            if (datetimeValue === '' || vehicleType === '' || advertisedSeats === '') {
                alert('Please fill in all the fields.');
                return;
            }

            // Save the route to the database
            var origin = autocompleteOrigin.getPlace();
            var destination = autocompleteDestination.getPlace();

            // convert the origin and destination to lat and long
            var origin_lat = origin.geometry.location.lat();
            var origin_lng = origin.geometry.location.lng();
            var destination_lat = destination.geometry.location.lat();
            var destination_lng = destination.geometry.location.lng();
     
            // Data to be sent to the server
            var dateValue = datetimeValue.split('T')[0];
            var timeValue = datetimeValue.split('T')[1];

            var data = {
                sourceAddress: origin.formatted_address,
                sourceLatitude: origin_lat,
                sourceLongitude: origin_lng,
                destinationAddress: destination.formatted_address,
                destinationLatitude: destination_lat,
                destinationLongitude: destination_lng,
                vehicleType: document.getElementById('vehicleType').value,
                advertisedSeats: document.getElementById('advertisedSeats').value,
                date: dateValue,
                time: timeValue
            };

            // AJAX request to storePoolAlert.php
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'Includes/storePoolAlert.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            var cardsContainer = document.getElementById('cards-container');
                            cardsContainer.innerHTML = ''; // Clear any existing cards
                            bestPoolRequests(response.alertId, origin_lat, origin_lng, destination_lat, destination_lng);
                        } else {
                            console.log('Error:', response.message);
                        }
                    } else {
                        console.log('Request failed. Returned status of ' + xhr.status);
                    }
                }
            };
            xhr.send(JSON.stringify(data));

            resetButton();
            resetInputs();
        }

        function showRoute() {
            if (currentDirectionRendererMain) {
                currentDirectionRendererMain.setMap(null); // Remove the current direction renderer
            }

            var origin = autocompleteOrigin.getPlace();
            var destination = autocompleteDestination.getPlace();

            if (origin && destination) {
                var directionsService = new google.maps.DirectionsService();
                var directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map,
                    polylineOptions: {  
                        strokeColor: "blue",
                        strokeWeight: 6
                    }
                });

                directionsService.route({
                origin: origin.geometry.location,
                destination: destination.geometry.location,
                travelMode: 'DRIVING'
                }, function(result, status) {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                        currentDirectionRendererMain = directionsRenderer;

                        var showRouteButton = document.getElementById('show-route');
                        showRouteButton.id = 'confirm-route';
                        showRouteButton.innerText = 'Confirm Route';
                        showRouteButton.classList.remove('bg-blue-500', 'hover:bg-blue-600', 'focus:ring-blue-500');
                        showRouteButton.classList.add('bg-green-500', 'hover:bg-green-600', 'focus:ring-green-500');
                        showRouteButton.onclick = confirmRoute;
                    } else {
                        window.alert('Directions request failed due to ' + status);
                    }
                });
            } else {
                alert("Please select both origin and destination.");
            }
        }

        function initMap(userLocation) {
            // Initialize the map with a default center
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: userLocation,
                disableDefaultUI: true,
                mapTypeControl: true,
                zoomControl: true,
                mapTypeControlOptions: {
                  style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                  mapTypeIds: ["roadmap", "terrain", "satellite"],
                },
            });

            // Add a marker for the user's current location
            new google.maps.Marker({
                position: userLocation,
                map: map,
                title: 'Your Location',
                icon: {
                    url: 'img/userLocation.svg',
                    scaledSize: new google.maps.Size(40, 40)
                }
            });

            inputOrigin = document.getElementById('origin');
            inputDestination = document.getElementById('destination');
            autocompleteOrigin = new google.maps.places.Autocomplete(inputOrigin);
            autocompleteDestination = new google.maps.places.Autocomplete(inputDestination);

            inputOrigin.addEventListener('input', function() {
                resetButton();
            });

            inputDestination.addEventListener('input', function() {
                resetButton();
            });

            // Add an event listener to capture the coordinates when the map is clicked
            map.addListener("click", (mapsMouseEvent) => {
                // Add a marker for the mouse click
                if (mapsMouseEvent.domEvent.ctrlKey) { // Check if the Ctrl key was pressed
                new google.maps.Marker({
                    position: mapsMouseEvent.latLng,
                    map: map,
                    title: 'Your Location'
                }); 
            }
            });
        }

        function handleLocationError(browserHasGeolocation, pos) {
            var infoWindow = new google.maps.InfoWindow({
                map: map
            });
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser doesn\'t support geolocation.');
        }

        function getUserLocation()
        {
            var userLocation;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    // Call a function that uses userLocation
                    initMap(userLocation);
                }, function() {
                    handleLocationError(true, map.getCenter());
                });
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, map.getCenter());
            }
        }
    </script>
</body>

</html>