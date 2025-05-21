<?php
session_start(); // Запускаем сессию
require_once 'db_connection.php'; // Подключаем файл с подключением к базе данных

// Логирование начала выполнения скрипта
error_log("Запуск auth_status.php. Сессия: " . print_r($_SESSION, true));

// Базовая структура ответа
$response = [
    'authenticated' => false,
    'username' => '',
    'role' => '',
    'session' => $_SESSION, // Добавляем данные сессии для отладки
    'error' => '' // Поле для ошибок
];

try {
    if (isset($_SESSION['user_id'])) {
        // Если пользователь авторизован, получаем его данные из базы
        $user_id = $_SESSION['user_id'];
        error_log("ID пользователя из сессии: " . $user_id);

        // Подготавливаем SQL-запрос
        $sql = "SELECT username, role FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $response['authenticated'] = true;
            $response['username'] = $user['username']; // ФИО пользователя
            $response['role'] = $user['role']; // Роль пользователя
            $response['user_id'] = $user_id; // Добавляем user_id в ответ
            error_log("Данные пользователя получены: " . print_r($user, true));
        } else {
            error_log("Пользователь с ID $user_id не найден в базе данных.");
            $response['error'] = "Пользователь не найден в базе данных.";
        }

        $stmt->close();
    } else {
        error_log("Сессия не содержит user_id. Пользователь не авторизован.");
        $response['error'] = "Пользователь не авторизован.";
    }
} catch (Exception $e) {
    error_log("Ошибка в auth_status.php: " . $e->getMessage());
    $response['error'] = $e->getMessage();
} finally {
    // Закрываем соединение с базой данных
    if (isset($conn)) {
        $conn->close();
    }
}

// Возвращаем ответ в формате JSON
header('Content-Type: application/json');
echo json_encode($response);

// Логирование окончания выполнения скрипта
error_log("Завершение auth_status.php. Ответ: " . print_r($response, true));
?>