<?php
    session_start();
    include 'config.php';
    require 'sendEmail.php';

    if(!isset($_GET['appId']) && !isset($_GET['payment_id'])) {
        header("location: index");
    }

    // Get the appId from the URL or fallback to a default value (for testing)
    $appId = isset($_GET['appId']) ? $_GET['appId'] : '';
    
    if (!empty($appId)) {
        try {
            $stmt = $conn->prepare("SELECT payment_status FROM payment WHERE app_id = :app_id");
            $stmt->bindParam(':app_id', $appId);
            $stmt->execute();
            $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Check if the payment is complete
            if ($paymentData) {
                $pay_status = $paymentData['payment_status'];
                
                if ($pay_status == "complete") {
                    $stmt = $conn->prepare("SELECT name, email, password, user_id FROM parent_user_master WHERE user_id = :appId");
                    $stmt->bindParam(':appId', $appId);
                    $stmt->execute();
                    $parentData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($parentData) {
                        // Secure encryption key (same as used during registration)
                        $encryption_key = getenv('ENCRYPTION_KEY') ?: 'mysecretkey1234567890';

                        // Encryption/Decryption functions (same as in registration)
                        function decrypt_password($encrypted_password, $encryption_key) {
                            list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_password), 2);
                            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
                        }

                        $name = $parentData['name'];
                        $email = $parentData['email'];
                        $decrypted_password = decrypt_password($parentData['password'], $encryption_key);

                        // $sql = "
                        //     SELECT 
                        //         a.applicant_name, a.aadhar_uid, a.category_id, a.applicant_password, p.user_id, p.password, p.email, p.name 
                        //     FROM 
                        //         applicant_master a 
                        //     JOIN 
                        //         parent_user_master p ON a.user_id = p.user_id 
                        //     WHERE
                        //         p.user_id = :uniqueId";
        
                        // $stmt = $conn->prepare($sql);
                        // $stmt->bindParam(':uniqueId', $uniqueId);
                        // $stmt->execute();
    
                        // $parentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        $sql = "SELECT application_id, applicant_name, applicant_password FROM applicant_master WHERE user_id = :appId";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':appId', $appId);
                        $stmt->execute();
                        $applicantData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($applicantData as $applicant) {
                            $app_name = $applicant['applicant_name'];
                            $app_id = $applicant['application_id'];
                            $applicant_password = decrypt_password($applicant['applicant_password'], $encryption_key);

                            // for email sending                    
                            $applicantDetails[] = [
                                'app_name' => $app_name,
                                'app_id' => $app_id,
                                'app_password' => $applicant_password
                            ];
                        }

                        // Email content for successful payment
                        $subject = 'Payment Successful - BrightMinds';
                        $body = "
                            <h3>Hello, {$name}</h3>
                            <p><strong>Parent User ID:</strong> " . htmlspecialchars($appId) . "</p>
                            <p><strong>Password:</strong> " . htmlspecialchars($decrypted_password) . "</p>                            
                            <p>Your payment is complete. Here are your related applicant details:</p>                              
                            <table border='1'>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Applicant ID / Login ID</th>
                                    <th>Applicant Password</th>
                                </tr>";

                            foreach ($applicantDetails as $applicant) {
                                $body .= "
                                    <tr>
                                        <td>" . htmlspecialchars($applicant['app_name']) . "</td>
                                        <td style='text-align:center;'>" . htmlspecialchars($applicant['app_id']) . "</td>
                                        <td style='text-align:center;'>" . htmlspecialchars($applicant['app_password']) . "</td>
                                    </tr>";
                            }

                        $body .= "
                            </table>
                                <p>If you didn't request this, please contact support.</p>
                                <br/><br/>
                                <div class='footer'>
                                    <p>&copy; 2024 <a href='" . $siteUrl . "'>BrightMinds</a>. All rights reserved.</p>
                                </div>";
                    }

                    // Send email
                    if (sendEmail($email, $subject, $body, $name)) {
                        // echo "success";  // AJAX success response
                    } else {
                        echo "Failed to send the email. Please try again later.";
                    }
                } else {
                    echo "Payment not complete.";
                    exit();
                }
            } else {
                echo "No payment record found.";
                exit();
            }
        } catch (PDOException $e) {
            echo "Error: Occured While Payment Success.";
            exit();
        }        
    } else {
        // Handle the case where appId is not present in the URL
        echo "Application ID not found in the URL.";
        exit();
    }
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations Page | BrightMinds</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, white, #f3f34b);
            /* background: linear-gradient(to top, #7DF9FF, #4169E1); */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            box-sizing: border-box;
        }

        @keyframes confetti-slow {
            0% {
                transform: translate3d(0, 0, 0) rotateX(0) rotateY(0);
            }

            100% {
                transform: translate3d(25px, 105vh, 0) rotateX(360deg) rotateY(180deg);
            }
        }

        @keyframes confetti-medium {
            0% {
                transform: translate3d(0, 0, 0) rotateX(0) rotateY(0);
            }

            100% {
                transform: translate3d(100px, 105vh, 0) rotateX(100deg) rotateY(360deg);
            }
        }

        @keyframes confetti-fast {
            0% {
                transform: translate3d(0, 0, 0) rotateX(0) rotateY(0);
            }

            100% {
                transform: translate3d(-50px, 105vh, 0) rotateX(10deg) rotateY(250deg);
            }
        }

        .container {
            width: 100vw;
            height: 100vh;
            background: linear-gradient(to right, white, #f3f34b);
            border: 1px solid white;
            position: fixed;
            top: 0;
            left: 0;
        }

        .confetti-container {
            perspective: 700px;
            position: absolute;
            overflow: hidden;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .confetti {
            position: absolute;
            z-index: 1;
            top: -10px;
            border-radius: 0%;
        }

        .confetti--animation-slow {
            animation: confetti-slow 2.25s linear 1 forwards;
        }

        .confetti--animation-medium {
            animation: confetti-medium 1.75s linear 1 forwards;
        }

        .confetti--animation-fast {
            animation: confetti-fast 1.25s linear 1 forwards;
        }

        .congrats-container {
            text-align: center;
            padding: 20px 30px 30px 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            position: relative;
        }
        h1 {
            color: #333;
            font-size: 42px;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .login-link {
            display: inline-block;
            padding: 12px 24px;
            background-color: #00c09d;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .login-link:hover {
            background-color: #2ca893;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confetti-container js-container"></div>
    </div>
    <div class="congrats-container">
        <h2 style="text-transform: capitalize; word-break: break-word;">Congratulations! <?= $name; ?></h2>
        <p>You have successfully registered at <strong>Bright Minds</strong>. </p>
        <p>Welcome to our community!</p>
        <a href="login" class="login-link">Click here to login</a>
    </div>

    <script src="https://unpkg.com/gsap@3.9.0/dist/gsap.min.js"></script>
    <script>
        const Confettiful = function (el) {
            this.el = el;
            this.containerEl = null;

            this.confettiFrequency = 3;
            this.confettiColors = ['#EF2964', '#00C09D', '#2D87B0', '#48485E', '#EFFF1D'];
            this.confettiAnimations = ['slow', 'medium', 'fast'];

            this._setupElements();
            this._renderConfetti();
        };

        Confettiful.prototype._setupElements = function () {
            const containerEl = document.createElement('div');
            containerEl.classList.add('confetti-container');
            this.el.appendChild(containerEl);
            this.containerEl = containerEl;
        };

        Confettiful.prototype._renderConfetti = function () {
            this.confettiInterval = setInterval(() => {
                const confettiEl = document.createElement('div');
                const confettiSize = (Math.floor(Math.random() * 3) + 7) + 'px';
                const confettiBackground = this.confettiColors[Math.floor(Math.random() * this.confettiColors.length)];
                const confettiLeft = (Math.floor(Math.random() * this.el.offsetWidth)) + 'px';
                const confettiAnimation = this.confettiAnimations[Math.floor(Math.random() * this.confettiAnimations.length)];

                confettiEl.classList.add('confetti', 'confetti--animation-' + confettiAnimation);
                confettiEl.style.left = confettiLeft;
                confettiEl.style.width = confettiSize;
                confettiEl.style.height = confettiSize;
                confettiEl.style.backgroundColor = confettiBackground;

                confettiEl.removeTimeout = setTimeout(function () {
                    confettiEl.parentNode.removeChild(confettiEl);
                }, 3000);

                this.containerEl.appendChild(confettiEl);
            }, 25);
        };

        window.confettiful = new Confettiful(document.querySelector('.js-container'));
    </script>
</body>
</html>
