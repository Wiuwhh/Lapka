<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webproject";
$port = 3307;

// Создаем соединение с базой данных
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Проверяем подключение
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4");
?>