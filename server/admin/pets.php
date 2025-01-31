<!-- управление животными -->

<?php
session_start();
require_once '../config.php';

// Проверяем, авторизован ли админ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Получаем всех питомцев
$pets = $pdo->query("SELECT * FROM pets ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Удаление питомца
if (isset($_GET['delete'])) {
    $petId = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM pets WHERE id = ?")->execute([$petId]);
    header("Location: pets.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление питомцами</title>
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

    <h1>Управление питомцами</h1>

    <a href="add_pet.php" class="add-btn">➕ Добавить питомца</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Порода</th>
            <th>Возраст</th>
            <th>Описание</th>
            <th>Фото</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($pets as $pet): ?>
        <tr>
            <td><?= $pet['id']; ?></td>
            <td><?= htmlspecialchars($pet['name']); ?></td>
            <td><?= htmlspecialchars($pet['breed']); ?></td>
            <td><?= htmlspecialchars($pet['age']); ?></td>
            <td><?= htmlspecialchars($pet['description']); ?></td>
            <td><img src="<?= htmlspecialchars($pet['photo']); ?>" width="50" height="50"></td>
            <td>
                <a href="edit_pet.php?id=<?= $pet['id']; ?>">✏️ Редактировать</a> | 
                <a href="pets.php?delete=<?= $pet['id']; ?>" onclick="return confirm('Удалить питомца?');">❌ Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="dashboard.php">⬅ Назад в админ-панель</a></p>

</body>
</html>
