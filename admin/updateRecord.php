<?php
   include '../config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $applicantId = $_POST['applicantId'];
        $className = $_POST['className'];
        $schoolName = $_POST['schoolName'];

        // Handle file uploads (if any)
        $idProofFileName = '';
        if (isset($_FILES['idProofFile']) && $_FILES['idProofFile']['error'] == 0) {
            $idProofFileName = basename($_FILES['idProofFile']['name']);
            $idProofUploadPath = 'uploads/' . $idProofFileName;
            move_uploaded_file($_FILES['idProofFile']['tmp_name'], $idProofUploadPath);
        }

        $imageFileName = '';
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] == 0) {
            $imageFileName = basename($_FILES['imageFile']['name']);
            $imageUploadPath = 'uploads/' . $imageFileName;
            move_uploaded_file($_FILES['imageFile']['tmp_name'], $imageUploadPath);
        }

        // Update the database with the new data
        $sql = "UPDATE applicant_master SET class = :class, school_name = :school_name";
        if ($idProofFileName) {
            $sql .= ", id_proof = :id_proof";
        }
        if ($imageFileName) {
            $sql .= ", upload_photo = :upload_photo";
        }
        $sql .= " WHERE application_id = :applicant_id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':class', $className);
        $stmt->bindParam(':school_name', $schoolName);
        if ($idProofFileName) {
            $stmt->bindParam(':id_proof', $idProofFileName);
        }
        if ($imageFileName) {
            $stmt->bindParam(':upload_photo', $imageFileName);
        }
        $stmt->bindParam(':applicant_id', $applicantId);

        // Execute and check if successful
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
?>
