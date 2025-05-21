<?php
include '../check_admin.php'; // Проверка прав администратора
require_once '../db_connection.php'; // Подключение к базе данных

// Включение отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Обработка изменения статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status']; // Новые значения: оплачено, получено, отменено

    // Обновляем статус заказа
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "Статус заказа успешно обновлен.";
    } else {
        $message = "Ошибка при обновлении статуса заказа.";
    }
}

// Получаем список всех заказов
$sql = "SELECT o.id, o.user_id, o.total_amount, o.status, o.created_at, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление заказами</title>
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

        .actions button.edit {
            background-color: #4CAF50;
            color: white;
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

        .status-form {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .status-form select, .status-form button {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .status-form button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .status-form button:hover {
            background-color: #218838;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            color: #28a745;
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
        <h1>Управление заказами</h1>

        <?php if (isset($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID заказа</th>
                    <th>Пользователь</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Нет заказов.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= $order['username'] ?></td>
                            <td><?= number_format($order['total_amount'], 2) ?> руб.</td>
                            <td><?= $order['status'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <form class="status-form" method="POST">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="status">
                                        <option value="оплачено" <?= $order['status'] === 'оплачено' ? 'selected' : '' ?>>Оплачено</option>
                                        <option value="получено" <?= $order['status'] === 'получено' ? 'selected' : '' ?>>Получено</option>
                                        <option value="отменено" <?= $order['status'] === 'отменено' ? 'selected' : '' ?>>Отменено</option>
                                    </select>
                                    <button type="submit">Обновить</button>
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