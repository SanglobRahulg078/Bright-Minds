<?php
    session_start();
    include 'config.php';

    // Encryption/Decryption functions (same as in registration)
    function decrypt_password($encrypted_password, $encryption_key) {
        list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_password), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    // Secure encryption key (same as used during registration)
    $encryption_key = getenv('ENCRYPTION_KEY') ?: 'mysecretkey1234567890'; // Ideally stored securely in an environment variable

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Sanitize and validate inputs
            $uniqueId = filter_input(INPUT_POST, 'uniqueId', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';
            $loginType = filter_input(INPUT_POST, 'loginType', FILTER_SANITIZE_STRING);

            if (!empty($uniqueId) && !empty($password)) {
                // if ($loginType === 'parent_login') {
                //     $stmt = $conn->prepare("SELECT name, id, email, password FROM parent_user_master WHERE email = :email LIMIT 1");
                //     $stmt->bindParam(':email', $uniqueId);
                //     // echo '<div class="alert alert-danger">Incorrect Parent Email Id</div>';
                //     // exit();
                // } else if ($loginType === 'application_login') {
                //     $stmt = $conn->prepare("SELECT a.applicant_name, a.aadhar_uid, a.category_id, a.applicant_password, p.user_id FROM applicant_master a JOIN parent_user_master p ON a.user_id = p.user_id WHERE aadhar_uid = :aadhar_uid LIMIT 1");
                //     // WHERE 
                //     //     " . ($loginType === 'parent_login' ? 'pum.email = :emailOrAadhar' : 'am.aadhar_uid = :emailOrAadhar');
            
                //     $stmt->bindParam(':aadhar_uid', $uniqueId);
                // }

                
                // $sql = ($loginType === 'parent_login') 
                // ? "SELECT name, user_id, email, password FROM parent_user_master WHERE email = :uniqueId LIMIT 1"
                // : "SELECT applicant_name, applicant_password, category_id, user_id FROM applicant_master WHERE aadhar_uid = :uniqueId LIMIT 1";
                
                $sql = "
                    SELECT 
                        a.applicant_name, a.aadhar_uid, a.category_id, a.applicant_password, p.user_id, p.password, p.email, p.name 
                    FROM 
                        applicant_master a 
                    JOIN 
                        parent_user_master p ON a.user_id = p.user_id 
                    WHERE 
                        " . ($loginType === 'parent_login' ? 'p.user_id = :uniqueId' : 'a.application_id = :uniqueId');

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':uniqueId', $uniqueId);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $userNameField = $loginType === 'parent_login' ? 'name' : 'applicant_name';
                $passwordField = $loginType === 'parent_login' ? 'password' : 'applicant_password';
                
                if ($user) {
                    // Decrypt the stored password
                    $decrypted_password = decrypt_password($user[$passwordField], $encryption_key);

                    // Compare the decrypted password with the entered password
                    if ($decrypted_password === $password) {
                        $_SESSION['name'] = $user[$userNameField];
                        $_SESSION['user_id'] = $user['user_id'] ?? null;
                        $_SESSION['category_id'] = $loginType == 'application_login' ? $user['category_id'] : null;
                        $_SESSION['loginType'] = $loginType;
                        $_SESSION['uniqueId'] = $uniqueId;

                        echo "success";
                    } else {
                        echo '<div class="alert alert-danger">Incorrect Credentials!</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">User ID Not Found!</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Unique ID cannot be empty!</div>';
            }
        } catch (PDOException $e) {
        echo '<div class="alert alert-danger">A server error occurred. Please try again later.</div>';
    }
}
?>
