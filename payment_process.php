<?php
    session_start();
    include 'config.php';

    file_put_contents('razorpay_log.txt', print_r($response, true), FILE_APPEND);

    $payment_status = "pending"; // Default payment status
    $added_on = date('Y-m-d H:i:s');

    // Step 1: Handle the initial payment request
    // if (isset($_POST['amount'], $_POST['email'], $_POST['appId'], $_POST['phone'])) {
    if (isset($_POST['amount']) && isset($_POST['email']) && isset($_POST['appId']) && isset($_POST['phone'])) {  
        // app_id is used inplace of user_id in payment table
        $appId = $_POST['appId'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $amount = $_POST['amount'];
        
        // Get the most recent User ID
        // $stmt = $conn->prepare("SELECT MAX(transaction_id) AS transaction_id FROM payment");
        // $stmt->execute();
        // $transaction_id = $stmt->fetch(PDO::FETCH_ASSOC);
        // // Save the transaction ID in session for later reference
        // $transaction_id = ($transaction_id === false || $transaction_id['transaction_id'] === null) ? 10000 : $transaction_id['transaction_id'] + 1;         

        $payment_id = 'pay_test';

        // Check if the payment entry already exists using PDO
        $stmt = $conn->prepare("SELECT amount, payment_status, payment_id, added_on, app_id, mobile_number, email FROM payment WHERE app_id = :appId");
        $stmt->bindParam(':appId', $appId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Insert a new payment entry if none exists for the appId and mobile
        if (!$result) {
            $stmt = $conn->prepare(
                "INSERT INTO payment (amount, payment_status, payment_id, added_on, app_id, mobile_number, email) 
                VALUES (:amount, :payment_status, :payment_id, :added_on, :appId, :phone, :email)"
            );
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_status', $payment_status);
            $stmt->bindParam(':payment_id', $payment_id);
            $stmt->bindParam(':added_on', $added_on);
            $stmt->bindParam(':appId', $appId);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
        }
    }

    // Step 2: Handle the payment confirmation after Razorpay returns the payment_id
    if (isset($_POST['payment_id']) && isset($_POST['appId'])) {
        $payment_id = $_POST['payment_id'];
        $appId = $_POST['appId'];

        // Update payment status and save the Razorpay payment ID using PDO
        $stmt = $conn->prepare("UPDATE payment SET payment_status = 'complete', payment_id = :payment_id WHERE app_id = :appId");
        $stmt->bindParam(':payment_id', $payment_id);
        $stmt->bindParam(':appId', $appId);
        $stmt->execute();
    }
?>
