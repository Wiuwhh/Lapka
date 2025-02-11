<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webproject";
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем выбранные параметры фильтрации и сортировки
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$sort_price = isset($_GET['sort_price']) ? $_GET['sort_price'] : null;

// Запрос на получение товаров с учетом фильтров и сортировки
$sql = "SELECT p.id, p.name, p.description, p.price, p.photo_path, c.name as category_name 
        FROM shop_products p 
        JOIN product_categories c ON p.category_id = c.id";

// Фильтр по категории
if ($category_id) {
    $sql .= " WHERE p.category_id = $category_id";
}

// Сортировка по цене
if ($sort_price === 'asc') {
    $sql .= " ORDER BY p.price ASC";
} elseif ($sort_price === 'desc') {
    $sql .= " ORDER BY p.price DESC";
}

$result = $conn->query($sql);

// Запрос на получение категорий для фильтра
$sql_categories = "SELECT id, name FROM product_categories";
$result_categories = $conn->query($sql_categories);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Rubik+Spray+Paint&display=swap" rel="stylesheet"> 
    <title>ЛАПКА | МАГАЗИН</title>
    <style>
        /* Стили для карточек товаров */
        .products-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .product-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .product-card p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        .product-card .price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #9F8B70;
            margin-bottom: 15px;
        }

        .product-card button {
            padding: 10px 20px;
            background-color: #9F8B70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .product-card button:hover {
            background-color: #786C5F;
        }

        /* Стили для фильтров и сортировки */
        .filters {
            display: flex;
            gap: 20px;
            padding: 20px;
            justify-content: center;
            border-bottom: 1px solid #ddd;
        }

        .filters select, .filters button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .filters button {
            background-color: #9F8B70;
            color: white;
            cursor: pointer;
        }

        .filters button:hover {
            background-color: #786C5F;
        }

        .text {
            text-align: center;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.html" class="logo-button">
                <h1 class="logo">ЛАПКА <span>| Приют для животных</span></h1>
            </a>
            <div class="auth-buttons">
                <a href="register.html" class="register-button" id="register-btn">Регистрация</a>
                <a href="login.html" class="login-button" id="login-btn">Вход</a>
                <a href="#" id="account-icon" style="display: none;" class="a">👤</a>
                <a href="#" style="display: none;" class="a" id="logout-btn">Выйти</a>
            </div>
        </div>
    </header>
    <div class="colorful-container">
        <nav class="nav">
            <a href="about.html" class="nav-link">О нас</a>
            <a href="donate.html" class="nav-link">Помощь</a>
            <a href="#" class="nav-use">Магазин</a>
            <a href="pets.php" class="nav-link">Наши животные</a>
        </nav>
        <main class="main">
            <div class="content">
                <h1 class="h1-title-page">Каталог:</h1>
                <div class="text">Вы можете приобрести данные товары в нашем приюте</div>

                <!-- Фильтры и сортировка -->
                <div class="filters">
                    <form method="GET" action="">
                        <select name="category" id="category">
                            <option value="">Все категории</option>
                            <?php
                            if ($result_categories->num_rows > 0) {
                                while ($row_category = $result_categories->fetch_assoc()) {
                                    $selected = ($category_id == $row_category['id']) ? 'selected' : '';
                                    echo "<option value='{$row_category['id']}' $selected>{$row_category['name']}</option>";
                                }
                            }
                            ?>
                        </select>

                        <select name="sort_price" id="sort_price">
                            <option value="">Сортировка по цене</option>
                            <option value="asc" <?php echo ($sort_price === 'asc') ? 'selected' : ''; ?>>По возрастанию</option>
                            <option value="desc" <?php echo ($sort_price === 'desc') ? 'selected' : ''; ?>>По убыванию</option>
                        </select>

                        <button type="submit">Применить</button>
                    </form>
                </div>

                <!-- Список товаров -->
                <div class="products-grid">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
                            <div class="product-card">
                                <img src="' . $row['photo_path'] . '" alt="' . $row['name'] . '">
                                <h3>' . $row['name'] . '</h3>
                                <p>' . $row['description'] . '</p>
                                <div class="price">' . number_format($row['price'], 2) . ' руб.</div>
                                <button>Добавить в корзину</button>
                            </div>';
                        }
                    } else {
                        echo '<p>Товары отсутствуют.</p>';
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>
    <footer>
        <div class="footer">
            <div class="left-section">
                <div class="email">ismagilovmarsel2005@gmail.com</div>
                <br>
                <div class="phone">+7 (996) 107-32-60</div>
            </div>
            <div class="right-section">
                <a href="https://vk.com/wiuwhh" class="vk" target="_blank">
                    <img src="images/vk.png" alt="Вконтакте">
                    <div>Вконтакте</div>
                </a>
                <a href="https://t.me/Qlsksk" class="tg" target="_blank">
                    <img src="images/telegram.png" alt="Телеграм">
                    <div>Телеграм</div>
                </a>
            </div>
            <div class="footer-text">© 2025 ЛАПКА - Помогаем животным вместе!</div>
        </div>
    </footer>

    <!-- Модалка для подтверждения выхода -->
    <div class="modal" id="logout-modal" style="display: none;">
        <div class="modal-content">
            <p>Вы уверены, что хотите выйти из аккаунта?</p>
            <button id="confirm-logout">Да</button>
            <button id="cancel-logout">Нет</button>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>



        

