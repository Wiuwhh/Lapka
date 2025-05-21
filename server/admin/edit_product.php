<?php

// Проверка прав администратора
include '../check_admin.php'; 


// Подключение к базе данных
require_once '../db_connection.php';

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $productId = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category_id = $_POST['category_id'];
    $photo_url = $_POST['photo_url']; // Получаем ссылку на фото

    // Запрос на обновление данных товара
    $stmt = $conn->prepare("UPDATE shop_products SET name = ?, description = ?, price = ?, stock_quantity = ?, photo_path = ?, category_id = ? WHERE id = ?");
    $stmt->bind_param("ssdisii", $name, $description, $price, $stock_quantity, $photo_url, $category_id, $productId);

    if ($stmt->execute()) {
        header("Location: manage_products.php"); // Перенаправление на страницу управления товарами
        exit;
    } else {
        $error_message = "Ошибка при обновлении данных товара.";
    }

    $stmt->close();
}

// Получение ID товара из URL
$productId = $_GET['id'];

// Запрос на получение данных товара
$sql = "SELECT id, name, description, price, stock_quantity, photo_path, category_id FROM shop_products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Товар не найден.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование товара</title>
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

        .edit-form {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .edit-form input, .edit-form select, .edit-form textarea {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .edit-form button {
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
        <h1>Редактирование товара</h1>

        <!-- Сообщение об ошибке -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Форма редактирования товара -->
        <div class="edit-form">
            <form action="edit_product.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="text" name="name" value="<?php echo $row['name']; ?>" placeholder="Название товара" required>
                <textarea name="description" placeholder="Описание" required><?php echo $row['description']; ?></textarea>
                <input type="number" name="price" value="<?php echo $row['price']; ?>" placeholder="Цена" step="0.01" required>
                <input type="number" name="stock_quantity" value="<?php echo $row['stock_quantity']; ?>" placeholder="Количество" required>
                <input type="text" name="photo_url" value="<?php echo $row['photo_path']; ?>" placeholder="Ссылка на фото" required>
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
                        while ($row_category = $result->fetch_assoc()) {
                            $selected = ($row_category['id'] == $row['category_id']) ? 'selected' : '';
                            echo "<option value='{$row_category['id']}' $selected>{$row_category['name']}</option>";
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