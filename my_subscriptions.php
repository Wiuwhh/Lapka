<?php
session_start();
require_once 'server/db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT s.id, s.pet_id, s.amount, s.start_date, s.end_date, s.status, p.name as pet_name 
        FROM subscriptions s 
        JOIN pets p ON s.pet_id = p.id 
        WHERE s.user_id = ? 
        ORDER BY s.start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subscriptions = [];
while ($row = $result->fetch_assoc()) {
    $subscriptions[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Rubik+Spray+Paint&display=swap" rel="stylesheet"> 
    <title>Мои подписки | ЛАПКА</title>
    <style>
        .subscriptions-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .subscription {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .subscription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .subscription-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        .subscription p {
            margin: 5px 0;
            color: #666;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: red;
            font-weight: bold;
        }
        .cancel-button, .delete-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .cancel-button:hover, .delete-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.html" class="logo-button">
                <h1 class="logo">ЛАПКА <span>| Приют для животных</span></h1>
            </a>
            <div class="auth-buttons">
                <a href="register.html" class="register-button" id="register-btn">Регистрация</a>
                <a href="login.html" class="login-button" id="login-btn">Вход</a>
                <a href="#" id="account-icon" style="display: none;" class="a"><span id="user-fio" style="display: none;"></span> 👤</a>
                <a href="#" style="display: none;" class="a" id="logout-btn">Выйти</a>
            </div>
        </div>
    </header>
    <div class="colorful-container">
        <nav class="nav">
            <a href="about.html" class="nav-link">О нас</a>
            <a href="donate.html" class="nav-link">Помощь</a>
            <a href="shop.php" class="nav-link">Магазин</a>
            <a href="pets.php" class="nav-link">Наши животные</a>
        </nav>
        <main class="main">
            <div class="content">
                <h1 class="h1-title-page">Мои подписки</h1>
                <div class="subscriptions-container">
                    <?php if (empty($subscriptions)): ?>
                        <p class="no-orders">У вас пока нет подписок.</p>
                    <?php else: ?>
                        <?php foreach ($subscriptions as $subscription): ?>
                            <div class="subscription">
                                <div class="subscription-header">
                                    <h3>Подписка на <?= htmlspecialchars($subscription['pet_name']) ?></h3>
                                    <p>Начало: <?= date('d.m.Y', strtotime($subscription['start_date'])) ?></p>
                                    <p>Окончание: <?= $subscription['end_date'] ? date('d.m.Y', strtotime($subscription['end_date'])) : 'Не указано' ?></p>
                                    <p>Статус: <span class="status-<?= $subscription['status'] ?>">
                                        <?= $subscription['status'] === 'active' ? 'Активна' : 'Неактивна' ?>
                                    </span></p>
                                </div>
                                <p><strong>Сумма:</strong> <?= number_format($subscription['amount'], 2) ?> руб.</p>
                                <?php if ($subscription['status'] === 'active'): ?>
                                    <form action="/webproject/server/subscribe/cancel_subscription.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="subscription_id" value="<?= $subscription['id'] ?>">
                                        <button type="submit" class="cancel-button">Отменить подписку</button>
                                    </form>
                                <?php else: ?>
                                    <form action="/webproject/server/subscribe/delete_subscription.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="subscription_id" value="<?= $subscription['id'] ?>">
                                        <button type="submit" class="delete-button">Удалить</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <footer>
        <div class="footer">
            <div class="left-section">
                <div class="email">ismagilovmarsel2005@gmail.com</div>
                <br>
                <div class="phone">+7 (996) 107-32-60</div>
            </div>
            <div class="right-section">
                <a href="https://vk.com/wiuwhh" class="vk" target="_blank">
                    <img src="images/vk.png" alt="Вконтакте">
                    <div>Вконтакте</div>
                </a>
                <a href="https://t.me/Qlsksk" class="tg" target="_blank">
                    <img src="images/telegram.png" alt="Телеграм">
                    <div>Телеграм</div>
                </a>
            </div>
            <div class="footer-text">© 2025 ЛАПКА - Помогаем животным вместе!</div>
        </div>
    </footer>

    <!-- Модалка для подтверждения выхода -->
    <div class="modal" id="logout-modal">
        <div class="modal-content">
            <p>Вы уверены, что хотите выйти из аккаунта?</p>
            <button id="confirm-logout">Да</button>
            <button id="cancel-logout">Нет</button>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>