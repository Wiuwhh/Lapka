<?php
session_start();

// Уничтожаем сессию
session_destroy();

echo json_encode(['success' => true]);
exit();