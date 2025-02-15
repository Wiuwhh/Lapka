<?php
session_start(); // Запускаем сессию
require_once 'db_connection.php'; // Подключаем файл с подключением к базе данных

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Подготавливаем SQL-запрос
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Устанавливаем сессию
            $_SESSION['user_id'] = $user['id'];
            $response['success'] = true;
            $response['message'] = 'Авторизация успешна!';
        } else {
            $response['message'] = 'Неверный пароль.';
        }
    } else {
        $response['message'] = 'Пользователь не найден.';
    }
}

// Возвращаем ответ в формате JSON
header('Content-Type: application/json');
echo json_encode($response);
?>