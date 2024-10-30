<?php
    session_start();
    
    if(isset($_POST['otp']) && $_POST['otp'] == $_SESSION['otp']) {
        echo 'verified';
    } else {
        echo 'Invalid OTP';
    }    
?>