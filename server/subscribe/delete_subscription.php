<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['subscription_id'])) {
    $subscription_id = intval($_POST['subscription_id']);

    $sql = "DELETE FROM subscriptions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $subscription_id);
    
    if ($stmt->execute()) {
        header('Location: /webproject/my_subscriptions.php'); // Обновляем страницу после удаления
        exit;
    } else {
        echo "Ошибка при удалении подписки.";
    }

    $stmt->close();
}

$conn->close();
?>
