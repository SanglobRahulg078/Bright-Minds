<?php
	session_start();
	require 'config.php';
	require 'sendEmail.php';

    // Encryption Function
    function encrypt_password($password, $encryption_key) {
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($password, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv); // Return encrypted data with IV
    }
    
    // Secure encryption key (use environment variable for security)
    $encryption_key = getenv('ENCRYPTION_KEY') ?: 'mysecretkey1234567890'; // In production, use a secure method to store keys

    // Generate random password
    function generatePassword($length = 10) {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789'), 0, $length);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // variable
            $fname = $_POST['fname'];
            $mname = $_POST['mname'];
            $lname = $_POST['lname'];

            // Build full name
            $name = trim("$fname $mname $lname");            
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $user_id = $_SESSION['user_id']; 

            $password = generatePassword();
            // $encryptedPassword = password_hash($password, PASSWORD_BCRYPT);
            $encryptedPassword = encrypt_password($password, $encryption_key);

            $category_sum = htmlspecialchars($_POST['category_sum']); // Total sum
            $payment_method = htmlspecialchars($_POST['payment'] ?? 'razorpay');
            $verify_otp = htmlspecialchars($_POST['otp']);

            // Check OTP validity
            if($verify_otp == $_SESSION['otp']) {
                
                $stmt = $conn->prepare("
                    UPDATE parent_user_master 
                    SET 
                        password = :password, 
                        amount = :amount, 
                        payment_method = :payment_method, 
                        verify_otp = :verify_otp, 
                        created_at = NOW()
                    WHERE user_id = :user_id");

                $stmt->execute([
                    ':password' => $encryptedPassword,
                    ':amount' => $category_sum,
                    ':payment_method' => $payment_method,
                    ':verify_otp' => $verify_otp,
                    ':user_id' => $user_id
                ]);

                // Process each applicant
                $applicants = $_POST['applicantName'];
                $categories = $_POST['category'];
                $uniqueIds = $_POST['uniqueId'];

                // Insert applicant data into applicant_master
                for ($i = 0; $i < count($uniqueIds); $i++) {
                    $app_name = htmlspecialchars($applicants[$i]);
                    $category_id = htmlspecialchars($categories[$i]);
                    $aadhar_uid = htmlspecialchars(strtoupper($uniqueIds[$i]));

                    // Pad the string with leading zeros if it's less than 6 characters long (Convert string or a number input)
                    $application_id = 'WQS' . str_pad(substr($aadhar_uid, -6), 6, '0', STR_PAD_LEFT);
                    
                    $applicantPassword = generatePassword();
                    $encryptedApplicantPassword = encrypt_password($applicantPassword, $encryption_key); // Encrypt applicant password
    
                    // Get category PRICE using prepared statement
                    $stmt = $conn->prepare("SELECT category_name, category_price FROM category_master WHERE category_id = :category_id");
                    $stmt->execute([':category_id' => $category_id]);
                    $category = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Insert applicant data into applicant_master
                    $stmt = $conn->prepare("INSERT INTO applicant_master 
                    (category_id, application_id, applicant_name, category_name, aadhar_uid, applicant_password, amount, user_id) 
                    VALUES 
                    (:category_id, :application_id, :app_name, :category_name, :aadhar_uid, :password, :amount, :user_id)");
                    $stmt->execute([
                        ':category_id' => $category_id,
                        ':application_id' => $application_id,
                        ':app_name' => $app_name,
                        ':category_name' => $category['category_name'],
                        ':aadhar_uid' => $aadhar_uid,
                        ':password' => $encryptedApplicantPassword,
                        ':amount' => $category['category_price'],
                        ':user_id' => $user_id
                    ]);

                    // for email sending                    
                    $applicantDetails[] = [
                        'app_name' => $app_name,
                        'application_id' => $application_id,
                        'password' => $applicantPassword
                    ];
                }
                
                // Send OTP via email using PHPMailer
                $subject = 'Registration Successful - BrightMinds';
                $body = "
                    <h3>Hello, {$name}</h3>
                    <p><strong>Parent User ID:</strong> " . htmlspecialchars($user_id) . "</p>
                    <p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>
                    <p>If you didn't request this, please contact support.</p>

                    <div>
                        <p>&copy; 2024 <a href='" . $siteUrl . "'>BrightMinds</a>. All rights reserved.</p>
                    </div>";
                
                if (sendEmail($email, $subject, $body)) {
                    echo 'success';
                } else {
                    echo 'Failed to send OTP.';
                }

                // To Generate random ID for payment
                $_SESSION['application_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['phone'] = $phone;
                $_SESSION['price'] = $category_sum;

                exit();
            } else {
                echo "Invalid OTP";
            }
        } catch (PDOException $e) {
            echo "Error: Duplicate entry for " . $email;
        }
    }
?>