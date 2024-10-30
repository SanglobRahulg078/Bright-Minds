<?php
//    session_start();
    include '../config.php';

    if (isset($_GET['application_id'])) {
        $application_id = $_GET['application_id'];
        
        $stmt = $conn->prepare("SELECT application_id, applicant_name, category_name, aadhar_uid, school_name, essay_topic, essay_description, word_count, sentence_count, essay_start_time, essay_end_time, essay_time, exam_date, exam_status, result_date, result_status, qualified_rank, religional_rank, national_rank, international_rank, applicant_password FROM applicant_master WHERE application_id = :application_id");
        $stmt->execute([':application_id' => $application_id]);
        $applicant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($applicant) {
            echo json_encode(['success' => true, 'data' => $applicant]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Applicant not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID.']);
    }
?>