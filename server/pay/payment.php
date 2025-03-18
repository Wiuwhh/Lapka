<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

// Получаем общую сумму из корзины
$user_id = $_SESSION['user_id'];
$sql = "SELECT SUM(p.price * c.quantity) as total_amount 
        FROM cart c 
        JOIN shop_products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_amount = $row['total_amount'];

if (!$total_amount) {
    header('Location: /cart.php');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Оплата заказа</title>
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
        }
        .payment-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .payment-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
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
        .total-amount {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Оплата заказа</h2>
        <div class="total-amount">
            Сумма к оплате: <?= number_format($total_amount, 2) ?> руб.
        </div>
        <form id="payment-form" action="process_payment.php" method="POST">
            <label for="card-number">Номер карты</label>
            <input type="text" id="card-number" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required>

            <label for="card-holder">Имя владельца</label>
            <input type="text" id="card-holder" name="card_holder" placeholder="IVAN IVANOV" required>

            <label for="expiry-date">Срок действия</label>
            <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" maxlength="5" required>

            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>

            <button type="submit">Оплатить</button>
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