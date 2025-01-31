<?php
$host = '127.0.0.1';
$dbname = 'webproject';
$username = 'root';  // Обычно root в XAMPP
$password = '';  // В XAMPP пароль пустой

try {
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>
