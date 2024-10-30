<?php
	session_start();
	require 'config.php';
	require 'sendEmail.php';

	// variable	       
	$fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
	$mname = filter_input(INPUT_POST, 'mname', FILTER_SANITIZE_STRING);
	$lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
	$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

	// Check if email already exists
	$stmt = $conn->prepare("SELECT COUNT(*) FROM parent_user_master WHERE email = :email");
	$stmt->bindParam(':email', $email);
	$stmt->execute();

	if ($stmt->fetchColumn() > 0) {
		echo 'Email already registered!';
		exit;
	}

	if(isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) > 8) {
            
		// Build full name
		$fullname = trim("$fname $mname $lname");
		
		// Get the most recent ID	
		$stmt = $conn->prepare("SELECT MAX(id) AS id FROM parent_user_master");
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		// Generate User ID for Parent / Guardian
		$base_id = ($user === false || $user['id'] === null) ? 1 : $user['id'] + 1;
		$remaining_digits = 6 - strlen($base_id); // Calculate the number of digits needed to make the total 9

		if ($remaining_digits > 0) {
			$random_number = str_pad(rand(0, pow(10, $remaining_digits) - 1), $remaining_digits, '0', STR_PAD_LEFT);
			$user_id = 'WQP' . $base_id . $random_number;
		} else {
			// If the base ID is already 6 digits or more, use only the base ID
			$user_id = 'WQP' . substr($base_id, 0, 6); // Truncate to 6 digits if needed
		}

		// Get the OTP
		$otp = rand(100000, 999999);
		$_SESSION['otp'] = $otp;
		$_SESSION['user_id'] = $user_id;
		
		// Save data to the table
		$stmt = $conn->prepare("INSERT INTO parent_user_master (name, phone, email, created_at, user_id) VALUES (:fullname, :phone, :email, NOW(), :user_id)");
		$stmt->execute([':fullname' => $fullname, ':phone' => $phone, ':email' => $email, ':user_id' => $user_id]);

		// Send OTP via email using PHPMailer		
		$subject = 'BrightMinds | OTP Code';
		$body = "OTP is $otp for verifying your details. This can be used once. Thank You";

		if (sendEmail($email, $subject, $body)) {
			// echo 'OTP: ' . htmlspecialchars($otp);
			echo 'OTP Sent to your Email';
		} else {
			echo "Failed to send OTP.";
		}
	} else {
		echo "Please Enter Correct Input";
	}
?>
