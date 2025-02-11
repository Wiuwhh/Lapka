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

// Получаем выбранную категорию
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;

// Запрос на получение животных с учетом фильтра
$sql = "SELECT p.id, p.name, p.breed, p.age, p.description, p.photo, c.name as category_name 
        FROM pets p 
        JOIN pet_categories c ON p.category_id = c.id";

// Фильтр по категории
if ($category_id) {
    $sql .= " WHERE p.category_id = $category_id";
}

$result = $conn->query($sql);

// Запрос на получение категорий для фильтра
$sql_categories = "SELECT id, name FROM pet_categories";
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
    <title>ЛАПКА | НАШИ ЖИВОТНЫЕ</title>
    <style>
        /* Стили для карточек животных */
        .pets-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }

        .pet-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .pet-card img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .pet-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .pet-card p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        .pet-card .category {
            font-size: 1rem;
            font-weight: bold;
            color: #9F8B70;
            margin-bottom: 15px;
        }

        /* Стили для фильтров */
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
            <a href="shop.php" class="nav-link">Магазин</a>
            <a href="#" class="nav-use">Наши животные</a>
        </nav>
        <main class="main-last-page">
            <div class="content">
                <h1 class="h1-title-page">Наши животные:</h1>

                <!-- Фильтр по категориям -->
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

                        <button type="submit">Применить</button>
                    </form>
                </div>

                <!-- Список животных -->
                <div class="pets-grid">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
                            <div class="pet-card">
                                <img src="' . $row['photo'] . '" alt="' . $row['name'] . '">
                                <div class="name-age">
                                    <h3>' . $row['name'] . ', ' . $row['age'] . '</h3>
                                </div>
                                <p>' . 'Порода: ' . $row['breed'] . '</p>
                                <p>' . $row['description'] . '</p>
                                <div class="category">' . $row['category_name'] . '</div>
                            </div>';
                        }
                    } else {
                        echo '<p>Животные отсутствуют.</p>';
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
