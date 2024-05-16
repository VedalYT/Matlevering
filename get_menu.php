<?php
require_once 'config.php';

$restaurant = $_GET['restaurant'];
$sql = "SELECT * FROM menus WHERE restaurant_name = :restaurant_name";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(':restaurant_name', $restaurant, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($menus);
    }
    unset($stmt);
}
unset($pdo);
?>
