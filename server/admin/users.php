<!-- управление пользователями -->

<?php
session_start();
require_once '../config.php';

// Проверяем, авторизован ли админ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Удаление пользователя
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    header("Location: users.php");
    exit();
}

// Получаем список пользователей
$users = $pdo->query("SELECT id, username, email, phone, created_at FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: #fff;
        }
        a {
            text-decoration: none;
            color: #ff0000;
        }
    </style>
</head>
<body>

    <h1>Управление пользователями</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Дата регистрации</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id']; ?></td>
                <td><?= htmlspecialchars($user['username']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td><?= htmlspecialchars($user['phone']); ?></td>
                <td><?= $user['created_at']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id']; ?>">✏️ Редактировать</a> | 
                    <a href="users.php?delete=<?= $user['id']; ?>" onclick="return confirm('Удалить пользователя?');">❌ Удалить</a>
                </td>
            </tr>   
        <?php endforeach; ?>
    </table>

    <p><a href="dashboard.php">⬅ Назад в админ-панель</a></p>

</body>
</html>
