<?php
include '../check_admin.php'; // Проверка прав администратора
require_once '../db_connection.php'; // Подключение к базе данных
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
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
        <h1>Управление пользователями</h1>

        <!-- Таблица с пользователями -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя пользователя</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Запрос на получение пользователей
                $sql = "SELECT id, username, email, phone, role FROM users";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['role']}</td>
                                <td class='actions'>
                                    <button class='edit' onclick='location.href=\"edit_user.php?id={$row['id']}\"'>Редактировать</button>
                                    <button class='delete' onclick='deleteUser({$row['id']})'>Удалить</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Нет данных о пользователях</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function deleteUser(userId) {
            if (confirm("Вы уверены, что хотите удалить этого пользователя?")) {
                fetch(`delete_user.php?id=${userId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Пользователь успешно удалён");
                        location.reload(); // Перезагружаем страницу
                    } else {
                        alert("Ошибка при удалении пользователя: " + data.message);
                    }
                })
                .catch(error => console.error('Ошибка:', error));
            }
        }
    </script>
</body>
</html>
