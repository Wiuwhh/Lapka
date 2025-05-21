<?php
// Подключение к базе данных
require_once 'db_connection.php';

// Установка кодировки
$conn->set_charset("utf8mb4");

// Получение данных из формы
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Запрос на вставку данных
$stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $phone, $password);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Регистрация успешна!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при регистрации.']);
}

$stmt->close();
$conn->close();
?>