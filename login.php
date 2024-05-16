<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row['id'];
                        $stored_password = $row['password'];
                        if ($password === $stored_password) {
                            session_regenerate_id();
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['username'] = $username;
                            header('Location: dashboard.php');
                            exit;
                        } else {
                            $error = 'Feil passord.';
                        }
                    }
                } else {
                    $error = 'Ingen konto funnet med dette brukernavnet.';
                }
            } else {
                $error = 'Noe gikk galt. Vennligst prøv igjen senere.';
            }
            unset($stmt);
        }
    } else {
        $error = 'Vennligst fyll ut begge feltene.';
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Logg inn</title>
</head>
<body>
    <h2>Logg inn</h2>
    <form action="login.php" method="post">
        <label for="username">Brukernavn:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Passord:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Logg inn</button>
    </form>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <p>Har du ikke en konto? <a href="register.php">Registrer deg her</a>.</p>
</body>
</html>
