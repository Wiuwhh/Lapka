<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные из формы
$pet_id = intval($_POST['pet_id']);
$amount = floatval($_POST['amount']);
$card_number = $_POST['card_number'];
$card_holder = $_POST['card_holder'];
$expiry_date = $_POST['expiry_date'];
$cvv = $_POST['cvv'];

// Проверяем, существует ли животное
$sql = "SELECT id FROM pets WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Животное не найдено.");
}

// Добавляем подписку в базу
$sql = "INSERT INTO subscriptions (pet_id, user_id, amount, start_date) VALUES (?, ?, ?, CURDATE())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iid", $pet_id, $user_id, $amount);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<script>
            alert('Подписка успешно оформлена!');
            window.location.href = '/webproject/pets.php';
          </script>";
} else {
    echo "<script>
            alert('Ошибка при оформлении подписки.');
            window.history.back();
          </script>";
}

$conn->close();
?>
