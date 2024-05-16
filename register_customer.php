<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password != $confirm_password) {
        $error = "Passordene stemmer ikke overens.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO customers (email, password) VALUES (:email, :password)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header('Location: login_customer.php');
                exit;
            } else {
                $error = "Noe gikk galt. Vennligst prøv igjen senere.";
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
    <title>Registrer Kunde</title>
    <style>
        /* Legg til stiler her */
    </style>
</head>
<body>
    <h2>Registrer deg som kunde</h2>
    <form action="register_customer.php" method="post">
        <label for="email">E-post:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Passord:</label>
        <input type="password" name="password" required>
        <br>
        <label for="confirm_password">Bekreft Passord:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <button type="submit">Registrer</button>
    </form>
    <a href="login_customer.php">Har du allerede en konto? Logg inn her.</a>
</body>
</html>
