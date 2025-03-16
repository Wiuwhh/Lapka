<?php
session_start();
require_once 'server/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.photo_path, c.quantity 
        FROM cart c 
        JOIN shop_products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_amount = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_amount += $row['price'] * $row['quantity'];
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
    <title>ЛАПКА | МАГАЗИН</title>
    <style>
        .cart-items { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .cart-item { display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; }
        .cart-item img { max-width: 50px; border-radius: 5px; }
        .cart-item h3 { margin: 0; font-size: 1rem; }
        .cart-item p { margin: 0; font-size: 0.9rem; }
        .cart-item button { background-color: #9F8B70; color: white; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer; }
        .cart-item button:hover { background-color: #786C5F; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; }
        .quantity-controls button { background-color: #9F8B70; color: white; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer; }
        .quantity-controls button:hover { background-color: #786C5F; }
        .cart-total { text-align: right; font-size: 1.2rem; font-weight: bold; margin-top: 20px; }
        .checkout-button { width: 100%; padding: 10px; background-color: #9F8B70; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; margin-top: 20px; }
        .checkout-button:hover { background-color: #786C5F; }
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
                <h1 class="h1-title-page">Корзина</h1>
                <div class="cart-items">
                    <?php if (empty($cart_items)): ?>
                        <p>Ваша корзина пуста.</p>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <img src="<?= $item['photo_path'] ?>" alt="<?= $item['name'] ?>">
                                <h3><?= $item['name'] ?></h3>
                                <p>Цена: <?= number_format($item['price'], 2) ?> руб.</p>
                                <div class="quantity-controls">
                                    <button onclick="updateCartItemQuantity(<?= $item['cart_id'] ?>, 'decrease')">-</button>
                                    <span data-cart-id="<?= $item['cart_id'] ?>"><?= $item['quantity'] ?></span>
                                    <button onclick="updateCartItemQuantity(<?= $item['cart_id'] ?>, 'increase')">+</button>
                                </div>
                                <button onclick="removeFromCart(<?= $item['cart_id'] ?>)">Удалить</button>
                            </div>
                        <?php endforeach; ?>
                        <div class="cart-total">
                            <p>Общая сумма: <?= number_format($total_amount, 2) ?> руб.</p>
                        </div>
                        <button id="checkout-button" class="checkout-button">Оформить заказ</button>
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

    <script>
        function removeFromCart(cartId) {
            fetch('server/cart/remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cart_id: cartId }),
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Товар удален из корзины.');
                    location.reload();
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => console.error('Ошибка:', error));
        }

        function updateCartItemQuantity(cartId, action) {
            fetch('server/cart/update_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_id: cartId,
                    action: action
                }),
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const quantityElement = document.querySelector(`.quantity-controls span[data-cart-id="${cartId}"]`);
                    if (quantityElement) {
                        quantityElement.textContent = data.quantity;
                    }
                    const totalAmountElement = document.querySelector('.cart-total p');
                    if (totalAmountElement) {
                        totalAmountElement.textContent = `Общая сумма: ${data.total_amount} руб.`;
                    }
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => console.error('Ошибка:', error));
        }
    </script>
</body>
</html>