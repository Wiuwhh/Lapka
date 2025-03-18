<?php
session_start(); // Инициализация сессии
header('Content-Type: application/json'); // Указываем, что возвращаем JSON

// Логирование информации о сессии
error_log("Сессия в add_suggestion.php: " . print_r($_SESSION, true));

// Подключение к базе данных
require_once 'db_connection.php';

// Установка кодировки
$conn->set_charset("utf8mb4");

// Получение user_id из POST-запроса
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

// Проверка, авторизован ли пользователь
if (!$user_id) {
    error_log("Ошибка: user_id не передан.");
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован', 'session' => $_SESSION]);
    exit();
}

// Получение данных из формы
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Проверка на пустые данные
if (empty($title) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Заголовок и описание не могут быть пустыми']);
    exit();
}

// Вставка данных в таблицу user_suggestions
$stmt = $conn->prepare("INSERT INTO user_suggestions (user_id, title, description, status) VALUES (?, ?, ?, 'new')");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса: ' . $conn->error]);
    exit();
}

$stmt->bind_param("iss", $user_id, $title, $description);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Пожелание успешно отправлено!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при отправке пожелания: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>