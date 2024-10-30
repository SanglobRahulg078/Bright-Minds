<?php
   session_start();
   include '../config.php';

   if(!isset($_SESSION['name'])) {
      header("location:../login");
   }

   // parent_login is 1 and applicant_login is 0
   $isParent = $_SESSION['loginType'] == 'parent_login' ? 1 : 0;

   $user_id = $_SESSION['user_id'] ?? '';
   $name = htmlspecialchars($_SESSION['name']) ?? '';
   $uniqueId = htmlspecialchars($_SESSION['uniqueId']) ?? '';

   if(!$isParent) {
      $stmt = $conn->prepare("SELECT application_id, class, school_name, id_proof, upload_photo FROM applicant_master WHERE application_id = :uniqueId AND user_id = :user_id");
      $stmt->execute([':uniqueId' => $uniqueId, ':user_id' => $user_id]);
      $applicant_details = $stmt->fetch(PDO::FETCH_ASSOC);

      $application_id = $applicant_details['application_id'] ?? '';
      $class = $applicant_details['class'] ?? '';
      $school = $applicant_details['school_name'] ?? '';
      $id_proof = $applicant_details['id_proof'] ?? '';
      $upload_photo = $applicant_details['upload_photo'] ?? '';

      // Remove "Class" followed by any space or hyphen
      $onlyNumber = preg_replace("/class[\s-]*/i", "", $class);
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!-- <title>Welcome Dashboard</title> -->
   <title>Welcome, <?= $_SESSION['name']; ?></title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

   <!-- custom css file link  -->
   <link rel="stylesheet" type="text/css" href="../admin/css/style.css?v=<?= rand(1,4) . '.' . rand(1,99); ?>" />

   <style>
      .parent_portal {
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
         background-color: #fff;
         padding: 10px 4px;
         border-radius: 10px;
         width: auto;
      }
      .preview-content-confirm {
         display: flex !important;
         display: -ms-flexbox !important;
         justify-content: space-between;
         margin-top: 6px;     
      }               
      .nowrap {
         white-space: nowrap;
      }
      
      #applicantsTable th{
         vertical-align:top;
         text-align:center;
         background-color: #9DA4B1;
         color: #000;
      }
      #applicantsTable .form-control-file {
         display:none;
      }
      .dashboard table {
         background-color:#fff;
      }
      .dashboard table th{
         color: var(--white);
         font-weight: bold;
      }
      tr:has(th):hover {
         background-color: transparent;
      }
      tr:hover {
         background-color: #f1f1f1;
      }
      .parent_portal img {
         max-height: 60px;
         max-width: 60px;
         border-radius: 5px;
      }
      
      @media (max-width: 991px) {
         .sidebar {
            width: 100%;
            height: auto;
            position: relative;
         }
      }

      .radio-label:has(input[type="radio"]) {
         font-size: 14px;
         line-height: 6px;
         margin-bottom: 10px;
      }
      input[type="radio"]:not(:checked) + span {
         color: gray;
      }

      .topic {
         font-weight: bold;
         margin: 5px 0;
         user-select: none;
         font-size: 15px;
      }
      .fa-pencil-square {
         font-size: 14px;
         margin-left: 5px;
         cursor: pointer;
         display:none;
      }
      #preview-topic { 
         font-size: 14px;
         color: var(--main-color);
         margin: 5px 0 8px 0;         
         font-weight: bold;
      }
      #preview-button {
         margin-top: 8px;
         display: flex;
         justify-content: center;
      }
      #confirm-submit {
         color: #fff;
         display:none;
      }
      section{
        background-color: transparent;
      }

      /* sticky notes css */
      .sticky-notes {
         float: right; 
         background-color:red;
         width: 30%;
         height: 100vh;

         display: flex;
         justify-content: end;
         flex-direction: column;
      }

      .sticky-notes div {
         height: 300px;
         width: 250px;
         background-color: yellow;
      }
      
      section {
         padding: 30px 5px;

      }

      .section h1 {
         margin: 0;
         color: #388da8;
         /* font-size: 30px; */
         font-weight: 700;
         /* line-height: 56px; */
      }
      .p_left {
         color: #fff;
         text-align: left;
         font-size: 15px;
      }
      .fw-16 {
         font-size: 16px;
      }
      
      div.checkbox {
         text-align: center;
         padding-top: 1rem;
      }
      input[type="checkbox"] {
         width: 14px;
         height: 14px;
         transform: scale(1.5); /* Increase scale to make checkbox larger */
         -webkit-transform: scale(1.5); /* For older webkit browsers */
         margin-right: 5px;
      }
      
      input[type="checkbox"] + label {
         display: inline;
         text-align:left !important;
      }
      
      .btn-get-started {
         cursor: pointer;
         color: #fff;
         background: #388da8;
         font-weight: 500;
         font-size: 16px;
         letter-spacing: 1px;
         padding: 6px 20px;
         border-radius: 50px;
         transition: 0.5s;
         box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
      }
      .btn-get-started.disabled {
         background: gray;
         color: #fff;
         cursor: none;
      }

     .btn-get-started:not(.disabled):hover {
         color: #fff;
         background: color-mix(in srgb, #388da8, transparent 15%);
         box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
      }


      footer {
         background-color: #fff;
         color: #3d4348;
         font-size: 14px;
         position: absolute !important;
         width: 100vw;
         bottom: 0px !important;
         padding: 10px 0;
      }
      
      /* media queries  */
      @media (max-width: 445px){ 
         div.checkbox:has(label) {
            text-align:left;
         }
      }

      .navbar.nav-1 {
         padding: 8px;
      }

      /* .profile-container {
         display: flex;
         align-items: center;
         position: relative;
      } */

      .profile-pic {
         height: 35px;
         width: 35px;
         border-radius: 50%;
         cursor: pointer;
      }

      .applicant-name {
         color: #fff;
         font-size: 15px;
         font-weight: bold;
         margin-left: 4px;
      }

      .extra-info {
         display: none;
         position: absolute;
         top: 55px;
         background-color: #fff;
         padding: 8px;
         border-radius: 5px;
         box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
         width: max-content;
         white-space: nowrap;
      }

      .profile-container:hover .extra-info {
         display: block;
      }

      #logoutBtn {
         color: #fff;
         font-size: 18px;
         text-decoration: none;
      }
   </style>
