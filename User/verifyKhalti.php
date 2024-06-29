<?php
include 'Includes/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve token and amount from POST data
    $token = $_GET['token'];
    $amount = $_GET['amount'];
    $requestId = $_GET['requestId'];

    // Build the POST data
    $postData = [
        'token' => $token,
        'amount' => $amount
    ];

    // Set up cURL to make the request to Khalti API
    $url = "https://khalti.com/api/v2/payment/verify/";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Replace 'test_secret_key_04691270759846a3bd13c3a31d030534' with your live secret key in production
    $headers = [
        'Authorization: Key test_secret_key_04691270759846a3bd13c3a31d030534',
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute cURL request
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle response
    if ($statusCode === 200) {
        // Payment verification successful, update database
        $sql = "UPDATE poolmappings SET completed = 'yes' WHERE poolRequestId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $requestId);
        $stmt->execute();
        $stmt->close();

        // Redirect to another page
        header("Location: createPoolRequest.php");
        exit;
    } else {
        // Payment verification failed
        echo "Error verifying payment: " . $response;
    }
} else {
    echo "Invalid request method.";
}
?>
