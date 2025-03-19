<?php

// Проверка прав администратора
include '../check_admin.php'; 


// Подключение к базе данных
require_once '../db_connection.php';

// Получение ID пользователя
$userId = $_GET['id'];

// Удаление пользователя
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при удалении пользователя']);
}

$stmt->close();
$conn->close();
?>