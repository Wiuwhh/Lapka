<?php
// Подключение к базе данных
require_once 'server/db_connection.php';

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
            cursor: pointer;
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

        /* Стили для модального окна */
        .modalprod {
            display: none; /* Скрыто по умолчанию */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Полупрозрачный фон */
        }

        .modalprod-content {
            background-color: #fff;
            margin: auto; /* Центрирование по горизонтали */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 70%; /* Ширина модального окна */
            max-width: 800px; /* Максимальная ширина */
            position: relative;
            top: 50%; /* Сдвигаем на 50% вниз */
            transform: translateY(-50%); /* Возвращаем на половину высоты вверх */
        }

        .modal-body {
            display: flex; /* Используем flexbox для расположения фото и текста */
            gap: 20px; /* Расстояние между фото и текстом */
        }

        .modal-image {
            flex: 1; /* Фото занимает 1 часть */
        }

        .modal-image img {
            max-width: 100%;
            border-radius: 10px;
        }

        .modal-text {
            flex: 2; /* Текст занимает 2 части */
            text-align: left; /* Выравнивание текста по левому краю */
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

        .modal-text button {
            padding: 10px 20px;
            background-color: #9F8B70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-text button:hover {
            background-color: #786C5F;
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

        @media (max-width: 768px) {
        .modalprod-content {
            width: 90%; /* Увеличиваем ширину на мобильных устройствах */
            max-width: none; /* Убираем ограничение по ширине */
        }

        .modal-body {
            flex-direction: column; /* Располагаем фото и текст вертикально */
        }

        .modal-image {
            text-align: center; /* Центрируем фото */
        }

        .modal-image img {
            max-width: 80%; /* Уменьшаем размер фото */
        }

        .modal-text {
            text-align: center; /* Центрируем текст */
        }
    }

    /* Стили для плавающей кнопки корзины */
    #floating-cart-button {
        position: fixed;
        bottom: 20px; /* Кнопка корзины остается внизу */
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

    /* Стили для плавающей кнопки "Мои заказы" */
    #floating-orders-button {
        position: fixed;
        bottom: 90px; /* Сдвигаем кнопку "Мои заказы" выше */
        right: 20px; /* Та же позиция по горизонтали */
        background-color:rgb(255, 255, 255); /* Цвет кнопки */
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

    /* Общие стили для обеих кнопок */
    #floating-cart-button:hover,
    #floating-orders-button:hover {
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
                            <div class="product-card" data-product=\'' . json_encode($row) . '\'>
                                <img src="' . $row['photo_path'] . '" alt="' . $row['name'] . '">
                                <h3>' . $row['name'] . '</h3>
                                <div class="price">' . number_format($row['price'], 2) . ' руб.</div>
                                <p style="display: none;">' . $row['category_name'] . '</p>
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
    
    <!-- Модальное окно для деталей товара -->
    <div id="productModal" class="modalprod" style="display: none;">
        <div class="modalprod-content">
            <span class="close">&times;</span>
            <div class="modal-body">
                <div class="modal-image">
                    <img id="modalImage" src="" alt="Фото товара">
                </div>
                <div class="modal-text">
                    <h2 id="modalTitle"></h2>
                    <p id="modalDescription"></p>
                    <p id="modalPrice"></p>
                    <p id="modalCategory"></p>
                    <button onclick="addToCart(currentProduct)">Добавить в корзину</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Плавающая кнопка корзины -->
    <a href="cart.php" id="floating-cart-button" class="floating-cart-button">🛒</a>

    <!-- Плавающая кнопка заказов -->
    <a href="orders.php" id="floating-orders-button" class="floating-cart-button">📦</a>




    <script src="js/auth.js"></script>

    <script>
    let currentProduct = null; // Глобальная переменная для хранения данных о товаре

    // Функция для обновления счетчика товаров в корзине
    function updateCartCount() {
        fetch('server/get_cart_count.php', {
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count').textContent = data.count;
            } else {
                console.error('Ошибка при обновлении счетчика:', data.message);
            }
        })
        .catch(error => console.error('Ошибка:', error));
    }

    // Обработчик для плавающей кнопки корзины
    document.getElementById('floating-cart-button').addEventListener('click', function() {
        window.location.href = 'cart.php'; // Перенаправляем на страницу корзины
    });

    // Обновляем счетчик при загрузке страницы
    document.addEventListener('DOMContentLoaded', updateCartCount);

    // Функция для открытия модального окна с деталями товара
    function openModal(product) {
        currentProduct = product; // Сохраняем данные о товаре
        document.getElementById('modalImage').src = product.photo_path;
        document.getElementById('modalTitle').innerText = product.name;
        document.getElementById('modalDescription').innerText = product.description;
        document.getElementById('modalPrice').innerText = `Цена: ${parseFloat(product.price).toFixed(2)} руб.`;
        document.getElementById('modalCategory').innerText = `Категория: ${product.category_name}`;
        document.getElementById('productModal').style.display = 'block';
    }

    // Функция для закрытия модального окна товара
    function closeModal() {
        document.getElementById('productModal').style.display = 'none';
    }

    // Закрытие модального окна при клике на крестик
    document.querySelector('#productModal .close').addEventListener('click', closeModal);

    // Закрытие модального окна при клике вне его
    window.addEventListener('click', function(event) {
        if (event.target == document.getElementById('productModal')) {
            closeModal();
        }
    });

    // Обработчик клика на карточку товара
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productData = this.getAttribute('data-product');
            if (productData) {
                const product = JSON.parse(productData);
                openModal(product);
            }
        });
    });

    // Функция для добавления товара в корзину
    function addToCart(product) {
        if (!product) {
            alert('Ошибка: данные о товаре отсутствуют.');
            return;
        }

        fetch('server/cart/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: product.id, // ID товара
                quantity: 1 // По умолчанию добавляем 1 единицу товара
            }),
            credentials: 'include' // Передаем cookies для авторизации
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Товар добавлен в корзину!');
                closeModal(); // Закрываем модальное окно товара
                updateCartCount(); // Обновляем счетчик товаров в корзине
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => console.error('Ошибка:', error));
    }
</script>

</body>
</html>