<?php
session_start();
if (!isset($_SESSION['customer_loggedin']) || $_SESSION['customer_loggedin'] !== true) {
    header('Location: login_customer.php');
    exit;
}

// Sett tidssonen til norsk tid (Oslo)
date_default_timezone_set('Europe/Oslo');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Hent handlekurven fra lokal lagring
    $cartJson = $_POST['cart'];
    $cart = json_decode($cartJson, true);

    // Beregn totalpris
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Sett opp bestillingsdata
    $orderTime = date('Y-m-d H:i:s');
    $deliveryTime = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    $orderData = [
        'customer_id' => $_SESSION['customer_id'],
        'items' => $cart,
        'total' => $total,
        'order_time' => $orderTime,
        'delivery_time' => $deliveryTime
    ];

    // Lagre bestillingsdata til databasen (tilpasses din database)
    require_once 'config.php';
    $itemsJson = json_encode($orderData['items']); // Mellomvariabel for JSON-kodet streng
    $sql = "INSERT INTO orders (customer_id, items, total, order_time, delivery_time) VALUES (:customer_id, :items, :total, :order_time, :delivery_time)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':customer_id', $orderData['customer_id'], PDO::PARAM_INT);
    $stmt->bindParam(':items', $itemsJson, PDO::PARAM_STR);
    $stmt->bindParam(':total', $orderData['total'], PDO::PARAM_STR);
    $stmt->bindParam(':order_time', $orderData['order_time'], PDO::PARAM_STR);
    $stmt->bindParam(':delivery_time', $orderData['delivery_time'], PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Tøm handlekurven
        echo "<script>localStorage.removeItem('cart');</script>";
        echo '<div class="confirmation">';
        echo '<h2>Bestillingen din er bekreftet!</h2>';
        echo '<p>Kjøpet ble gjort kl. ' . date('H:i', strtotime($orderTime)) . '.</p>';
        echo '<p>Forventet levering innen kl. ' . date('H:i', strtotime($deliveryTime)) . '.</p>';
        echo '<p>Takk for at du bestilte hos oss. Vi håper du nyter måltidet ditt!</p>';
        echo '<a href="index.php" class="back-button">Tilbake til forsiden</a>';
        echo '</div>';
    } else {
        echo '<p>Noe gikk galt. Vennligst prøv igjen senere.</p>';
    }
    unset($stmt);
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Handlekurv</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .confirmation {
            text-align: center;
            margin-top: 50px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .confirmation h2 {
            margin-bottom: 20px;
        }
        .confirmation p {
            margin-bottom: 10px;
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
        .total {
            font-weight: bold;
        }
        .checkout-button, .update-button, .history-button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .checkout-button:hover, .update-button:hover, .history-button:hover {
            background-color: #218838;
        }
        .delete-button {
            padding: 5px 10px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .back-button {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 20px;
            display: inline-block;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        .dish-img {
            width: 50px;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button">Tilbake</a>
    <h1>Handlekurv</h1>
    <div id="cart-container"></div>
    <button class="checkout-button" onclick="checkout()">Bekreft Bestilling</button>
    <a href="history.php" class="history-button">Se Bestillingshistorikk</a>

    <script>
        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cart-container');
            if (cart.length === 0) {
                cartContainer.innerHTML = '<p>Handlekurven er tom.</p>';
                document.querySelector('.checkout-button').style.display = 'none';
                return;
            }

            let cartTable = '<table><tr><th>Bilde</th><th>Rettnavn</th><th>Pris</th><th>Antall</th><th>Total</th><th>Handling</th></tr>';
            let total = 0;
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                cartTable += `<tr>
                    <td><img src="${item.image}" alt="${item.dishName}" class="dish-img"></td>
                    <td>${item.dishName}</td>
                    <td>${item.price.toFixed(2)} NOK</td>
                    <td><input type="number" min="1" value="${item.quantity}" onchange="updateQuantity(${index}, this.value)"></td>
                    <td>${itemTotal.toFixed(2)} NOK</td>
                    <td><button class="delete-button" onclick="removeFromCart(${index})">Slett</button></td>
                </tr>`;
            });
            cartTable += `<tr class="total"><td colspan="4">Total</td><td colspan="2">${total.toFixed(2)} NOK</td></tr></table>`;
            cartContainer.innerHTML = cartTable;
        }

        function updateQuantity(index, quantity) {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart[index].quantity = parseInt(quantity);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }

        function removeFromCart(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }

        function checkout() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart.length === 0) {
                alert('Handlekurven er tom.');
                return;
            }

            const formData = new FormData();
            formData.append('cart', JSON.stringify(cart));

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.body.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Noe gikk galt. Vennligst prøv igjen senere.');
            });
        }

        loadCart();
    </script>
</body>
</html>
