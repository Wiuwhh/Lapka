<?php
include '../check_admin.php'; // Проверка прав администратора
require_once '../db_connection.php'; // Подключение к базе данных

// Включение отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Обработка удаления подписки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subscription'])) {
    $subscription_id = intval($_POST['subscription_id']);
    
    // Удаляем подписку
    $sql = "DELETE FROM subscriptions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $subscription_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "Подписка успешно удалена.";
    } else {
        $message = "Ошибка при удалении подписки.";
    }
}

// Получаем список всех подписок
$sql = "SELECT s.id, s.user_id, s.pet_id, s.amount, s.start_date, s.end_date, s.status, 
               u.username, p.name as pet_name 
        FROM subscriptions s 
        JOIN users u ON s.user_id = u.id 
        JOIN pets p ON s.pet_id = p.id 
        ORDER BY s.start_date DESC";
$result = $conn->query($sql);

$subscriptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subscriptions[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление подписками</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Rubik+Spray+Paint&display=swap" rel="stylesheet">
    <style>
        .admin-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            background-color: floralwhite;
        }

        th {
            background-color: #9F8B70;
            color: white;
        }

        .actions button {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .actions button.delete {
            background-color: #f44336;
            color: white;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            border: 3px solid #9F8B70;
            border-radius: 30px;
            text-decoration: none;
            color: #fff;
            background-color: #9F8B70;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #786C5F;
            border-color: #786C5F;
        }

        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-inactive {
            color: red;
            font-weight: bold;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .delete-form {
            display: inline;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="../../index.html" class="logo-button">
                <h1 class="logo">ЛАПКА <span>| Приют для животных</span></h1>
            </a>
            <a href="../../admin_panel.html" class="back-button">Назад</a>
        </div>
    </header>

    <div class="admin-container">
        <h1>Управление подписками пользователей</h1>

        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'Ошибка') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID подписки</th>
                    <th>Пользователь</th>
                    <th>Животное</th>
                    <th>Сумма</th>
                    <th>Начало</th>
                    <th>Окончание</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subscriptions)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">Нет активных подписок.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <tr>
                            <td><?= $subscription['id'] ?></td>
                            <td><?= $subscription['username'] ?></td>
                            <td><?= $subscription['pet_name'] ?></td>
                            <td><?= number_format($subscription['amount'], 2) ?> руб.</td>
                            <td><?= date('d.m.Y', strtotime($subscription['start_date'])) ?></td>
                            <td><?= $subscription['end_date'] ? date('d.m.Y', strtotime($subscription['end_date'])) : 'Не указано' ?></td>
                            <td>
                                <span class="status-<?= $subscription['status'] ?>">
                                    <?= $subscription['status'] === 'active' ? 'Активна' : 'Неактивна' ?>
                                </span>
                            </td>
                            <td class="actions">
                                <form class="delete-form" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту подписку?');">
                                    <input type="hidden" name="subscription_id" value="<?= $subscription['id'] ?>">
                                    <input type="hidden" name="delete_subscription" value="1">
                                    <button type="submit" class="delete">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>