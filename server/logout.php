<?php
session_start(); // Запускаем сессию
session_destroy(); // Уничтожаем сессию

$response = ['success' => true];

// Возвращаем ответ в формате JSON
header('Content-Type: application/json');
echo json_encode($response);
?>