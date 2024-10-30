<?php
    // remove all error warnings
    error_reporting(1);

    $db_name = 'mysql:host=154.41.233.154;dbname=u422319652_vmshikshadb';
    $db_user_name = 'u422319652_dbadmin';
    $db_user_pass = 'Test4312';

    try {
        $conn = new PDO($db_name, $db_user_name, $db_user_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: ");
    }

    date_default_timezone_set('Asia/Kolkata');

    $siteUrl = 'brightminds.sanglob.in';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>
