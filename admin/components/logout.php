<?php
   include '../config.php';

   session_start();
   $_SESSION = array(); // Clear session array
   session_unset(); // Unset session variables
   session_destroy(); // Destroy session

   // Redirect to login page after logout
   header('Location: ../../login');
   exit(0);
?>
