<?php
    $p_title = basename($_SERVER['PHP_SELF'],".php");

    // switch($p_title) {
    //     case "login":          $title = 'Login Page | '    ;break;
    //     case "pay":            $title = 'Payment Page | '  ;break;
    //     case "register":       $title = 'Register Page | ' ;break;
    //     case "password-reset": $title = 'Forgot Password | ';break;
    //     case "privacy-policy": $title = 'Privacy Policy | ';break;
    //     case "term-condition": $title = 'Term Condition | ';break;
    //     default: $title = ''; break;
    // }
    
    switch($p_title) {
        case "login":          
            $title = 'Login | '; 
            $description = 'Login to access your WriteQuest account and continue your journey.';
            $keywords = 'WriteQuest login, login page, account access'; break;
        case "pay":            
            $title = 'Payment | '; 
            $description = 'Complete your WriteQuest payment securely to participate in the competition.';
            $keywords = 'WriteQuest payment, secure payment, transaction'; 
            break;
        case "register":       
            $title = 'Register | '; 
            $description = 'Register for WriteQuest and join the national essay competition today.';
            $keywords = 'WriteQuest registration, register, join competition'; 
            break;
        case "password-reset": 
            $title = 'Forgot Password | '; 
            $description = 'Reset your WriteQuest password to regain access to your account.';
            $keywords = 'WriteQuest forgot password, reset password, account recovery'; 
            break;
        case "privacy-policy": 
            $title = 'Privacy Policy | '; 
            $description = 'Learn about the privacy policies governing the WriteQuest platform.';
            $keywords = 'WriteQuest privacy policy, data protection'; 
            break;
        case "term-condition": 
            $title = 'Terms & Conditions | '; 
            $description = 'Understand the terms and conditions of using the WriteQuest platform.';
            $keywords = 'WriteQuest terms, terms and conditions'; 
            break;
        default: 
            $title = ''; 
            $description = 'WriteQuest - National Essay Competition organized by VM Shiksha Society in partnership with Sanglob.';
            $keywords = 'WriteQuest, essay competition, VM Shiksha Society'; 
            break;
    }
    
    // Determine the current URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $canonicalURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Security-focused Meta Tags -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="no-referrer">
    <meta http-equiv="X-Permitted-Cross-Domain-Policies" content="none">
    
    <!-- SEO Meta Tags -->    
    <meta name="description" content="<?= $description; ?>">
    <meta name="keywords" content="<?= $keywords; ?>">
    <meta name="author" content="Rahul Gupta">
	<meta property="og:title" content="VISIT BrightMinds" />
    <meta property="og:type" content="website">
	<!-- <meta property="og:url" content="brightminds.sanglob.in" /> -->
    <meta property="og:url" content="<?= $canonicalURL; ?>">
    <meta property="og:image" content="assets/img/favicon.png">
    <meta property="og:description" content="<?= $description; ?>">
    
    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#77b6ca">
    <meta name="robots" content="index, follow">


    <!-- Favicon -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    
    <!-- Custom CSS Files with Cache Buster -->
    <link rel="stylesheet" type="text/css" href="assets/css/main.css?v=<?= rand(1,4) . '.' . rand(1,999); ?>" />
    <?php if($p_title == 'register' || $p_title == 'login' || $p_title == 'password-reset') {
        echo '<link rel="stylesheet" href="assets/css/register.css?v=' . rand(1,4) . '.' . rand(1,999) . '">'; 
    } ?>

    <!-- Page Title & Canonical Link -->
    <link rel="canonical" href="<?= $canonicalURL; ?>" />
    <title>
        <?= $title; ?> WriteQuest - Organized by: VM Shiksha Society along with Technology Partner: Sanglob
    </title>
    <script>
        let index = 0;
        const arr = <?php echo json_encode(["WriteQuest - Organized by: VM Shiksha Society", "WriteQuest along with Technology Partner: Sanglob", "WriteQuest - Enjoy Your Day With Sanglob!"]); ?>;

        function changeTitle() {
            document.title = <?= json_encode($title); ?> + arr[index];
            index = (index + 1) % arr.length;
        }        
        setInterval(changeTitle, 3000);
    </script>
</head>

<body <?php if($p_title != 'register') { echo 'class="index-page"'; } if($p_title == 'register') { echo 'style="background-image:url(\'assets/img/hero-bg-light.webp\'); background-size: 100%;"'; } ?>>

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="/" class="logo d-flex align-items-center me-auto">
                <img src="assets/img/logo.png" alt="WriteQuest Logo">
                <h1 class="sitename">WriteQuest</h1>
                <!-- <p>Organized by: VM Shiksha Society 
                    <br>Technology Partner: Sanglob
                </p> -->
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="/" class="active">Home</a></li>
                    <li><a href="index#about">About</a></li>
                    <li><a href="index#services">Benefits</a></li>
                    <li><a href="index#features">Prizes</a></li>
                    <li><a href="index#faq">FAQ</a></li>
                    <li><a href="index#contact">Contact</a></li>
                    <li>
                        <?php if($p_title == 'login') { ?>
                            <a class="btn-getstarted text-center d-lg-none d-block w-25 mx-2" href="register">Register</a>
                        <?php } else { ?>
                            <a class="btn-getstarted text-center d-lg-none d-block w-25 mx-2" href="login">Login</a>
                        <?php } ?>
                    </li>
                </ul>

                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <?php if($p_title == 'login') { ?>
                <a class="btn-getstarted text-center d-none d-md-block" href="register">Register Now</a>
            <?php } else { ?>
                <a class="btn-getstarted px-5 text-center d-none d-md-block" href="login">Login</a>
            <?php } ?>
        </div>
    </header>