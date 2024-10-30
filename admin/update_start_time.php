<?php
    include '../config.php';

    // Get the JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = $data['application_id'];

    // Prepare your update query using PDO
    $query = "UPDATE applicant_master SET essay_start_time = NOW(), exam_status = 'In Progress' WHERE application_id = :application_id";
    $stmt = $conn->prepare($query);

    // Bind the parameter
    $stmt->bindParam(':application_id', $application_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update start time.']);
    }

    // Close the statement and connection
    $stmt = null;
    $conn = null;
?>