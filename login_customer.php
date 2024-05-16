<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['customer_loggedin']) && $_SESSION['customer_loggedin'] === true) {
    header('Location: index.html');
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
                            header('Location: index.html');
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            position: relative;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container .error {
            color: red;
            margin-bottom: 15px;
        }
        .login-container a {
            color: #007bff;
            text-decoration: none;
        }
        .login-container a:hover {
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
<body>
    <a href="index.php" class="back-button">Tilbake</a>
    <div class="login-container">
        <h2>Logg inn</h2>
        <form action="login_customer.php" method="post">
            <label for="email">E-post:</label>
            <input type="email" name="email" required>
            <br>
            <label for="password">Passord:</label>
            <input type="password" name="password" required>
            <br>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <button type="submit">Logg inn</button>
        </form>
        <a href="register_customer.php">Har du ikke en konto? Registrer deg her.</a>
    </div>
</body>
</html>
