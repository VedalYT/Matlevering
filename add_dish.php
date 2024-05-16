<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $restaurant_name = $_POST['restaurant_name'];
    $dish_name = $_POST['dish_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $user_id = $_SESSION['id'];

    // Bildeopplasting
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $sql = "INSERT INTO menus (restaurant_name, dish_name, description, price, image_url, user_id) VALUES (:restaurant_name, :dish_name, :description, :price, :image_url, :user_id)";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(':restaurant_name', $restaurant_name, PDO::PARAM_STR);
        $stmt->bindParam(':dish_name', $dish_name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $target_file, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('location: dashboard.php');
        } else {
            echo "Noe gikk galt. Vennligst prøv igjen senere.";
        }

        unset($stmt);
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Legg til ny rett</title>
</head>
<body>
    <h2>Legg til ny rett</h2>
    <form action="add_dish.php" method="post" enctype="multipart/form-data">
        <label for="restaurant_name">Restaurantnavn:</label>
        <input type="text" name="restaurant_name" required>
        <br>
        <label for="dish_name">Rettnavn:</label>
        <input type="text" name="dish_name" required>
        <br>
        <label for="description">Beskrivelse:</label>
        <textarea name="description" required></textarea>
        <br>
        <label for="price">Pris:</label>
        <input type="text" name="price" required>
        <br>
        <label for="image">Bilde:</label>
        <input type="file" name="image" accept="image/*" required>
        <br>
        <button type="submit">Legg til</button>
    </form>
</body>
</html>
