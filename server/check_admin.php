<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user'])) {
    header('Location: /login.html'); // Перенаправление на страницу входа
    exit;
}

// Проверяем, является ли пользователь администратором
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.html'); // Перенаправление на страницу входа
    exit;
}
?>