<?php
session_start();

header('Content-Type: application/json'); // Указываем тип содержимого

if (isset($_SESSION['user'])) {
    echo json_encode([
        'authenticated' => true,
        'username' => $_SESSION['user']['username'],
        'role' => $_SESSION['user']['role'] ?? 'user', // Пример дополнительных данных
    ]);
} else {
    echo json_encode(['authenticated' => false]);
}

exit();