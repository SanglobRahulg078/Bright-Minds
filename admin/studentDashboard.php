<?php
   session_start();
   include '../config.php';
   
   if(!isset($_SESSION['name'])) {
      header("location:../login");
   }

   $user_id = $_SESSION['user_id'] ?? '';
   $name = htmlspecialchars($_SESSION['name']) ?? '';
   $uniqueId = htmlspecialchars($_SESSION['uniqueId']) ?? '';
   
   $stmt = $conn->prepare("
      SELECT application_id, applicant_name, class, category_name, aadhar_uid, school_name, id_proof, upload_photo, essay_topic, essay_description, word_count, sentence_count, essay_start_time, essay_end_time, essay_time, exam_date, exam_status, result_date, result_status, qualified_rank, religional_rank, national_rank, international_rank 
      FROM applicant_master 
      WHERE application_id = :uniqueId AND user_id = :user_id
   ");
   $stmt->execute([':uniqueId' => $uniqueId, ':user_id' => $user_id]);
   $applicant_details = $stmt->fetch(PDO::FETCH_ASSOC);
   
   $application_id = $applicant_details['application_id'] ?? '';
   $applicant_name = $applicant_details['applicant_name'] ?? '';
   $class = $applicant_details['class'] ?? '';
   $category_name = $applicant_details['category_name'] ?? '';
   $school = $applicant_details['school_name'] ?? '';
   $aadhar_uid = $applicant_details['aadhar_uid'] ?? '';
   $id_proof = $applicant_details['id_proof'] ?? '';
   $upload_photo = $applicant_details['upload_photo'] ?? '';

   $essay_topic = $applicant_details['essay_topic'] ?? '';
   $essay_description = $applicant_details['essay_description'] ?? '';
   $word_count = $applicant_details['word_count'] ?? '';
   $sentence_count = $applicant_details['sentence_count'] ?? '';

   $essay_start_time = $applicant_details['essay_start_time'] ?? '';
   $essay_end_time = $applicant_details['essay_end_time'] ?? '';
   $essay_time = $applicant_details['essay_time'] ?? '';
   
   $exam_date = $applicant_details['exam_date'] ?? '';
   $exam_status = $applicant_details['exam_status'] ?? '';
   $result_date = $applicant_details['result_date'] ?? '';
   $result_status = $applicant_details['result_status'] ?? '';

   // Remove "Class" followed by any space or hyphen
   $onlyNumber = preg_replace("/class[\s-]*/i", "", $class);
   
   // Check if the user is a parent
   $isParent = $_SESSION['loginType'] == 'parent_login' ? 1 : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- textarea  -->
   <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

   <!-- custom css file link  -->
   <link rel="stylesheet" type="text/css" href="../admin/css/style.css?v=<?= rand(1,4) . '.' . rand(1,99); ?>" />
   <link rel="stylesheet" type="text/css" href="../assets/css/main.css?v=<?= rand(1,4) . '.' . rand(1,99); ?>" />

   <style>
      .dashboard {
         padding: 5px;
         max-width: 100%;
         margin: 0 auto;
         background-color: transparent;
      }

      .container {
         max-width: 900px;
         margin: 25px auto;
         padding: 15px 20px;
         background-color: #fff;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
         border-radius: 10px;
      }

      h1 {
         text-align: center;
         font-size: 24px;
         color: #333;
         margin-bottom: 20px;
      }

      .section {
         margin-bottom: 20px;
      }

      .section-header {
            font-size: 20px;
         font-weight: bold;
         color: #555;
         border-bottom: 2px solid #ccc;
         margin-top: 20px;
         margin-bottom: 10px;
      }
      .section-header:first-child {
         margin-top: 0;
      }

      .info {
         display: flex;
         justify-content: space-between;
         padding: 10px;
         background-color: #f9f9f9;
         border-radius: 8px;
         margin-bottom: 5px;
         flex-wrap: wrap;
      }

      .info label {
         font-weight: bold;
         color: #444;
      }

      .info span {
         font-size: 14px;
         color: #333;
      }

      .highlight {
         background-color: #e6ffe6;
         border-left: 4px solid #28a745;
         padding-left: 8px;
      }

      .applicant-section {
         display: grid;
         grid-template-columns: repeat(2, 1fr);
         gap: 4px 12px;
      }

      .essay-content {
         background: #fff;
         padding: 8px 8px 0 22px;
      }

      /* Customize the scrollbar track */
      .container::-webkit-scrollbar-track {
         background-color: transparent; /* Scrollbar track background color */
      }

      /* Customize the scrollbar itself */
      .container::-webkit-scrollbar {
         width: 10px; /* Scrollbar width */
      }

      /* Customize the scrollbar thumb (the draggable part) */
      .container::-webkit-scrollbar-thumb {
         background-color: #888; /* Thumb color */
         border-radius: 10px; /* Rounded corners */
      }

      /* Hover effect on the scrollbar thumb */
      .container::-webkit-scrollbar-thumb:hover {
         background-color: #555; /* Darker thumb on hover */
      }

      .container .btn {
         background-color: var(--main-color);
         color: var(--white);
         float:right;
      }

      @media (max-width: 991px) {
         .sidebar {
            width: 100%;
            height: auto;
            position: relative;
         }

         .dashboard {
            margin-left: 0;
         }

         .has-sidebar main,
         .no-sidebar main {
            padding-left: 0;
         }
      }
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


      /* sticky notes css */
      /* .sticky-notes {
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
      } */
      
      /* Responsive Modal */
      @media (max-width: 768px) {
         .applicant-section {
            grid-template-columns: repeat(1, 1fr);
         }
      }

      @media (max-width: 768px) {
         .info span {
            font-size: 11px;
         }
      }

      @media print {
         body * {
            visibility: hidden;
         }

         #printableArea, #printableArea * {
            visibility: visible;
         }

         #printableArea {
            position: absolute;
            left: 0;
            top: 0;
         }
         .applicant-section {
            grid-template-columns: repeat(2, 1fr);
            gap: 0;
         }
      }
    </style>
