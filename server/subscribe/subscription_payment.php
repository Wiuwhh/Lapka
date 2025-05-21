<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Проверяем, есть ли ID животного в запросе
if (!isset($_GET['pet_id'])) {
    header('Location: /pets.php');
    exit;
}

$pet_id = intval($_GET['pet_id']);

// Получаем данные о животном
$sql = "SELECT id, name FROM pets WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Животное не найдено.");
}

$pet = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Оплата подписки</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .payment-container h2 {
            margin-bottom: 20px;
        }
        .payment-container p {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .payment-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .payment-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .payment-container button:hover {
            background-color: #218838;
        }
        #card-number,
    #card-holder,
    #expiry-date,
    #cvv {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
        font-size: 16px;
        font-family: Arial, sans-serif;
        background-color: #fff;
        color: #333;
        box-sizing: border-box;
        -webkit-appearance: none;
        appearance: none;
        resize: none;
        outline: none;
        transition: none;
        box-shadow: none;
    }

    /* Отключаем все состояния */
    #card-number:hover,
    #card-holder:hover,
    #expiry-date:hover,
    #cvv:hover,
    #card-number:focus,
    #card-holder:focus,
    #expiry-date:focus,
    #cvv:focus,
    #card-number:active,
    #card-holder:active,
    #expiry-date:active,
    #cvv:active {
        background-color: #fff !important;
        box-shadow: none !important;
        outline: none !important;
    }

    /* Специфичные стили для отдельных полей */
    #expiry-date {
        letter-spacing: 1px;
    }

    #cvv {
        width: 80px;
    }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Оформление подписки на <?= htmlspecialchars($pet['name']) ?></h2>
        <p>Стоимость подписки: <strong>99 рублей</strong></p>
        <form id="payment-form" action="process_subscription_payment.php" method="POST">
            <input type="hidden" name="pet_id" value="<?= $pet_id ?>">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <input type="hidden" name="amount" value="99">

            <label for="card-number">Номер карты</label>
            <input type="text" id="card-number" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required>

            <label for="card-holder">Имя владельца</label>
            <input type="text" id="card-holder" name="card_holder" placeholder="IVAN IVANOV" required>

            <label for="expiry-date">Срок действия</label>
            <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" maxlength="5" required>

            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>

            <button type="submit">Оформить подписку</button>
        </form>
    </div>
    <script>
        // Форматирование номера карты
        document.getElementById('card-number').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            if (value.length > 16) {
                value = value.slice(0, 16); // Ограничиваем до 16 цифр
            }
            let formattedValue = value.match(/.{1,4}/g);
            if (formattedValue !== null) {
                e.target.value = formattedValue.join(' ');
            }
        });

        // Форматирование срока действия
        document.getElementById('expiry-date').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 4) {
                value = value.slice(0, 4); // Ограничиваем до 4 цифр
            }
            if (value.length > 2) {
                e.target.value = value.slice(0, 2) + '/' + value.slice(2, 4);
            } else {
                e.target.value = value;
            }
        });

        // Ограничение для CVV
        document.getElementById('cvv').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 3) {
                value = value.slice(0, 3); // Ограничиваем до 3 цифр
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
