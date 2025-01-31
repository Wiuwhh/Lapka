<?php
session_start();
require_once '../config.php';

// Проверяем, авторизован ли админ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Получаем ID питомца
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pets.php");
    exit();
}

$petId = intval($_GET['id']);

// Получаем данные питомца
$stmt = $pdo->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->execute([$petId]);
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pet) {
    header("Location: pets.php");
    exit();
}

// Обновление данных
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $description = $_POST['description'];
    $photo = $_POST['photo'];

    $updateStmt = $pdo->prepare("UPDATE pets SET name = ?, breed = ?, age = ?, description = ?, photo = ? WHERE id = ?");
    $updateStmt->execute([$name, $breed, $age, $description, $photo, $petId]);

    header("Location: pets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать питомца</title>
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
    

    <h1>Редактировать питомца</h1>

    <form method="POST">
        <label>Имя:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($pet['name']) ?>" required>

        <label>Порода:</label>
        <input type="text" name="breed" value="<?= htmlspecialchars($pet['breed']) ?>" required>

        <label>Возраст:</label>
        <input type="number" name="age" value="<?= htmlspecialchars($pet['age']) ?>" required>

        <label>Описание:</label>
        <textarea name="description" required><?= htmlspecialchars($pet['description']) ?></textarea>

        <label>Фото (ссылка):</label>
        <input type="text" name="photo" value="<?= htmlspecialchars($pet['photo']) ?>" required>

        <button type="submit">Сохранить изменения</button>
    </form>

    <p><a href="pets.php">⬅ Назад</a></p>

</body>
</html>