</head>

<body style="background-image: url('../assets/img/hero-bg-light.webp'); height:100vh; overflow:hidden;">
   
   <!-- Top header -->   
   <nav class="navbar nav-1 pl-3">
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
      
      <div class="d-flex align-items-center">
         <a href="components/logout.php" title="logout" id="logoutBtn" onclick="return confirm('Logout from this website?')">
            <i class="fas fa-right-from-bracket"></i>
         </a>
      </div>
   </nav>

   <!-- dashboard section starts  -->
   <section class="dashboard">
      <div class="container" style="height: 83vh; overflow-y: scroll; overflow-x: auto;">
      
         <!-- Button to Print Report -->
         <a href="javascript:printDiv('printableArea')" class="btn">Print Report</a>

         <div id="printableArea">
            <!-- Applicant Information Section -->
            <div class="section-header">Personal Information</div>
            <div class="applicant-section">
               <div class="info">
                  <label>Applicant Name:</label>
                  <span><?= $applicant_name; ?></span>
               </div>
               <div class="info">
                  <label>Applicant ID / Login ID:</label>
                  <span><?= $application_id; ?></span>
               </div>
               <div class="info">
                  <label>Unique ID:</label>
                  <span><?= $aadhar_uid; ?></span>
               </div>
            </div>

            <!-- Exam Information Section -->
            <div class="section-header">Exam Information</div>
            <div class="applicant-section">
               <div class="info">
                  <label>Exam Date:</label>
                  <span><?= $exam_date; ?></span>
               </div>
               <div class="info">
                  <label>Exam Status:</label>
                  <span class="highlight"><?= $exam_status; ?></span>
               </div>
               <div class="info">
                  <label>Result Date:</label>
                  <span><?= $result_date; ?></span>
               </div>
               <div class="info">
                  <label>Result Status:</label>
                  <span class="highlight"><?= $result_status = 0 ? 'Pass' : 'Pending'; ?></span>
               </div>
            </div>

            <!-- Rank Information Section -->
            <div class="section-header">Rank Information</div>
            <div class="applicant-section">
               <div class="info">
                  <label>Qualified Rank:</label>
                  <span>0</span>
               </div>
               <div class="info">
                  <label>Regional Rank:</label>
                  <span>0</span>
               </div>
               <div class="info">
                  <label>National Rank:</label>
                  <span>0</span>
               </div>
               <div class="info">
                  <label>International Rank:</label>
                  <span>0</span>
               </div>
            </div>

            <!-- Essay Topic Section -->
            <div class="section-header">Essay Topic</div>

            <div class="info">
               <label>Topic:</label>
               <span><?= $essay_topic; ?></span>
            </div>
            <div class="info" style="display: flex; flex-direction: column; word-break: break-word;">
               <label>Essay Content:</label>
               <span class="essay-content"><?= $essay_description; ?></span>
            </div>

            <div class="applicant-section">
               <div class="applicant-section">
                  <div class="info">
                     <label>Word Count:</label>
                     <span><?= $word_count; ?></span>
                  </div>
                  <div class="info">
                     <label>Sentence Count:</label>
                     <span><?= $sentence_count; ?></span>
                  </div>
               </div>
               <div class="info">
                  <label>Total Time:</label>
                  <span><?= $essay_time; ?></span>
               </div>
               <div class="info">
                  <label>Essay Start Time:</label>
                  <span><?= $essay_start_time; ?></span>
               </div>
               <div class="info">
                  <label>Essay End Time:</label>
                  <span><?= $essay_end_time; ?></span>
               </div>
            </div>

         </div>  
      </div>
   </section>
   <!-- dashboard section ends -->
 
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      
   <script type="text/javascript">
      function printDiv(divId) {
         var printContents = document.getElementById(divId).innerHTML;
         var originalContents = document.body.innerHTML;

         document.body.innerHTML = printContents;
         window.print();
         document.body.innerHTML = originalContents;
      }
   </script>


   <!-- custom js file link  -->
   <script src="../admin/js/script.js"></script>
</body>
</html>