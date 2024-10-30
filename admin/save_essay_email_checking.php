<?php
    include '../config.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../vendor/autoload.php'; // Adjust path to load PHPMailer from vendor in the root directory

    header('Content-Type: application/json');

    try {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (isset($data['topic']) && isset($data['content']) && isset($data['application_id'])) {
            $time = date('Y-m-d H:i:s');
            
            // Prepare the SQL statement
            $stmt = $conn->prepare("
                UPDATE applicant_master 
                SET essay_topic = :topic, 
                    essay_description = :content, 
                    word_count = :word_count, 
                    sentence_count = :sentence_count, 
                    essay_end_time = NOW(), 
                    essay_time = :time,
                    exam_status = 'Closed'
                WHERE application_id = :application_id");

            // Bind parameters
            $stmt->bindParam(':topic', $data['topic']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':word_count', $data['word_count']);
            $stmt->bindParam(':sentence_count', $data['sentence_count']);
            $stmt->bindParam(':time', $data['time_taken']);
            $stmt->bindParam(':application_id', $data['application_id']);
                         
            // Execute the statement
            if ($stmt->execute()) {
                // Fetch parent details and applicant name
                $parentQuery = $conn->prepare("

                
                    SELECT 
                        a.applicant_name, a.aadhar_uid, a.class, p.user_id, p.email, p.name 
                    FROM 
                        applicant_master a 
                    JOIN 
                        parent_user_master p ON a.user_id = p.user_id 
                    WHERE 

                    SELECT email, applicant_name 
                    FROM applicant_master 
                    WHERE application_id = :application_id");
                $parentQuery->bindParam(':application_id', $data['application_id']);
                $parentQuery->execute();
                $parentData = $parentQuery->fetch(PDO::FETCH_ASSOC);

                if ($parentData) {
                    $parentEmail = $parentData['parent_email'];
                    $applicantName = $parentData['applicant_name'];
                    $timeTaken = $data['time_taken'];
                    
                    // Send email to the parent
                    $mail = new PHPMailer(true);

                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.example.com'; // Specify your SMTP server
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'your-email@example.com'; // SMTP username
                        $mail->Password   = 'your-email-password';   // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Recipients
                        $mail->setFrom('no-reply@example.com', 'Essay Submission System');
                        $mail->addAddress($parentEmail, 'Parent');

                        // Email content
                        $mail->isHTML(true);
                        $mail->Subject = 'Applicant Essay Submission Confirmation';
                        $mail->Body    = "
                            <p>Dear Parent,</p>
                            <p>Your applicant <strong>{$applicantName}</strong> (ID: {$data['application_id']}) has successfully submitted their essay.</p>
                            <p>Details:</p>
                            <ul>
                                <li>Topic: {$data['topic']}</li>
                                <li>Time Taken: {$timeTaken}</li>
                                <li>Word Count: {$data['word_count']}</li>
                                <li>Sentence Count: {$data['sentence_count']}</li>
                            </ul>
                            <p>Thank you.</p>";

                        // Send the email
                        $mail->send();

                        echo json_encode(['success' => true]);
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Parent details not found.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error while executing query.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    }

    // Close the connection
    // $conn = null;
?>