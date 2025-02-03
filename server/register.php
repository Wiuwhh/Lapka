<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webproject";
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Установка кодировки
$conn->set_charset("utf8mb4");

// Получение данных из формы
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Запрос на вставку данных
$stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $phone, $password);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Регистрация успешна!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при регистрации.']);
}

$stmt->close();
$conn->close();
?>
