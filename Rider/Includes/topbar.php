<?php 
  $fullName = $_SESSION['firstName']." ".$_SESSION['lastName'];
?>

<nav class="bg-gray-800 text-white w-full p-4 flex items-center justify-between">
    <!-- <button id="sidebarToggleTop" class="text-white focus:outline-none">
        <i class="fa fa-bars"></i>
    </button> -->
    <!-- <div class="flex-1" id="logoText"><b>सहयात्री</b></div> -->
    <ul class="flex items-center justify-center w-full space-x-4">
        <!-- <select class="w-48 bg-white text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer" required name="vehicleType" id="vehicleType">
            <option value="car">Car</option>
            <option value="bike">Bike</option>
            <option value="cab">Cab</option>
        </select>


        <li>
            <input type="text" class="px-2 py-1 rounded-md bg-white text-gray-800 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Vacant Seats" required id='advertisedSeats'>
        </li> -->
    </ul>
    <ul class="flex items-center space-x-4">
        <!-- Notification Icon -->
        <li class="relative">
            <a class="relative block p-2" href="#" id="notificationDropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw text-white"></i>
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full" id="notificationBadge"></span>
            </a>
            <div class="hidden absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg" aria-labelledby="notificationDropdown" id="notificationDropdownMenu">
                <div id="notificationContent" class="p-2 text-gray-800"></div> <!-- Adjusted text color -->
            </div>
        </li>

        <!-- User Profile -->
        <li class="relative">
            <a href="#" id="userDropdown" role="button" class="flex items-center space-x-2">
                <img class="w-8 h-8 rounded-full" src="./img/user-<?php echo $_SESSION['userId']; ?>.png">
                <span><b><?php echo $fullName;?></b></span>
            </a>
            <div class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded shadow-lg" id="userMenu"> <!-- Adjusted text color -->
                <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">Logout</a>
            </div>
        </li>
    </ul>
</nav>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        // Retrieve the values from localStorage
        var prev_notificationbadge = localStorage.getItem('prev_notificationbadge');

        // Convert string to number for prev_notificationbadge
        if (prev_notificationbadge === null) {
            prev_notificationbadge = 0;
        } else {
            prev_notificationbadge = parseInt(prev_notificationbadge);
        }

        function load_unseen_notification(view = '') {
            $.ajax({
                url: "./Includes/check_notifications.php",
                method: "POST",
                data: { view: view },
                dataType: "json",
                success: function (data) {
                    $('#notificationContent').html(data.notification);
                    if (data.unseen_notification == 0) {
                        $('#notificationBadge').html('');
                        $('#notificationBadge').addClass('hidden'); // Hide the badge
                        prev_notificationbadge = data.unseen_notification;
                        localStorage.setItem('prev_notificationbadge', prev_notificationbadge);
                    } else if (data.unseen_notification > 0) {
                        $('#notificationBadge').removeClass('hidden'); // Show the badge
                        $('#notificationBadge').html(data.unseen_notification);

                        if (data.unseen_notification > prev_notificationbadge) {
                            prev_notificationbadge = data.unseen_notification;
                            localStorage.setItem('prev_notificationbadge', prev_notificationbadge);
                        }
                    }
                }
            });
        }

        load_unseen_notification();

        $(document).on('click', '#notificationDropdown', function (event) {
            event.preventDefault();
            $('#notificationBadge').html('');
            $('#notificationBadge').addClass('hidden'); // Hide the badge
            prev_notificationbadge = 0; // Reset the badge count
            localStorage.setItem('prev_notificationbadge', prev_notificationbadge);
            load_unseen_notification('yes');
            $('#notificationDropdownMenu').toggleClass('hidden');
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#notificationDropdown').length) {
                $('#notificationDropdownMenu').addClass('hidden');
            }
        });

        setInterval(function () {
            load_unseen_notification();
        }, 5000);

        // Toggle user menu
        $('#userDropdown').click(function(event) {
            event.preventDefault();
            $('#userMenu').toggleClass('hidden');
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#userDropdown').length) {
                $('#userMenu').addClass('hidden');
            }
        });
    });
</script>

<script>
    // Get a reference to the input element
    const advertisedSeatsInput = document.getElementById('advertisedSeats');

    // Add an input event listener to validate the input as the user types
    advertisedSeatsInput.addEventListener('input', function() {
        // Remove any non-digit characters
        this.value = this.value.replace(/\D/g, '');

        // Convert the input value to a number
        let inputValue = parseInt(this.value, 10);

        // Check if the value is a valid number and greater than 0
        if (isNaN(inputValue) || inputValue <= 0) {
            // If invalid, reset the value to an empty string or handle it as needed
            this.value = '';
        }
    });
</script>