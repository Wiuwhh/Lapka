<?php
// Подключение к базе данных
require_once '../server/db_connection.php';

// Получаем ID животного из URL
$pet_id = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : null;

// Проверяем, существует ли животное с таким ID
if ($pet_id) {
    $sql = "SELECT id, name, photo FROM pets WHERE id = $pet_id";
    $result = $conn->query($sql);
    $pet = $result->fetch_assoc();
} else {
    die("Животное не найдено.");
}

// Обработка формы подписки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = 1; // Здесь нужно получить ID текущего пользователя (например, из сессии)
    $amount = floatval($_POST['amount']);

    // Сохраняем подписку в базу данных
    $sql = "INSERT INTO subscriptions (pet_id, user_id, amount, start_date) VALUES (?, ?, ?, CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iid", $pet_id, $user_id, $amount);
    $stmt->execute();

    // Перенаправляем пользователя на страницу успешной оплаты
    header('Location: payment.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление подписки</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .pet-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .pet-info img {
            max-width: 100%;
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #9F8B70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #786C5F;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Оформление подписки</h1>
        <div class="pet-info">
            <img src="<?= $pet['photo'] ?>" alt="<?= $pet['name'] ?>">
            <h2><?= $pet['name'] ?></h2>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label for="amount">Сумма ежемесячного платежа:</label>
                <input type="number" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <button type="submit">Оформить подписку</button>
            </div>
        </form>
    </div>
</body>
</html>