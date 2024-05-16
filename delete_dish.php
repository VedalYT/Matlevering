<?php
session_start();
require_once 'config.php';

// Kontroller om brukeren er logget inn
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Hent ID-en til retten som skal slettes
if (isset($_GET['id'])) {
    $dish_id = $_GET['id'];
} else {
    die('Feil: Ingen rett ID spesifisert.');
}

// Sjekk om brukeren har tillatelse til å slette denne retten
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

// Slett retten fra databasen
$sql = "DELETE FROM menus WHERE id = :id AND user_id = :user_id";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(':id', $dish_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit;
    } else {
        echo 'Feil: Kunne ikke slette retten. Vennligst prøv igjen senere.';
    }

    unset($stmt);
}

unset($pdo);
?>
