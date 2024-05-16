<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['customer_loggedin']) && $_SESSION['customer_loggedin'] === true) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT id, email, password FROM customers WHERE email = :email";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row['id'];
                        $hashed_password = $row['password'];
                        if (password_verify($password, $hashed_password)) {
                            session_regenerate_id();
                            $_SESSION['customer_loggedin'] = true;
                            $_SESSION['customer_id'] = $id;
                            $_SESSION['email'] = $email;
                            header('Location: index.php');
                            exit;
                        } else {
                            $error = 'Feil passord.';
                        }
                    }
                } else {
                    $error = 'Ingen konto funnet med denne e-postadressen.';
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
    <title>Logg inn Kunde</title>
    <style>
        /* Legg til stiler her */
    </style>
</head>
<body>
    <h2>Logg inn</h2>
    <form action="login_customer.php" method="post">
        <label for="email">E-post:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Passord:</label>
        <input type="password" name="password" required>
        <br>
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <button type="submit">Logg inn</button>
    </form>
    <a href="register_customer.php">Har du ikke en konto? Registrer deg her.</a>
</body>
</html>
