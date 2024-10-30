<?php
	require 'config.php';

    if (isset($_POST['aadhar_uid'])) {
        $aadhar_uid = $_POST['aadhar_uid'];

        // Check if the Aadhaar UID already exists in the database
        $stmt = $conn->prepare("SELECT COUNT(*) FROM applicant_master WHERE aadhar_uid = :aadhar_uid");
        $stmt->execute([':aadhar_uid' => $aadhar_uid]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "exists";
        } else {
            echo "available";
        }
    }
?>
