<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Restaurantmeny</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js" defer></script>
    <style>
        .dropdown {
            position: relative;
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
    </style>
</head>
<body>
    <h1>Velg en restaurant</h1>
    <select id="restaurant-select">
        <option value="">Velg en restaurant</option>
        <option value="McDonalds">McDonalds</option>
        <option value="Burger King">Burger King</option>
        <option value="Subway">Subway</option>
    </select>
    <div id="menu-container"></div>

    <div class="dropdown">
        <button class="dropbtn">Logg inn / Registrer</button>
        <div class="dropdown-content">
            <a href="login.php">Logg inn som Restaurant-eier</a>
            <a href="login_customer.php">Logg inn som Kunde</a>
            <a href="register_customer.php">Registrer deg som Kunde</a>
        </div>
    </div>

  
</body>
</html>
