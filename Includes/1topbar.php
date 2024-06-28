<?php 
  $fullName = $_SESSION['firstName']." ".$_SESSION['lastName'];

?>
<nav class="navbar navbar-expand navbar-light bg-gradient-light topbar mb-4 static-top">
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars" style="color: black;"></i>
    </button>
    <div class="text-white big" style="margin-left:100px;"><b></b></div>
    <ul class="navbar-nav ml-auto">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <!-- Notification Icon -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw" style="color: black;"></i>


                <span class="badge badge-danger badge-counter" id="notificationBadge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="notificationDropdown">
                <div id="notificationContent"></div>
            </div>
        </li>
        <!-- ------------------notifications ends------------------------------- -->

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="../img/user-<?php echo $_SESSION['userId']; ?>.png"
                    style="max-width: 60px">
                <span class="ml-2 d-none d-lg-inline" style="color: black;"><b>
                        <?php echo $fullName;?>
                    </b></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-power-off fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>



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

    // console.log(prev_notificationbadge);

    function load_unseen_notification(view = '') {
        $.ajax({
            url: "./Includes/check_notifications.php",
            method: "POST",
            data: { view: view },
            dataType: "json",
            success: function (data) {
                $('#notificationContent').html(data.notification);
                if (data.unseen_notification == 0)
                {
                    $('#notificationBadge').html('');
                    prev_notificationbadge = data.unseen_notification;
                    localStorage.setItem('prev_notificationbadge', prev_notificationbadge);
                }
                else if (data.unseen_notification > 0) {
                    $('#notificationBadge').html(data.unseen_notification);

                    if(data.unseen_notification > prev_notificationbadge){
                        prev_notificationbadge = data.unseen_notification;
                        // Update values in localStorage
                        localStorage.setItem('prev_notificationbadge', prev_notificationbadge);
                        var notification = new Notification("Alert ⚠", {
                            body: data.notification_content,
                            icon: "./img/alert.png",
                        });
                    }
                }
            }
        });
    }

    load_unseen_notification();

    $(document).on('click', '#notificationDropdown', function () {
        $('#notificationBadge').html('');
        prev_notificationbadge = 0; // Reset the badge count
        // Update value in localStorage
        localStorage.setItem('prev_notificationbadge', prev_notificationbadge);
        load_unseen_notification('yes');
    });

    setInterval(function () {
        load_unseen_notification();
    }, 5000);
});


</script>