<?php
session_start();
require_once 'db_connection.php';
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $response['success'] = true;
                $response['message'] = 'Авторизация успешна!';
            } else {
                $response['message'] = 'Неверный пароль.';
            }
        } else {
            $response['message'] = 'Пользователь не найден.';
        }
    } else {
        $response['message'] = 'Ошибка запроса к базе данных.';
    }
} else {
    $response['message'] = 'Некорректные данные.';
}

echo json_encode($response);