<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['customer_loggedin']) || $_SESSION['customer_loggedin'] !== true) {
    header('Location: login_customer.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_history'])) {
    $sql = "DELETE FROM orders WHERE customer_id = :customer_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location: history.php');
    exit;
}

$sql = "SELECT * FROM orders WHERE customer_id = :customer_id ORDER BY order_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

unset($pdo);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Bestillingshistorikk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-button, .delete-history-button {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .back-button:hover, .delete-history-button:hover {
            background-color: #5a6268;
        }
        .delete-history-button {
            background-color: #dc3545;
        }
        .delete-history-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <a href="cart.php" class="back-button">Tilbake</a>
    <h1>Din Bestillingshistorikk</h1>
    <?php if (count($orders) > 0): ?>
        <table>
            <tr>
                <th>Bestillings-ID</th>
                <th>Bestillingsdetaljer</th>
                <th>Total</th>
                <th>Bestillingstidspunkt</th>
                <th>Leveringstidspunkt</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo nl2br(htmlspecialchars(json_encode(json_decode($order['items']), JSON_PRETTY_PRINT))); ?></td>
                    <td><?php echo number_format($order['total'], 2); ?> NOK</td>
                    <td><?php echo $order['order_time']; ?></td>
                    <td><?php echo $order['delivery_time']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <form method="post" action="history.php">
            <button type="submit" name="delete_history" class="delete-history-button">Slett Historikk</button>
        </form>
    <?php else: ?>
        <p>Du har ingen tidligere bestillinger.</p>
    <?php endif; ?>
</body>
</html>
