<!-- панель управления -->

<?php


session_start();
require_once '../config.php';

// Проверяем, авторизован ли админ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Получаем статистику из БД
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPets = $pdo->query("SELECT COUNT(*) FROM pets")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM shop_products")->fetchColumn();
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 40px;
    background-color: #f4f4f4;
    }
    h1 {
        color: #333;
    }
    .stats {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 20px 0;
    }
    .stats div {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    nav ul {
        list-style: none;
        padding: 0;
    }
    nav li {
        display: inline;
        margin: 10px;
    }
    nav a {
        text-decoration: none;
        font-size: 18px;
        color: #007bff;
    }
</style>
</head>
<body>
    <h1>Добро пожаловать в админ-панель!</h1>
    
    <div class="stats">
        <div>👤 Пользователей: <?php echo $totalUsers; ?></div>
        <div>🐾 Питомцев: <?php echo $totalPets; ?></div>
        <div>🛒 Товаров: <?php echo $totalProducts; ?></div>
    </div>

    <nav>
        <ul>
            <li><a href="users.php">👤 Управление пользователями</a></li>
            <li><a href="pets.php">🐾 Управление питомцами</a></li>
            <li><a href="shop.php">🛒 Управление товарами</a></li>
            <li><a href="logout.php">🚪 Выйти</a></li>
        </ul>
    </nav>
</body>
</html>
