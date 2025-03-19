<?php
session_start();
require_once 'server/db_connection.php';

// Включение отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем список заказов пользователя
$sql = "SELECT id, total_amount, status, created_at 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
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
    <title>Мои заказы | ЛАПКА</title>
    <style>
        .orders-container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .order { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .order-header h3 { margin: 0; font-size: 1.2rem; }
        .order-header p { margin: 0; font-size: 0.9rem; color: #666; }
        .order-items { margin-top: 10px; }
        .order-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
        .order-item:last-child { border-bottom: none; }
        .order-item img { max-width: 50px; border-radius: 5px; }
        .order-item h4 { margin: 0; font-size: 1rem; }
        .order-item p { margin: 0; font-size: 0.9rem; color: #666; }
        .no-orders { text-align: center; font-size: 1.1rem; color: #666; }

        /* Стили для статусов */
        .status-оплачено { color: orange; font-weight: bold; }
        .status-получено { color: green; font-weight: bold; }
        .status-отменено { color: red; font-weight: bold; }

        /* Стили для плавающей кнопки корзины */
        #floating-cart-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: rgb(255, 255, 255);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
            z-index: 1000;
            text-decoration: none;
        }
        #floating-cart-button:hover {
            background-color: #786C5F;
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
                <h1 class="h1-title-page">Мои заказы</h1>
                <div class="orders-container">
                    <?php if (empty($orders)): ?>
                        <p class="no-orders">У вас пока нет заказов.</p>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order">
                                <div class="order-header">
                                    <h3>Заказ #<?= $order['id'] ?></h3>
                                    <p>Дата: <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                                    <p>Статус: 
                                        <span class="status-<?= $order['status'] ?>">
                                            <?= $order['status'] ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="order-items">
                                    <?php
                                    // Получаем товары из заказа
                                    $conn = new mysqli($servername, $username, $password, $dbname, $port);
                                    $sql = "SELECT oi.product_id, oi.quantity, oi.price, p.name, p.photo_path 
                                            FROM order_items oi 
                                            JOIN shop_products p ON oi.product_id = p.id 
                                            WHERE oi.order_id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $order['id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $items = [];
                                    while ($row = $result->fetch_assoc()) {
                                        $items[] = $row;
                                    }
                                    $conn->close();
                                    ?>
                                    <?php foreach ($items as $item): ?>
                                        <div class="order-item">
                                            <img src="<?= $item['photo_path'] ?>" alt="<?= $item['name'] ?>">
                                            <h4><?= $item['name'] ?></h4>
                                            <p>Количество: <?= $item['quantity'] ?></p>
                                            <p>Цена: <?= number_format($item['price'], 2) ?> руб.</p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="order-total">
                                    <p><strong>Итого: <?= number_format($order['total_amount'], 2) ?> руб.</strong></p>
                                </div>
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
        
    <!-- Плавающая кнопка корзины -->
    <a href="cart.php" id="floating-cart-button" class="floating-cart-button">🛒</a>

    <script src="js/auth.js"></script>
</body>
</html>