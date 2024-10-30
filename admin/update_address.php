<?php
   include '../config.php';

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize form inputs
        $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_STRING);
        $address1 = filter_var($_POST['address1'], FILTER_SANITIZE_STRING);
        $address2 = filter_var($_POST['address2'], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
        $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
        $pincode = filter_var($_POST['pincode'], FILTER_SANITIZE_NUMBER_INT);
        $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
        
        // Validate required fields
        if (empty($address1) || empty($city) || empty($state) || empty($pincode) || empty($country)) {
            echo 'error'; // Return error if validation fails
            exit();
        }

        // Ensure valid pincode (modify this regex based on country requirements)
        if (!preg_match('/^\d{5,6}$/', $pincode)) {
            echo 'invalid_pincode';
            exit();
        }
              
        try {
            $stmt = $conn->prepare("
                UPDATE parent_user_master 
                SET 
                    address_line_1 = :address1, 
                    address_line_2 = :address2, 
                    pincode = :pincode, 
                    city = :city, 
                    state = :state, 
                    country = :country 
                WHERE user_id = :user_id");
   
            // Bind parameters
            $stmt->bindParam(':address1', $address1);
            $stmt->bindParam(':address2', $address2);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':pincode', $pincode);
            $stmt->bindParam(':country', $country);
            $stmt->bindParam(':user_id', $user_id);
    
            // Execute the query
            if ($stmt->execute()) {
                echo 'success';
            } else {
                echo 'error';
            }
   
        } catch (PDOException $e) {
            echo 'Address Not Updated';
        }
   }
?>
   