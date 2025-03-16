<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

$response = ['success' => false, 'message' => '', 'quantity' => 0, 'total_amount' => 0];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Пользователь не авторизован.';
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart_id = intval($data['cart_id']);
$action = $data['action']; // "increase" или "decrease"
$user_id = $_SESSION['user_id'];

$sql = "SELECT c.quantity, p.price 
        FROM cart c 
        JOIN shop_products p ON c.product_id = p.id 
        WHERE c.id = ? AND c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_quantity = $row['quantity'];
    $price = $row['price'];

    if ($action === 'increase') {
        $new_quantity = $current_quantity + 1;
    } elseif ($action === 'decrease') {
        $new_quantity = $current_quantity - 1;
        if ($new_quantity < 1) {
            $new_quantity = 1;
        }
    } else {
        $response['message'] = 'Неверное действие.';
        echo json_encode($response);
        exit;
    }

    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iii", $new_quantity, $cart_id, $user_id);
    $update_stmt->execute();

    $total_sql = "SELECT SUM(p.price * c.quantity) as total_amount 
                  FROM cart c 
                  JOIN shop_products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
    $total_stmt = $conn->prepare($total_sql);
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    $total_amount = $total_row['total_amount'] ?? 0;

    $response['success'] = true;
    $response['quantity'] = $new_quantity;
    $response['total_amount'] = number_format($total_amount, 2);
} else {
    $response['message'] = 'Товар не найден в корзине.';
}

echo json_encode($response);
?>