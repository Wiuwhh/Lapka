<!-- страница входа -->
<?php
session_start();
require_once "../config.php"; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    // Проверяем, есть ли такой email и является ли он админом
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user["password"])) {
        session_start();
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['role'] = $user['role']; 
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Неверный email, пароль или недостаточно прав!";
    }
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            background-color: #f4f4f4; 
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #fff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); 
            width: 300px;
            text-align: left; /* Выравнивание текста внутри контейнера по левому краю */
        }
        h2 {
            text-align: center; /* Выравнивание заголовка по центру */
            margin-bottom: 15px;
            color: #333;
        }
        input {
            width: 100%; 
            padding: 10px; 
            margin: 8px 0; 
            border: 1px solid #ddd; 
            border-radius: 5px;
        }
        button {
            width: 100%; 
            padding: 10px; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            margin-top: 10px;
            text-align: center; /* Выравнивание сообщения об ошибке по центру */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Админ-панель - Вход</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Пароль:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>