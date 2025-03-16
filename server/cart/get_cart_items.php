<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

$response = ['success' => false, 'message' => '', 'items' => []];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем товары из корзины
$sql = "SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.photo_path, c.quantity 
        FROM cart c 
        JOIN shop_products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $response['items'][] = $row;
}

$response['success'] = true;
echo json_encode($response);
?>