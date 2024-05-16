<?php
session_start();
require_once 'config.php';

// Kontroller om brukeren er logget inn
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Hent ID-en til retten som skal redigeres
if (isset($_GET['id'])) {
    $dish_id = $_GET['id'];
} else {
    die('Feil: Ingen rett ID spesifisert.');
}

// Hent informasjon om retten fra databasen
$sql = "SELECT * FROM menus WHERE id = :id AND user_id = :user_id";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(':id', $dish_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $dish = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$dish) {
        die('Feil: Ingen rett funnet med denne ID-en eller du har ikke tilgang til denne retten.');
    }
} else {
    die('Feil: Kunne ikke hente data fra databasen.');
}

// Behandle skjema innsending
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $restaurant_name = $_POST['restaurant_name'];
    $dish_name = $_POST['dish_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Bildeopplasting (hvis et nytt bilde er valgt)
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        // Behold det gamle bildet hvis ikke et nytt er valgt
        $target_file = $dish['image_url'];
    }

    // Oppdater databasen med de nye verdiene
    $sql = "UPDATE menus SET restaurant_name = :restaurant_name, dish_name = :dish_name, description = :description, price = :price, image_url = :image_url WHERE id = :id AND user_id = :user_id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(':restaurant_name', $restaurant_name, PDO::PARAM_STR);
        $stmt->bindParam(':dish_name', $dish_name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':image_url', $target_file, PDO::PARAM_STR);
        $stmt->bindParam(':id', $dish_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: dashboard.php');
            exit;
        } else {
            echo 'Feil: Kunne ikke oppdatere retten. Vennligst prøv igjen senere.';
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
    <title>Rediger Rett</title>
</head>
<body>
    <h2>Rediger Rett</h2>
    <form action="edit_dish.php?id=<?php echo $dish_id; ?>" method="post" enctype="multipart/form-data">
        <label for="restaurant_name">Restaurantnavn:</label>
        <input type="text" name="restaurant_name" value="<?php echo htmlspecialchars($dish['restaurant_name']); ?>" required>
        <br>
        <label for="dish_name">Rettnavn:</label>
        <input type="text" name="dish_name" value="<?php echo htmlspecialchars($dish['dish_name']); ?>" required>
        <br>
        <label for="description">Beskrivelse:</label>
        <textarea name="description" required><?php echo htmlspecialchars($dish['description']); ?></textarea>
        <br>
        <label for="price">Pris:</label>
        <input type="text" name="price" value="<?php echo htmlspecialchars($dish['price']); ?>" required>
        <br>
        <label for="image">Bilde:</label>
        <input type="file" name="image" accept="image/*">
        <br>
        <button type="submit">Oppdater</button>
    </form>
    <a href="dashboard.php">Tilbake til dashboard</a>
</body>
</html>
