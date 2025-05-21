<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Пользователь не авторизован.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

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
    $response['message'] = 'Ваша корзина пуста.';
    echo json_encode($response);
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
    $response['success'] = true;
    $response['message'] = 'Заказ успешно оформлен!';
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = 'Ошибка при оформлении заказа: ' . $e->getMessage();
}

echo json_encode($response);
?>