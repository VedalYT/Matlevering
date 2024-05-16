<?php
// Database konfigurasjon
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'restaurant');

// Forsøk å koble til databasen
try {
    $pdo = new PDO("sqlite:" . DB_NAME . ".db");
    // Sett PDO feilhåndteringsmodus til exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Kunne ikke koble til databasen. " . $e->getMessage());
}
?>
