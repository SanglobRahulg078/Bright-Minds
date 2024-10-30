<?php
   //site base name
   $p_title = basename($_SERVER['PHP_SELF'],".php");
      
   $name = htmlspecialchars($_SESSION['name']) ?? '';
   $uniqueId = htmlspecialchars($_SESSION['uniqueId']) ?? '';
   
   $user_id = $_SESSION['user_id'] ?? '';

   // Check if the user is a parent
   $isParent = $_SESSION['loginType'] == 'parent_login' ? 1 : 0;

   if ($isParent) {
      $stmt = $conn->prepare("
         SELECT p.id, p.name, p.phone, p.email, p.amount, p.created_at, p.address_line_1, p.address_line_2, p.city, p.pincode, p.country,  COUNT(a.app_id) AS applicant_count
         FROM parent_user_master p
         LEFT JOIN applicant_master a ON p.user_id = a.user_id
         WHERE p.name = :name AND p.user_id = :user_id
      ");
      $stmt->execute([':name' => $name, ':user_id' => $user_id]);
      $parent_details = $stmt->fetch(PDO::FETCH_ASSOC);

      // Extract parent details with default values
      $parent_id = $parent_details['id'] ?? '';
      $parent_name = $parent_details['name'] ?? '';
      $parent_phone = $parent_details['phone'] ?? '';
      $parent_email = $parent_details['email'] ?? '';
      $parent_amount = $parent_details['amount'] ?? '';
      $applicant_count = $parent_details['applicant_count'] ?? 0;

      // Format created_at date to display only month and year
      $created_at = !empty($parent_details['created_at']) ? date('d F Y', strtotime($parent_details['created_at'])) : '';
      
      // Retrieve address details
      $address1 = $parent_details['address_line_1'] ?? '';
      $address2 = $parent_details['address_line_2'] ?? '';
      $city = $parent_details['city'] ?? '';
      $pincode = $parent_details['pincode'] ?? '';
      $country = $parent_details['country'] ?? '';

      // Check if "Pay here" button was clicked
      if (isset($_POST['pay'])) {
         $_SESSION['application_id'] = $user_id;
         $_SESSION['email'] = $parent_email;
         $_SESSION['phone'] = $parent_phone;
         $_SESSION['price'] = $parent_amount;
         
         header('Location: ../pay');
         exit(); // Always exit after a redirect to stop script execution
      }
      
   } else {
      $stmt = $conn->prepare("
         SELECT application_id, class, school_name, id_proof, upload_photo 
         FROM applicant_master 
         WHERE application_id = :uniqueId AND user_id = :user_id
      ");
      $stmt->execute([':uniqueId' => $uniqueId, ':user_id' => $user_id]);
      $applicant_details = $stmt->fetch(PDO::FETCH_ASSOC);

      $application_id = $applicant_details['application_id'] ?? '';
      $class = $applicant_details['class'] ?? '';
      $school = $applicant_details['school_name'] ?? '';
      $id_proof = $applicant_details['id_proof'] ?? '';
      $upload_photo = $applicant_details['upload_photo'] ?? '';
   }
?>

<!-- Top header -->
<nav class="navbar nav-1">
   <!-- <div id="menu-btn" class="fas fa-bars"></div> -->
   <div>
      <span class="<?= $isParent ? '' : 'ml-3'; ?> text-white font-weight-bold lead">
         <?php if(!$isParent && !empty($upload_photo)) { ?>
            <img src="../admin/uploads/<?= htmlspecialchars($upload_photo); ?>" class="profile_pic mr-2" alt=""/>
         <?php } ?>
         
         <?php if($isParent) { ?>
            <span class="parent_header">
               <?= "<span>" . $name . "</span> [ " . $uniqueId . " ]"; ?>
            </span>
         <?php } else { ?>
            <span class="parent_header">
            <span><?= $name ? htmlspecialchars($name) : ''; ?></span>
            <span class="px-2"><?= $class ? ' | ' . htmlspecialchars($class) : ''; ?></span>
            <span><?= $school ? ' | ' . htmlspecialchars($school) : ''; ?></span></span>
         <?php } ?>
      </span>
   </div>

   <div class="d-flex align-items-center">
      <!-- Timer beside the logout & Logout button with confirmation -->       
      <?php if(!$isParent && $p_title != 'studentDashboard') { ?>
         <div id="timer" class="font-weight-bold lead text-white">02:00:00</div>
      <?php } ?>
      
      <a href="components/logout.php" title="logout" id="logoutBtn" onclick="return confirm('Logout from this website?')">
         <i class="fas fa-right-from-bracket"></i>
      </a>
   </div>
</nav>

<!-- Sidebar -->
<!-- <?php if($isParent) { ?>
   <header class="header">
      <div id="close-btn"><i class="fas fa-times"></i></div>

      <nav class="navbar">
         <aside class="sidebar">
            <?php if($isParent) { ?>
               <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
               <a href=""><i class="fas fa-building"></i><span>Applicant Details</span></a>
               <a id="export-csv">Export CSV</a>
               <a id="export-pdf">Export PDF</a>
            <?php } ?>
         </aside>
      </nav>
   </header>
<?php } ?> -->