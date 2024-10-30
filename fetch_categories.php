<?php
  // include 'config.php';  

  // try {
  //   $stmt = $conn->query("
  //     SELECT 
  //       class, 
  //       GROUP_CONCAT(category_name ORDER BY category_name) AS category_name,
  //       GROUP_CONCAT(category_id ORDER BY category_id) AS category_id,
  //       GROUP_CONCAT(category_price ORDER BY category_price) AS category_price
  //     FROM category_master2
  //     GROUP BY class
  //       ORDER BY CAST(SUBSTRING(class, 6) AS UNSIGNED);
  //   ");
  //   $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //   echo json_encode($categories);
  // } catch (PDOException $e) {
  //   echo json_encode(['error' => $e->getMessage()]);
  // }
?>

<?php
  include 'config.php';  

  try {
    $stmt = $conn->query("SELECT DISTINCT category_id, category_name, category_price FROM category_master");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categories);
  } catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
  }
?>