<?php
session_start();

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.html"); // Перенаправление на страницу входа
    exit;
}

// Подключение к базе данных
require_once '../db_connection.php';

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category_id = $_POST['category_id'];
    $photo_url = $_POST['photo_url']; // Получаем ссылку на фото

    // Запрос на добавление товара
    $stmt = $conn->prepare("INSERT INTO shop_products (name, description, price, stock_quantity, photo_path, category_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $description, $price, $stock_quantity, $photo_url, $category_id);

    if ($stmt->execute()) {
        // Успешное добавление
        echo "<script>
                alert('Товар успешно добавлен!');
                window.location.href = 'manage_products.php';
              </script>";
        exit;
    } else {
        $error_message = "Ошибка при добавлении товара: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить товар</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Rubik+Spray+Paint&display=swap" rel="stylesheet">
    <style>
        .admin-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .add-form {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .add-form input, .add-form select, .add-form textarea {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .add-form button {
            padding: 10px 20px;
            background-color: #9F8B70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            border: 3px solid #9F8B70;
            border-radius: 30px;
            text-decoration: none;
            color: #fff;
            background-color: #9F8B70;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #786C5F;
            border-color: #786C5F;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="../../index.html" class="logo-button">
                <h1 class="logo">ЛАПКА <span>| Приют для животных</span></h1>
            </a>
            <a href="manage_products.php" class="back-button">Назад</a>
        </div>
    </header>

    <div class="admin-container">
        <h1>Добавить товар</h1>

        <!-- Сообщение об ошибке -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Форма для добавления товара -->
        <div class="add-form">
            <form action="add_product.php" method="POST">
                <input type="text" name="name" placeholder="Название товара" required>
                <textarea name="description" placeholder="Описание" required></textarea>
                <input type="number" name="price" placeholder="Цена" step="0.01" required>
                <input type="number" name="stock_quantity" placeholder="Количество" required>
                <input type="text" name="photo_url" placeholder="Ссылка на фото" required>
                <select name="category_id" required>
                    <option value="">Выберите категорию</option>
                    <?php
                    // Подключение к базе данных
                    $conn = new mysqli($servername, $username, $password, $dbname, 3307);

                    // Проверка подключения
                    if ($conn->connect_error) {
                        die("Ошибка подключения: " . $conn->connect_error);
                    }

                    // Запрос на получение категорий
                    $sql = "SELECT id, name FROM product_categories";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                    }

                    $conn->close();
                    ?>
                </select>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</body>
</html>