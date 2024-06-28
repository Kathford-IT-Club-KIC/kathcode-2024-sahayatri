<?php
include('Includes/dbcon.php');
include('Includes/session.php');

$db = $conn;

if (isset($_GET['userId'])) {
    $id = $_GET['userId'];
    $query = "SELECT firstName, lastName, email, address, phone, rating FROM users WHERE id=? LIMIT 1";
    $stmt = mysqli_prepare($db, $query);
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . mysqli_error($db));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) {
        $first_name = $user['firstName'];
        $last_name = $user['lastName'];
        $email = $user['email'];
        $address = $user['address'];
        $phone_no = $user['phone'];
        $rating = $user['rating'];
    } else {
        echo "User not found.";
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    $image_path = 'img/user-' . $id . '.png';
}

function generateStars($rating) {
    $starsHtml = '';
    for ($i = 0; $i < 5; $i++) {
        if ($i < $rating) {
            $starsHtml .= '<span class="text-yellow-500">★</span>';
        } else {
            $starsHtml .= '<span class="text-gray-400">★</span>';
        }
    }
    return $starsHtml;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - <?php echo $first_name . ' ' . $last_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen bg-gray-900 text-white">
    <!-- Include the sidebar -->
    <?php include './Includes/sidebar.php'; ?>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col">
        <!-- Include the top bar -->
        <?php include './Includes/topbar.php'; ?>

        <!-- User Detail Content -->
        <div class="flex flex-1 justify-center items-center p-6">
            <div class="max-w-md w-full bg-gray-800 rounded-lg shadow-lg p-8">
                <div class="flex flex-col items-center">
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars("$first_name $last_name's Profile Image", ENT_QUOTES); ?>" class="rounded-full w-32 h-32 mb-4">
                    <h1 class="text-2xl font-semibold mb-2"><?php echo $first_name . ' ' . $last_name; ?></h1>
                    <p class="text-gray-400 mb-4"><?php echo $email; ?></p>
                    <p class="text-gray-400 mb-4"><?php echo $address; ?></p>
                    <p class="text-gray-400 mb-4"><?php echo $phone_no; ?></p>
                    <div class="flex items-center">
                        <p class="text-gray-400 mr-2">Rating:</p>
                        <?php echo generateStars($rating); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
