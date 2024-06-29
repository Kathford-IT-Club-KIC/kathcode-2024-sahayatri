<?php
include 'Includes/dbcon.php';

if(isset($_GET['requestId'])) {
    $requestId = $_GET['requestId'];

    // Prepare SQL query to fetch price and users.id
    $sql = "SELECT poolmappings.price, users.id AS userId
            FROM poolmappings
            INNER JOIN poolrequests ON poolmappings.poolRequestId = poolrequests.id
            INNER JOIN users ON poolrequests.userId = users.id
            WHERE poolmappings.poolRequestId = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $requestId); // Bind the requestId parameter
        $stmt->execute();
        $stmt->bind_result($price, $userId); // Bind variables to store result
        $stmt->fetch(); // Fetch the result
        
        if ($price !== null && $userId !== null) {
            $price = ceil($price);
            // $price = 10;
        } else {
            echo "No results found.";
        }

        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }
} else {
    echo "No request ID provided.";
}
?>

<html>
<head>
    <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
    <title>Khalti Payment</title>
    <link rel="icon" href="img/logo/favicon.png">

</head>
<body>
    <script>
        var config = {
            // replace the publicKey with yours
            "publicKey": "test_public_key_4db552c2f8ce46fbabcc377befd8081a",
            "productIdentity": "<?php echo $userId; ?>",
            "productName": "<?php echo $requestId; ?>",
            "productUrl": "http://sahayatri.khwopa.edu.np/getRequestDetails.php?requestId=8&status=booked",
            "paymentPreference": [
                "KHALTI"
                ],
            "eventHandler": {
                onSuccess (payload) {
                    // hit merchant api for initiating verfication
                    // console.log(payload);
                    window.location.href = `verifyKhalti.php?token=${payload.token}&amount=<?php echo ($price * 100); ?>&requestId=<?php echo $requestId; ?>`;
                },
                onError (error) {
                    console.log(error);
                },
                onClose () {
                    console.log('widget is closing');
                }
            }
        };

        var checkout = new KhaltiCheckout(config);
        checkout.show({amount: <?php echo ($price * 100); ?>});
    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $amount = $_POST['amount'];

    $url = "https://khalti.com/api/v2/payment/verify/";
    $data = [
        'token' => $token,
        'amount' => $amount
    ];

    $args = http_build_query($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Replace with your Khalti secret key
    $headers = ["Authorization: Key test_public_key_4db552c2f8ce46fbabcc377befd8081a"];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_code == 200) {
        $response_data = json_decode($response, true);
        if (isset($response_data['state']) && $response_data['state']['name'] === 'Completed') {
            echo "Payment verified successfully!";
        } else {
            echo "Payment verification failed!";
        }
    } else {
        echo "Error verifying payment: " . $response;
    }
}
?>
