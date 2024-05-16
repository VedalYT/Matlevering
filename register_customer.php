<?php
session_start();
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
                // Automatisk logg inn kunden
                $customer_id = $pdo->lastInsertId();
                session_regenerate_id();
                $_SESSION['customer_loggedin'] = true;
                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['email'] = $email;

                header('Location: index.php');
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            position: relative;
        }
        .register-container h2 {
            margin-bottom: 20px;
        }
        .register-container label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        .register-container input[type="email"],
        .register-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .register-container button:hover {
            background-color: #0056b3;
        }
        .register-container .error {
            color: red;
            margin-bottom: 15px;
        }
        .register-container a {
            color: #007bff;
            text-decoration: none;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 10px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<a href="index.php" class="back-button">Tilbake</a>
<body>
    <div class="register-container">
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
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <button type="submit">Registrer</button>
        </form>
        <a href="login_customer.php">Har du allerede en konto? Logg inn her.</a>
    </div>
</body>
</html>
