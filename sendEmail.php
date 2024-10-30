<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
	require 'vendor/autoload.php';

	// Function to send email
	function sendEmail($recipientEmail, $subject, $body, $recipientName = 'User') {
        $mail = new PHPMailer(true);

		try {
			$mail->isSMTP();
			$mail->CharSet = 'UTF-8';
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			
			// check / see errors
			// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
			
			// $mail->Host = 'smtp.hostinger.com';
			// $mail->Port = 587;
			// $mail->Username = 'info@iuppindia.org';
			// $mail->Password = 'Rahul@321';
			// $mail->setFrom('info@iuppindia.org', 'Sanglob Business Services'); // Use the same domain as the SMTP server
			// $mail->addAddress($email, 'Sanglob.in');

			$mail->Host = 'smtp.gmail.com';
			$mail->Username = "Rahulg6235035@gmail.com"; 
			$mail->Password = "mxnuzppnkkrurtkr";
			$mail->Port = 587;

			// Recipients
			$mail->setFrom('Rahulg6235035@gmail.com', 'Award Website');
			$mail->addAddress($recipientEmail, $recipientName);

			$mail->isHTML(true);
			$mail->Subject = $subject;       // Set the email subject
			$mail->Body = $body;             // Set the email body in HTML format
			$mail->AltBody = strip_tags($body); // Alternative plain text body for non-HTML email clients

			// Additional headers for security
			$mail->ContentType = 'text/html';
			$mail->addCustomHeader('MIME-Version', '1.0');
			$mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());
			$mail->addCustomHeader('X-Priority', '1'); // Set highest email priority

			$mail->send();
			return true;
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error:";
			return false;
		}
	}
?>
