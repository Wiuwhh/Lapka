<?php
session_start();
require_once '../config.php';

// Проверяем, авторизован ли админ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Обработка добавления товара
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $photo_path = $_POST['photo_path']; 

    $stmt = $pdo->prepare("INSERT INTO shop_products (name, description, price, stock_quantity, photo_path, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $description, $price, $stock_quantity, $photo_path]);

    header("Location: shop.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить товар</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            background-color: #f4f4f4; 
        }
        table { 
            width: 90%; 
            margin: 20px auto; 
            border-collapse: collapse; 
            background: #fff; 
        }
        th, td { 
            padding: 10px; 
            border: 1px solid #ddd; 
            text-align: left; 
        }
        th { 
            background: #007bff; 
            color: #fff; 
        }
        a { 
            text-decoration: none; 
            color: #007bff; 
        }
        .add-btn { 
            display: inline-block; 
            margin: 10px; 
            padding: 10px 20px; 
            background: #28a745; 
            color: white; 
            border-radius: 5px; 
        }
    </style>
</head>
<body>

    <h1>Добавить товар</h1>

    <form method="POST">
        <label>Название:</label>
        <input type="text" name="name" required>

        <label>Описание:</label>
        <textarea name="description" required></textarea>

        <label>Цена (₽):</label>
        <input type="number" name="price" step="0.01" required>

        <label>Количество на складе:</label>
        <input type="number" name="stock_quantity" required>

        <label>Фото (ссылка):</label>
        <input type="text" name="photo_path" required>

        <button type="submit">Добавить</button>
    </form>

    <p><a href="shop.php">⬅ Назад</a></p>

</body>
</html>
