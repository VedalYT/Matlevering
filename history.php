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
        .order {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-bottom: 20px;
        }
        .order p {
            margin: 5px 0;
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
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <p><strong>Bestillings-ID:</strong> <?php echo $order['id']; ?></p>
                <?php
                $items = json_decode($order['items'], true);
                foreach ($items as $item):
                ?>
                    <p><strong>Bestillingsdetaljer:</strong> <?php echo $item['dishName']; ?> | Pris: <?php echo $item['price']; ?> NOK | Antall: <?php echo $item['quantity']; ?></p>
                <?php endforeach; ?>
                <p><strong>Total:</strong> <?php echo number_format($order['total'], 2); ?> NOK</p>
                <p><strong>Bestillingstidspunkt:</strong> <?php echo $order['order_time']; ?></p>
                <p><strong>Leveringstidspunkt:</strong> <?php echo $order['delivery_time']; ?></p>
            </div>
        <?php endforeach; ?>
        <form method="post" action="history.php">
            <button type="submit" name="delete_history" class="delete-history-button">Slett Historikk</button>
        </form>
    <?php else: ?>
        <p>Du har ingen tidligere bestillinger.</p>
    <?php endif; ?>
</body>
</html>
