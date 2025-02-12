<?php
session_start();

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Доступ запрещён']));
}

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