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
$cart_id = intval($data['cart_id']);
$user_id = $_SESSION['user_id'];

// Удаляем товар из корзины
$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $response['success'] = true;
    $response['message'] = 'Товар удален из корзины.';
} else {
    $response['message'] = 'Ошибка при удалении товара.';
}

echo json_encode($response);
?>