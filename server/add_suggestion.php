<?php
header('Content-Type: application/json'); // Указываем, что возвращаем JSON

session_start();

// Подключение к базе данных
require_once 'db_connection.php';

// Установка кодировки
$conn->set_charset("utf8mb4");

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
    exit();
}

// Получение данных из формы
$user_id = $_SESSION['user']['id']; // Используем ID из сессии
$title = isset($_POST['title']) ? $_POST['title'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';

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