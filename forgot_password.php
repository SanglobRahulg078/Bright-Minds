<?php
    session_start();
    include 'config.php';
    require 'sendEmail.php';

    // Secure encryption key (same as used during registration)
    $encryption_key = getenv('ENCRYPTION_KEY') ?: 'mysecretkey1234567890';

    // Encryption/Decryption functions (same as in registration)
    function decrypt_password($encrypted_password, $encryption_key) {
        list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_password), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $uniqueId = filter_input(INPUT_POST, 'uniqueId', FILTER_SANITIZE_STRING);

            if (!empty($uniqueId)) {    
                $sql = "SELECT name, email, password, user_id FROM parent_user_master WHERE user_id = :uniqueId";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':uniqueId', $uniqueId);
                $stmt->execute();
                $parentData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($parentData) {
                    $name = $parentData['name'];
                    $email = $parentData['email'];
                    // Decrypt the stored password
                    $decrypted_password = decrypt_password($parentData['password'], $encryption_key);
                    
                    // check the payment or not                    
                    $stmt = $conn->prepare("SELECT payment_status FROM payment WHERE app_id = :uniqueId");
                    $stmt->bindParam(':uniqueId', $parentData['user_id']);
                    $stmt->execute();
                    $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);                    
            
                    // Check if the payment is complete
                    if ($paymentData['payment_status'] === "complete") { 
                        $sql = "SELECT application_id, applicant_name, applicant_password FROM applicant_master WHERE user_id = :uniqueId";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':uniqueId', $uniqueId);
                        $stmt->execute();
                        $applicantData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($applicantData as $applicant) {
                            $app_id = $applicant['application_id'];
                            $app_name = $applicant['applicant_name'];
                            $applicant_password = decrypt_password($applicant['applicant_password'], $encryption_key);

                            // for email sending                    
                            $applicantDetails[] = [
                                'app_name' => $app_name,
                                'app_id' => $app_id,
                                'app_password' => $applicant_password
                            ];
                        }

                        $subject = 'Password Request Successful - BrightMinds';
                        $body = "
                            <h3>Hello, {$name}</h3>
                            <p><strong>Parent User ID:</strong> " . htmlspecialchars($parentData['user_id']) . "</p>
                            <p><strong>Password:</strong> " . htmlspecialchars($decrypted_password) . "</p>
                            <p>Your payment is complete. Here are your related applicant details:</p>                                
                            <table border='1'>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Applicant ID / Login ID</th>
                                    <th>Applicant Password</th>
                                    </tr>";

                            foreach ($applicantDetails as $applicant) {
                                $body .= "
                                    <tr>
                                        <td>" . htmlspecialchars($applicant['app_name']) . "</td>
                                        <td style='text-align:center;'>" . htmlspecialchars($applicant['app_id']) . "</td>
                                        <td style='text-align:center;'>" . htmlspecialchars($applicant['app_password']) . "</td>
                                    </tr>";
                            }

                        $body .= "
                            </table>
                            <p>If you didn't request this, please contact support.</p>";
                        
                        // Now you can use $subject and $body to send the email

                    } else {
                        $subject = 'Payment Pending - Action Required';
                        $body = "
                            <h3>Hello, {$name}</h3>
                            <p>Your payment has not been completed. Please complete your payment to access applicant details.</p>
                            <p><strong>Parent User ID:</strong> " . htmlspecialchars($parentData['user_id']) . "</p>
                            <p><strong>Password:</strong> " . htmlspecialchars($decrypted_password) . "</p>
                            
                            <p>If you need assistance, please contact support.</p>";
                    }

                    // Send email
                    if (sendEmail($email, $subject, $body, $name)) {
                        echo "success";  // AJAX success response
                    } else {
                        echo "Failed to send the email. Please try again later.";
                    }
                    
                    exit();
                } else {
                    echo '<div class="alert alert-danger">No Data Available.</div>';
                } 
            } else {
                echo '<div class="alert alert-danger">Please Enter Correct ID</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">A server error occurred. Please try again later.</div>';
        }
    }
?>
