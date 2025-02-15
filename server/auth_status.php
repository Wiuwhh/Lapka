<?php
session_start(); // Запускаем сессию
require_once 'db_connection.php'; // Подключаем файл с подключением к базе данных

$response = [
    'authenticated' => false,
    'username' => '',
    'role' => ''
];

if (isset($_SESSION['user_id'])) {
    // Если пользователь авторизован, получаем его данные из базы
    $user_id = $_SESSION['user_id'];

    // Подготавливаем SQL-запрос
    $sql = "SELECT username, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $response['authenticated'] = true;
        $response['username'] = $user['username']; // ФИО пользователя
        $response['role'] = $user['role']; // Роль пользователя
    }
}

// Возвращаем ответ в формате JSON
header('Content-Type: application/json');
echo json_encode($response);
?>