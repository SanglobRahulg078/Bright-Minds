<?php
    include '../config.php';
    header('Content-Type: application/json');

    try {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (isset($data['topic']) && isset($data['content']) && isset($data['application_id'])) {            
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
                echo json_encode(['success' => true]);
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