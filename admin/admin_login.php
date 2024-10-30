<?php
    session_start();

    // Check if user is already logged in, redirect to dashboard
    if (isset($_SESSION['admin_logged_in'])) {
        header("Location: admin_dashboard.php");
        exit();
    }

    // Encryption Function
    // function encrypt_password($password, $encryption_key) {
    //     $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    //     $iv = openssl_random_pseudo_bytes($iv_length);
    //     $encrypted = openssl_encrypt($password, 'aes-256-cbc', $encryption_key, 0, $iv);
    //     return base64_encode($encrypted . '::' . $iv); // Return encrypted data with IV
    // }

    // Secure encryption key (use environment variable for security)
    // $encryption_key = getenv('ENCRYPTION_KEY') ?: 'mysecretkey1234567890'; // In production, use a secure method to store keys

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Basic validation
        if (empty($email) || empty($mobile) || empty($password) || empty($confirm_password)) {
            $error = "All fields are required.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Generate plain password and encrypt it
            // $encryptedPassword = encrypt_password($password, $encryption_key);

            $_SESSION['admin_logged_in'] = true; // Simulate login
            // $_SESSION['user_id'] = 'WQP383332';
            header("Location: admin_dashboard.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    </head>

    <body class="bg-light">
        <div class="container mt-5" style="width: 500px;">
            <h2 class="text-center">Admin Login</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="" class="mt-4">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Your Email" required>
                    <div id="emailMessage"></div>
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="tel" name="mobile" id="phone" class="form-control" maxlength="10" placeholder="Mobile Number" required>
                    <div id="phoneMessage" class="error-message"></div>
                </div>

                <div class="row gy-1 mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Strong Password" required>
                            <div id="passwordMessage" class="error-message"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                            <div id="confirmPasswordMessage" class="error-message"></div>
                        </div>
                    </div>
                </div>
              
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                // Email validation
                $('#email').on('input', function () {
                    const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($(this).val());
                    $(this).toggleClass('green', isValid).toggleClass('red', !isValid);
                    $('#emailMessage').text(isValid ? '' : 'Enter a valid email!');
                });

                // Phone validation (10 digits, starts with 6-9, and no repeated digits)
                $('#phone').on('keypress input', function (event) {
                    const phone = $(this).val();
                    const isValid = /^[6-9]\d{9}$/.test(phone) && !/^(.)\1{9}$/.test(phone);

                    if (event.type === 'keypress' && (!/^\d$/.test(event.key) || phone.length >= 10)) {
                        event.preventDefault();
                    }

                    $(this).toggleClass('green', isValid).toggleClass('red', !isValid);
                    $('#phoneMessage').text(isValid ? '' : phone.length < 10 ? 'Enter Valid Mobile Number!' : 'Invalid mobile number!');
                });

                // Password validation
                $('#password').on('input', function () {
                    const password = $(this).val();

                    const hasUpperCase = /[A-Z]/.test(password);
                    const hasLowerCase = /[a-z]/.test(password);
                    const hasNumber = /\d/.test(password);
                    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
                    const isValidLength = password.length >= 8;

                    const isValid = hasUpperCase && hasLowerCase && hasNumber && hasSpecialChar && isValidLength;

                    $('#passwordMessage').html(`
                        <ul>
                            <li class="${hasUpperCase ? 'valid' : 'invalid'}">At least one uppercase letter</li>
                            <li class="${hasLowerCase ? 'valid' : 'invalid'}">At least one lowercase letter</li>
                            <li class="${hasNumber ? 'valid' : 'invalid'}">At least one number</li>
                            <li class="${hasSpecialChar ? 'valid' : 'invalid'}">At least one special character</li>
                            <li class="${isValidLength ? 'valid' : 'invalid'}">Minimum 8 characters long</li>
                        </ul>
                    `);

                    $(this).toggleClass('green', isValid).toggleClass('red', !isValid);
                });

                // Confirm password validation
                $('#confirm_password').on('input', function () {
                    const password = $('#password').val();
                    const confirmPassword = $(this).val();

                    const isMatch = password === confirmPassword;

                    $('#confirmPasswordMessage').text(isMatch ? '' : 'Passwords do not match!');
                    $(this).toggleClass('green', isMatch).toggleClass('red', !isMatch);
                });
            });            
        </script>
            
    </body>
</html>
