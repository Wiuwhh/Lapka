<?php
session_start();

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Доступ запрещён']));
}

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