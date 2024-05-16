<?php
session_start();
require_once 'config.php';

// Kontroller om brukeren er logget inn
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Behandle skjema innsending
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Feilsøkingsutskrifter
    var_dump($_POST);
    var_dump($_FILES);

    $dish_name = isset($_POST['dish_name']) ? trim($_POST['dish_name']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $price = isset($_POST['price']) ? trim($_POST['price']) : null;
    $user_id = $_SESSION['id'];

    // Kontroller at alle nødvendige felt er fylt ut
    if (!$dish_name || !$description || !$price) {
        echo "Alle feltene må fylles ut.";
    } else {
        // Bildeopplasting
        $target_file = null;
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $target_dir = "uploads/";
            // Kontroller at uploads-mappen finnes
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "Feil under opplasting av bilde.";
                exit;
            }
        } else {
            echo "Bilde er påkrevd.";
            exit;
        }

        // Hent restaurantnavnet basert på brukerens ID
        $sql = "SELECT username FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $restaurant_name = $user['username'];

        // Sett inn ny rett i databasen
        $sql = "INSERT INTO menus (restaurant_name, dish_name, description, price, image_url, user_id) VALUES (:restaurant_name, :dish_name, :description, :price, :image_url, :user_id)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':restaurant_name', $restaurant_name, PDO::PARAM_STR);
            $stmt->bindParam(':dish_name', $dish_name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':image_url', $target_file, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit;
            } else {
                echo "Noe gikk galt. Vennligst prøv igjen senere.";
            }

            unset($stmt);
        }

        unset($pdo);
    }
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
    <a href="dashboard.php">Tilbake til dashboard</a>
</body>
</html>
