<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные из формы
$card_number = $_POST['card_number'];
$card_holder = $_POST['card_holder'];
$expiry_date = $_POST['expiry_date'];
$cvv = $_POST['cvv'];

// Получаем товары из корзины
$sql = "SELECT c.product_id, c.quantity, p.price 
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

if (empty($cart_items)) {
    header('Location: /cart.php');
    exit;
}

// Создаем заказ
$conn->begin_transaction();
try {
    // Добавляем заказ
    $order_sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("id", $user_id, $total_amount);
    $order_stmt->execute();
    $order_id = $conn->insert_id;

    // Добавляем товары в заказ
    foreach ($cart_items as $item) {
        $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $order_item_stmt = $conn->prepare($order_item_sql);
        $order_item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $order_item_stmt->execute();
    }

    // Очищаем корзину
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_sql);
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();

    $conn->commit();

    // Выводим сообщение об успешной оплате
    echo "<!DOCTYPE html>
    <html lang='ru'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Оплата успешна</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .success-message {
                background-color: #fff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            .success-message h2 {
                color: #28a745;
            }
        </style>
    </head>
    <body>
        <div class='success-message'>
            <h2>Оплата прошла успешно!</h2>
            <p>Вы будете перенаправлены на страницу заказов через 5 секунд...</p>
        </div>
        <script>
            setTimeout(function() {
                window.location.href = 'order.php'; // Исправленный путь
            }, 5000);
        </script>
    </body>
    </html>";
    exit;
} catch (Exception $e) {
    $conn->rollback();
   exit;
}