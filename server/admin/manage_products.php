<?php
include '../check_admin.php'; // Проверка прав администратора
require_once '../db_connection.php'; // Подключение к базе данных
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление товарами</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            background-color: floralwhite;
        }

        th {
            background-color: #9F8B70;
            color: white;
        }

        .actions button {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .actions button.edit {
            background-color: #4CAF50;
            color: white;
        }

        .actions button.delete {
            background-color: #f44336;
            color: white;
        }

        .add-button {
            padding: 10px 20px;
            background-color: #9F8B70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
            <a href="../../admin_panel.html" class="back-button">Назад</a>
        </div>
    </header>

    <div class="admin-container">
        <h1>Управление товарами</h1>

        <!-- Кнопка для добавления нового товара -->
        <button class="add-button" onclick="location.href='add_product.php'">Добавить товар</button>

        <!-- Фильтр по категории -->
        <form method="GET" action="">
            <label for="category">Фильтр по категории:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">Все категории</option>
                <?php
                // Запрос на получение категорий
                $sql_categories = "SELECT id, name FROM product_categories";
                $result_categories = $conn->query($sql_categories);

                if ($result_categories->num_rows > 0) {
                    while ($row_category = $result_categories->fetch_assoc()) {
                        $selected = (isset($_GET['category']) && $_GET['category'] == $row_category['id']) ? 'selected' : '';
                        echo "<option value='{$row_category['id']}' $selected>{$row_category['name']}</option>";
                    }
                }
                ?>
            </select>
        </form>

        <!-- Таблица с товарами -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Категория</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Запрос на получение товаров с учетом фильтра
                $sql = "SELECT p.id, p.name, p.description, p.price, p.stock_quantity, c.name as category 
                        FROM shop_products p 
                        JOIN product_categories c ON p.category_id = c.id";

                // Если выбрана категория, добавляем условие WHERE
                if (isset($_GET['category']) && !empty($_GET['category'])) {
                    $category_id = intval($_GET['category']);
                    $sql .= " WHERE p.category_id = $category_id";
                }

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['description']}</td>
                                <td>{$row['price']}</td>
                                <td>{$row['stock_quantity']}</td>
                                <td>{$row['category']}</td>
                                <td class='actions'>
                                    <button class='edit' onclick='location.href=\"edit_product.php?id={$row['id']}\"'>Редактировать</button>
                                    <button class='delete' onclick='deleteProduct({$row['id']})'>Удалить</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Нет данных о товарах</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function deleteProduct(productId) {
            if (confirm("Вы уверены, что хотите удалить этот товар?")) {
                fetch(`delete_product.php?id=${productId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Товар успешно удален");
                        location.reload(); // Перезагружаем страницу
                    } else {
                        alert("Ошибка при удалении товара: " + data.message);
                    }
                })
                .catch(error => console.error('Ошибка:', error));
            }
        }
    </script>
</body>
</html>
