<?php
session_start();

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Доступ запрещён']));
}

// Подключение к базе данных
require_once '../db_connection.php';

// Получение ID товара
$productId = $_GET['id'];

// Удаление товара
$stmt = $conn->prepare("DELETE FROM shop_products WHERE id = ?");
$stmt->bind_param("i", $productId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при удалении товара']);
}

$stmt->close();
$conn->close();
?>