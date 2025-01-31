<?php
session_start();

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user'])) {
    echo json_encode([
        'authenticated' => true,
        'username' => $_SESSION['user']['username'], // Передаем имя пользователя
    ]);
} else {
    echo json_encode(['authenticated' => false]);
}
exit();

