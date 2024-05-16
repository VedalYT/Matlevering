<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        echo "Passordene stemmer ikke overens.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "Registrering vellykket. Du kan nå logge inn.";
            } else {
                echo "Noe gikk galt. Vennligst prøv igjen senere.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Registrer</title>
</head>
<body>
    <h2>Registrer deg som restaurant-eier</h2>
    <form action="register.php" method="post">
        <label for="username">Brukernavn:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Passord:</label>
        <input type="password" name="password" required>
        <br>
        <label for="confirm_password">Bekreft Passord:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <button type="submit">Registrer</button>
    </form>
</body>
</html>
