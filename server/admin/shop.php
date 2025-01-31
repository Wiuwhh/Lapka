<!-- управление товарами -->

<?php
session_start();
require_once '../config.php';

// Проверяем, авторизован ли админ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Получаем список товаров
$products = $pdo->query("SELECT * FROM shop_products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Удаление товара
if (isset($_GET['delete'])) {
    $productId = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM shop_products WHERE id = ?")->execute([$productId]);
    header("Location: shop.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление товарами</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            background-color: #f4f4f4; 
        }
        table { 
            width: 90%; 
            margin: 20px auto; 
            border-collapse: collapse; 
            background: #fff; 
        }
        th, td { 
            padding: 10px; 
            border: 1px solid #ddd; 
            text-align: left; 
        }
        th { 
            background: #007bff; 
            color: #fff; 
        }
        a { 
            text-decoration: none; 
            color: #007bff; 
        }
        .add-btn { 
            display: inline-block; 
            margin: 10px; 
            padding: 10px 20px; 
            background: #28a745; 
            color: white; 
            border-radius: 5px; 
        }
    </style>
</head>
<body>

    <h1>Управление товарами</h1>

    <a href="add_product.php" class="add-btn">➕ Добавить товар</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Фото</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id']; ?></td>
            <td><?= htmlspecialchars($product['name']); ?></td>
            <td><?= htmlspecialchars($product['description']); ?></td>
            <td><?= htmlspecialchars($product['price']); ?> ₽</td>
            <td><?= htmlspecialchars($product['stock_quantity']); ?></td>
            <td><img src="<?= htmlspecialchars($product['photo_path']); ?>" width="50" height="50"></td>
            <td>
                <a href="edit_product.php?id=<?= $product['id']; ?>">✏️ Редактировать</a> | 
                <a href="shop.php?delete=<?= $product['id']; ?>" onclick="return confirm('Удалить товар?');">❌ Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="dashboard.php">⬅ Назад в админ-панель</a></p>

</body>
</html>
