<?php

// Проверка прав администратора
include '../check_admin.php'; 


// Подключение к базе данных
require_once '../db_connection.php';

// Получение ID животного
$petId = $_GET['id'];

// Удаление животного
$stmt = $conn->prepare("DELETE FROM pets WHERE id = ?");
$stmt->bind_param("i", $petId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при удалении животного']);
}

$stmt->close();
$conn->close();
?>