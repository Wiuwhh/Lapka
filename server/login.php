<?php
session_start();

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webproject";
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

// Проверка подключения
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']));
}

// Установка кодировки
$conn->set_charset("utf8mb4");

// Получение данных из формы
$username = $_POST['username'];
$password = $_POST['password'];

// Запрос на получение данных пользователя
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        $_SESSION['user'] = $row;
        echo json_encode(['success' => true]); // Успешная авторизация
    } else {
        echo json_encode(['success' => false, 'message' => 'Неверное имя или пароль']); // Ошибка пароля
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверное имя или пароль']); // Пользователь не найден
}

$conn->close();
?>
