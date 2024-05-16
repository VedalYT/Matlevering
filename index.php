<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Restaurantmeny</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js" defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        select {
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
        }
        #menu-container {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .dish {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            width: 200px;
            text-align: center;
        }
        .dish img {
            width: 100%;
            border-radius: 8px;
        }
        .dropdown {
            position: absolute;
            top: 20px;
            left: 20px;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown:hover .dropbtn {
            background-color: #3e8e41;
        }
        .dropbtn {
            background-color: #4CAF50;
            color: white;
            padding: 16px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        .user-info {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .user-info p {
            margin: 0;
        }
        .user-info a {
            color: #007bff;
            text-decoration: none;
        }
        .user-info a:hover {
            text-decoration: underline;
        }
        .cart-button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .cart-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION['customer_loggedin']) && $_SESSION['customer_loggedin'] === true) {
        echo '<div class="user-info">';
        echo '<p>Logget inn som: ' . htmlspecialchars($_SESSION['email']) . '</p>';
        echo '<a href="logout_customer.php">Logg ut</a>';
        echo '</div>';
    } else {
        echo '<div class="dropdown">';
        echo '<button class="dropbtn">Logg inn / Registrer</button>';
        echo '<div class="dropdown-content">';
        echo '<a href="login_customer.php">Logg inn som Kunde</a>';
        echo '<a href="register_customer.php">Registrer deg som Kunde</a>';
        echo '</div>';
        echo '</div>';
    }
    ?>

    <h1>Velg en restaurant</h1>
    <select id="restaurant-select">
        <option value="">Velg en restaurant</option>
        <option value="McDonalds">McDonalds</option>
        <option value="Burger King">Burger King</option>
        <option value="Subway">Subway</option>
    </select>

    <div id="menu-container"></div>

    <a href="cart.php" class="cart-button">Se Handlekurv</a>

    <footer>
        <p>Er du en restaurant-eier? <a href="login.php">Logg inn her</a>.</p>
    </footer>
</body>
</html>
