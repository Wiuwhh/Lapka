<?php
session_start();
require_once '../config.php';

// Проверяем, авторизован ли админ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Обработка добавления питомца
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $description = $_POST['description'];
    $photo = $_POST['photo']; // Тут можно добавить загрузку файлов

    $stmt = $pdo->prepare("INSERT INTO pets (name, breed, age, description, photo, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $breed, $age, $description, $photo]);

    header("Location: pets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить питомца</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            background-color: #f4f4f4; 
        }
        table { 
            width: 80%; 
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

    <h1>Добавить питомца</h1>

    <form method="POST">
        <label>Имя:</label>
        <input type="text" name="name" required>

        <label>Порода:</label>
        <input type="text" name="breed" required>

        <label>Возраст:</label>
        <input type="number" name="age" required>

        <label>Описание:</label>
        <textarea name="description" required></textarea>

        <label>Фото (ссылка):</label>
        <input type="text" name="photo" required>

        <button type="submit">Добавить</button>
    </form>

    <p><a href="pets.php">⬅ Назад</a></p>

</body>
</html>
