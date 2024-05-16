<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config.php';

// Hent menyen for den innloggede brukeren
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM menus WHERE user_id = :user_id";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($stmt);
}
unset($pdo);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Administrer Meny</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Velkommen, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <h2>Meny</h2>
    <table>
        <tr>
            <th>Rettnavn</th>
            <th>Beskrivelse</th>
            <th>Pris</th>
            <th>Bilde</th>
            <th>Handling</th>
        </tr>
        <?php foreach ($menus as $menu): ?>
        <tr>
            <td><?php echo htmlspecialchars($menu['dish_name']); ?></td>
            <td><?php echo htmlspecialchars($menu['description']); ?></td>
            <td><?php echo htmlspecialchars($menu['price']); ?></td>
            <td><img src="<?php echo htmlspecialchars($menu['image_url']); ?>" alt="<?php echo htmlspecialchars($menu['dish_name']); ?>" width="100"></td>
            <td>
                <a href="edit_dish.php?id=<?php echo $menu['id']; ?>">Rediger</a>
                <a href="delete_dish.php?id=<?php echo $menu['id']; ?>">Slett</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="add_dish.php">Legg til ny rett</a>
    <a href="logout.php">Logg ut</a>
</body>
</html>
