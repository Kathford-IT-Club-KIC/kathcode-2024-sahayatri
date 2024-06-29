<?php
session_start();

$errors = array();
$first_name = "";
$middle_name = "";
$last_name = "";
$dob = "";
$email = "";
$address = "";
$are_you_driver = "";
$blood_group = "";
$nagarita_number = "";
$username = "";
$password = "";
$confirm_password = "";
$location = "";

$db = mysqli_connect('localhost', 'root', '', 'hackthon');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to display errors
function display_errors($errors) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
}

// Function to generate a random token
function generate_token() {
    return bin2hex(random_bytes(32)); // Generate a 256-bit (32-byte) random token
}

// Function to handle file uploads securely
function handle_file_upload($db, $username, $file_input_name, $target_directory) {
    // Ensure the target directory exists or create it
    if (!file_exists($target_directory)) {
        if (!mkdir($target_directory, 0777, true)) {
            return "Failed to create directory: $target_directory";
        }
    }

    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]["error"] == 0) {
        $image = $_FILES[$file_input_name]['tmp_name'];
        $imgContent = file_get_contents($image);
    
        // Check if the image content was successfully read
        if ($imgContent === false) {
            echo "Failed to read the image file.";
        } else {
            // Insert image data into database as BLOB
            $sql = "INSERT INTO users (image) VALUES (?)";
            $stmt = mysqli_prepare($db, $sql);
    
            // Bind parameters
            mysqli_stmt_bind_param($stmt, 's', $imgContent);
    
            // Execute the statement
            $result = mysqli_stmt_execute($stmt);
    
            // Check execution result
            if ($result) {
                echo "Image uploaded successfully.";
            } else {
                echo "Image upload failed, please try again.";
                // Optionally, log the MySQL error for debugging
                // echo "Error: " . mysqli_stmt_error($stmt);
            }
        }
    } else {
        echo "Please select an image file to upload.";
    }
    
}

if (isset($_POST['reg_user'])) {
    // Sanitize and escape inputs
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $location = mysqli_real_escape_string($db, $_POST['address']);
    $first_name = mysqli_real_escape_string($db, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($db, $_POST['middle_name']);
    $are_you_driver = mysqli_real_escape_string($db, $_POST['are_you_driver']);
    $blood_group = mysqli_real_escape_string($db, $_POST['blood_group']);
    $nagarita_number = mysqli_real_escape_string($db, $_POST['nagarita_number']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
    $licenseNo = mysqli_real_escape_string($db, $_POST['number_plate']);
    $dob = $_POST['dob'];

    // Form validation
    if (empty($username)) array_push($errors, "Username is required");
    if (empty($email)) array_push($errors, "Email is required");
    if (empty($location)) array_push($errors, "Location is required");
    if (empty($password_1)) array_push($errors, "Password is required");
    if ($password_1 != $password_2) array_push($errors, "The two passwords do not match");

    // Check if user exists
    $user_check_query = "SELECT * FROM users WHERE username=? OR email=? LIMIT 1";
    $stmt = mysqli_prepare($db, $user_check_query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['username'] === $username) array_push($errors, "Username already exists");
        if ($user['email'] === $email) array_push($errors, "Email already exists");
    }

    // Register user if no errors
    if (count($errors) == 0) {
        $password = password_hash($password_1, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (firstName, lastName, email, username, password, address, bloodGroup, DOB, citizenshipNo, licenseNo, isRider) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "sssssssssss", $first_name, $last_name, $email, $username, $password, $location, $blood_group, $dob, $nagarita_number, $licenseNo, $are_you_driver);
        handle_file_upload($db, $username, 'citizenship_photo', 'images/profiles/');
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "You are now logged in";
            header('location: index.php');
            exit();
        } else {
            array_push($errors, "Failed to register user: " . mysqli_error($db));
        }
        mysqli_stmt_close($stmt);
    }
}

if (isset($_POST['login_user'])) {
    // Clean and escape inputs
    $username_or_email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $remember_me = isset($_POST['remember_me']);

    // Check if username or email is empty
    if (empty($username_or_email)) {
        array_push($errors, "Email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $query = "SELECT * FROM users WHERE username=? OR email=? LIMIT 1";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username_or_email, $username_or_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            // Generate and store a token if "Remember me" is checked
            if ($remember_me) {
                $token = generate_token();
                $token_expiry = date('Y-m-d H:i:s', strtotime('+30 days')); // Token expires in 30 days

                // Update user's token and token_expiry in the database
                $update_token_query = "UPDATE users SET token=?, tokenExpiry=? WHERE username=?";
                $stmt = mysqli_prepare($db, $update_token_query);
                mysqli_stmt_bind_param($stmt, "sss", $token, $token_expiry, $user['username']);
                mysqli_stmt_execute($stmt);

                // Set token as a cookie with expiry time
                setcookie('token', $token, strtotime($token_expiry), '/');
            }

            // Start session and set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['success'] = "You are now logged in";

            // Redirect to index.php or desired page
            header('location: index.php');
            exit();
        } else {
            array_push($errors, "Wrong email or password combination");
        }
    }
    // Display errors if any
    display_errors($errors);
}

// Check if token cookie is set and log in the user automatically
if (isset($_COOKIE['token'])) {
    $token = $_COOKIE['token'];

    // Query to fetch user based on token validity
    $query = "SELECT * FROM users WHERE token=? AND token_expiry > NOW() LIMIT 1";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['success'] = "You are now logged in";
        
        // Redirect to index.php or desired page
        header('location: index.php');
        exit();
    } else {
        // Invalid token, clear cookies
        setcookie('token', '', time() - (86400 * 30), '/');
    }
}

if (isset($_POST['logout'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Clear token in database and in cookies
    $update_token_query = "UPDATE users SET token=NULL, token_expiry=NULL WHERE username=?";
    $stmt = mysqli_prepare($db, $update_token_query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
    mysqli_stmt_execute($stmt);

    // Clear token cookie
    setcookie('token', '', time() - (86400 * 30), '/');

    // Redirect to login.php after logout
    header('location: login.php');
    exit();
}

?>