</head>

<body style="background-image: url('../assets/img/hero-bg-light.webp'); ">

   <!-- Top header -->   
   <nav class="navbar nav-1">
      <div class="profile-container">
         <?php if(!$isParent && !empty($upload_photo)) { ?>
            <img src="../admin/uploads/<?= htmlspecialchars($upload_photo); ?>" class="profile-pic mr-2" alt="Profile Photo" loading="lazy"/>
         <?php } ?>
         <span class="applicant-name"><?= $name; ?></span>        
         <?php if ($class || $school) { ?>
            <div class="extra-info">
               <span><?= $class ? '<strong>Class: </strong>' . htmlspecialchars($onlyNumber) : ''; ?></span><br>
               <span><?= $school ? '<strong>School: </strong>' . htmlspecialchars($school) : ''; ?></span>
            </div>
         <?php } ?>
      </div>
      <a href="components/logout.php" title="logout" id="logoutBtn" onclick="return confirm('Logout from this website?')">
         <i class="fas fa-right-from-bracket"></i>
      </a>
   </nav>


   <!-- dashboard section starts  -->
   <section class="dashboard">
      <main>  
         <div class="container text-center">
            <div class="row mb-md-4 mb-2">
               <div class="col-md-4" style="border: 2px double #446C91;">
                  <div class="d-flex flex-column justify-content-center align-items-center">
                     <h1 data-aos="fade-up">
                        <span>Srijan Essay Premier League <br> November 2024</span>
                     </h1>
                  </div>
               </div>
               <div class="col-md-8 align-content-center" style="background-color: #446C91;">
                  <p data-aos="fade-up" data-aos-delay="100" class="p_left">
                     An unique platform for young Indian students 
                     to express their thoughts, creativity, and vision for the future through an - <strong class="d-inline-block">Srijan Essay Premier League - EPL</strong><br/>
                  </p>
               </div>
            </div>
            <div class="row bg-light shadow-lg">
               <div class="col text-left fw-16 py-4">
                  <p>Pre-Assessment Confirmation Checklist</p>

                  <ul class=" pl-4 ml-md-5">
                     <li>I confirm that I have my parents or guardian's consent to participate in this competition.</li>
                     <li>I confirm that the essay I submit will be my own original work, and I will not engage in plagiarism</li>
                     <li>I understand that using any AI tools, online content generators, or copying from other sources is not allowed.</li>
                     <li>I understand the consequences of submitting plagiarized content or violating the competition rules.</li>
                  </ul>

                  <div class="checkbox">
                     <input type="checkbox" id="checkbox">
                     <label for="checkbox">I have read and agreed to abide by the competition rules and regulations.</label>
                  </div>
               </div>
            </div>
            
            <div class="d-flex justify-content-center mt-3" data-aos="fade-up" data-aos-delay="200">
               <button class="btn-get-started disabled">Start Assessment</button>
            </div>
         </div>
      </main>
   </section>
   <!-- dashboard section ends -->
    
   <footer>
      <div class="text-center">
         <p>© <span>Copyright</span> <strong class="px-1">BrightMinds,</strong><span>All Rights Reserved.</span></p>
         Designed by <a href="https://sanglob.in/" target="_blank">Sanglob.in</a>
      </div>
   </footer>
 
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <script>
      $(document).ready(function() {
         // Get the checkbox and button elements
         const checkbox = $('#checkbox');
         const button = $('.btn-get-started');

         // Initially disable the button
         button.prop('disabled', true);

         // Event listener for checkbox click
         checkbox.on('change', function() {
            if (this.checked) {
            button.removeClass('disabled');
            button.prop('disabled', false);
            } else {
               button.addClass('disabled');
            button.prop('disabled', true);
            }
         });

         // Event listener for button click to redirect to the assessment page
         button.on('click', function() {
            if (checkbox.is(':checked')) {
            // Redirect to the assessment page
            window.location.href = 'assessment'; // Change 'assessment' to the actual URL of the assessment page
            }
         });
      });
   </script>
</body>
</html>