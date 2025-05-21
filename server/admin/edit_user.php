<?php

// Проверка прав администратора
include '../check_admin.php'; 

// Подключение к базе данных
require_once '../db_connection.php';

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $userId = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Запрос на обновление данных пользователя
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $email, $phone, $role, $userId);

    if ($stmt->execute()) {
        // Перенаправление на страницу управления пользователями
        header("Location: manage_users.php");
        exit;
    } else {
        $error_message = "Ошибка при обновлении данных пользователя.";
    }

    $stmt->close();
}

// Получение ID пользователя из URL
$userId = $_GET['id'];

// Запрос на получение данных пользователя
$sql = "SELECT id, username, email, phone, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Пользователь не найден.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
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

        .edit-form input, .edit-form select {
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
            <a href="manage_users.php" class="back-button">Назад</a>
        </div>
    </header>

    <div class="admin-container">
        <h1>Редактирование пользователя</h1>

        <!-- Сообщение об ошибке -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Форма редактирования пользователя -->
        <div class="edit-form">
            <form action="edit_user.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="text" name="username" value="<?php echo $row['username']; ?>" placeholder="Имя пользователя" required>
                <input type="email" name="email" value="<?php echo $row['email']; ?>" placeholder="Email" required>
                <input type="text" name="phone" value="<?php echo $row['phone']; ?>" placeholder="Телефон" required>
                <select name="role">
                    <option value="user" <?php echo ($row['role'] === 'user') ? 'selected' : ''; ?>>Пользователь</option>
                    <option value="admin" <?php echo ($row['role'] === 'admin') ? 'selected' : ''; ?>>Администратор</option>
                </select>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</body>
</html>