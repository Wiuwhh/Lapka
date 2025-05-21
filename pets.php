<?php
session_start();
require_once 'server/db_connection.php';

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
            cursor: pointer; /* Добавляем курсор-указатель */
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

        /* Стили для модального окна */
        .modalpet {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modalpet-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 70%;
            max-width: 800px;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }

        .modal-body {
            display: flex;
            gap: 20px;
        }

        .modal-image {
            flex: 1;
        }

        .modal-image img {
            max-width: 100%;
            border-radius: 10px;
        }

        .modal-text {
            flex: 2;
            text-align: left;
        }

        .modal-text h2 {
            margin-top: 0;
            font-size: 1.5rem;
            color: #333;
        }

        .modal-text p {
            font-size: 1rem;
            color: #666;
            margin: 10px 0;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Стили для кнопки подписки */
        .subscribe-button {
        background-color: #9F8B70;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        margin-top: 20px;
        }

        .subscribe-button:hover {
            background-color: #786C5F;
        }

        /* Стили для плавающей кнопки подписок */
        #floating-subscriptions-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: rgb(255, 255, 255);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
            z-index: 1000;
            text-decoration: none;
        }

        #floating-subscriptions-button:hover {
            background-color: #786C5F; /* Цвет при наведении */
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
                <a href="#" id="account-icon" style="display: none;" class="a"><span id="user-fio" style="display: none;"></span> 👤</a>
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
                            <div class="pet-card" data-pet=\'' . json_encode($row) . '\'>
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

    <!-- Плавающая кнопка для отслеживания подписок -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="my_subscriptions.php" id="floating-subscriptions-button" class="floating-cart-button">📋</a>
    <?php endif; ?>

    <!-- Модальное окно для деталей питомца -->
    <div id="petModal" class="modalpet" style="display: none;">
        <div class="modalpet-content">
            <span class="close">&times;</span>
            <div class="modal-body">
                <div class="modal-image">
                    <img id="modalPetImage" src="" alt="Фото питомца">
                </div>
                <div class="modal-text">
                    <h2 id="modalPetName"></h2>
                    <p id="modalPetBreed"></p>
                    <p id="modalPetAge"></p>
                    <p id="modalPetDescription"></p>
                    <p id="modalPetCategory"></p>
                    <!-- Кнопка для оформления подписки -->
                    <?php if (isset($_SESSION['user_id'])): ?> 
                        <button id="subscribeButton" class="subscribe-button">Оформить подписку (99 руб/мес)</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Модалка для подтверждения выхода -->
    <div class="modal" id="logout-modal" style="display: none;">
        <div class="modal-content">
            <p>Вы уверены, что хотите выйти из аккаунта?</p>
            <button id="confirm-logout">Да</button>
            <button id="cancel-logout">Нет</button>
        </div>
    </div>

    <script>
        // Функция для открытия модального окна с деталями питомца
        function openPetModal(pet) {
            document.getElementById('modalPetImage').src = pet.photo;
            document.getElementById('modalPetName').innerText = pet.name;
            document.getElementById('modalPetBreed').innerText = `Порода: ${pet.breed}`;
            document.getElementById('modalPetAge').innerText = `Возраст: ${pet.age}`;
            document.getElementById('modalPetDescription').innerText = pet.description;
            document.getElementById('modalPetCategory').innerText = `Категория: ${pet.category_name}`;
            document.getElementById('petModal').style.display = 'block';

            // Добавляем обработчик для кнопки подписки
            document.getElementById('subscribeButton').onclick = function() {
                window.location.href = `server/subscribe/subscription_payment.php?pet_id=${pet.id}`;
            };
        }

        // Функция для закрытия модального окна
        function closePetModal() {
            document.getElementById('petModal').style.display = 'none';
        }

        // Закрытие модального окна при клике на крестик
        document.querySelector('#petModal .close').addEventListener('click', closePetModal);

        // Закрытие модального окна при клике вне его
        window.addEventListener('click', function(event) {
            if (event.target == document.getElementById('petModal')) {
                closePetModal();
            }
        });

        // Обработчик клика на карточку питомца
        document.querySelectorAll('.pet-card').forEach(card => {
            card.addEventListener('click', function() {
                const petData = this.getAttribute('data-pet');
                if (petData) {
                    const pet = JSON.parse(petData);
                    openPetModal(pet);
                }
            });
        });
    </script>

    <script src="js/auth.js"></script>
</body>
</html>