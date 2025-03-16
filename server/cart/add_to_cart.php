<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Пользователь не авторизован.';
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = intval($data['product_id']);
$quantity = intval($data['quantity']);
$user_id = $_SESSION['user_id'];

// Проверяем, есть ли товар уже в корзине
$sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Если товар уже в корзине, обновляем количество
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $new_quantity, $row['id']);
    $update_stmt->execute();
    $response['success'] = true;
    $response['message'] = 'Количество товара обновлено.';
} else {
    // Если товара нет в корзине, добавляем его
    $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $insert_stmt->execute();
    $response['success'] = true;
    $response['message'] = 'Товар добавлен в корзину.';
}

echo json_encode($response);
?>