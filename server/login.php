<?php
session_start();

// Подключение к базе данных
require_once 'db_connection.php';

// Установка кодировки
$conn->set_charset("utf8mb4");

// Получение данных из формы
$username = $_POST['username'];
$password = $_POST['password'];

// Запрос на получение данных пользователя
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Сохраняем данные пользователя в сессии
        $_SESSION['user'] = $row;

        // Проверяем роль пользователя
        if ($row['role'] === 'admin') {
            echo json_encode(['success' => true, 'redirect' => 'admin_panel.html']); // Перенаправляем на админ-панель
        } else {
            echo json_encode(['success' => true, 'redirect' => 'index.html']); // Перенаправляем на главную страницу
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Неверное имя или пароль']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверное имя или пароль']);
}

$conn->close();
?>