<?php
// Проверка, авторизован ли администратор
include '../check_admin.php'; // Проверка прав администратора

// Подключение к базе данных
require_once '../db_connection.php'; // Подключение к базе данных

// Обработка обновления статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_status') {
    $suggestionId = intval($_GET['id']); // Приводим к целому числу для безопасности
    $status = $_GET['status'];

    // Проверяем, что статус допустимый
    $allowedStatuses = ['new', 'in_progress', 'completed', 'rejected'];
    if (!in_array($status, $allowedStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Недопустимый статус']);
        exit();
    }

    // Обновляем статус
    $stmt = $conn->prepare("UPDATE user_suggestions SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $suggestionId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Статус обновлён']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении статуса']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Обработка фильтрации и сортировки
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'filter') {
    $status = $_GET['status'] ?? 'all';
    $sort = $_GET['sort'] ?? 'newest';

    // Базовый SQL-запрос
    $sql = "SELECT s.id, u.username, s.title, s.description, s.created_at, s.status 
            FROM user_suggestions s 
            JOIN users u ON s.user_id = u.id";

    // Фильтрация по статусу
    if ($status !== 'all') {
        $sql .= " WHERE s.status = ?";
    }

    // Сортировка
    $sql .= " ORDER BY s.created_at " . ($sort === 'newest' ? 'DESC' : 'ASC');

    // Подготавливаем запрос
    $stmt = $conn->prepare($sql);
    if ($status !== 'all') {
        $stmt->bind_param("s", $status);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Преобразуем статус в читаемый формат
            $statusText = [
                'new' => 'Новое',
                'in_progress' => 'В процессе',
                'completed' => 'Завершено',
                'rejected' => 'Отклонено'
            ][$row['status']] ?? 'Неизвестно';

            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['created_at']}</td>
                    <td>{$statusText}</td>
                    <td class='actions'>
                        <button class='edit' onclick='updateStatus({$row['id']}, \"in_progress\")'>В процессе</button>
                        <button class='edit' onclick='updateStatus({$row['id']}, \"completed\")'>Завершить</button>
                        <button class='delete' onclick='updateStatus({$row['id']}, \"rejected\")'>Отклонить</button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>Нет предложений</td></tr>";
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление предложениями</title>
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

        .status-filter, .sort-filter {
            margin-bottom: 20px;
        }

        .status-filter select, .sort-filter select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #9F8B70;
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
        <h1>Управление предложениями</h1>

        <!-- Фильтры -->
        <div class="filters">
            <div class="status-filter">
                <label for="status">Статус:</label>
                <select id="status" onchange="filterSuggestions()">
                    <option value="all">Все</option>
                    <option value="new">Новое</option>
                    <option value="in_progress">В процессе</option>
                    <option value="completed">Завершено</option>
                    <option value="rejected">Отклонено</option>
                </select>
            </div>
            <div class="sort-filter">
                <label for="sort">Сортировка:</label>
                <select id="sort" onchange="filterSuggestions()">
                    <option value="newest">Сначала новые</option>
                    <option value="oldest">Сначала старые</option>
                </select>
            </div>
        </div>

        <!-- Таблица с предложениями -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Заголовок</th>
                    <th>Описание</th>
                    <th>Дата создания</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="suggestionsTable">
                <?php
                // Запрос на получение предложений
                $sql = "SELECT s.id, u.username, s.title, s.description, s.created_at, s.status 
                        FROM user_suggestions s 
                        JOIN users u ON s.user_id = u.id 
                        ORDER BY s.created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Преобразуем статус в читаемый формат
                        $statusText = [
                            'new' => 'Новое',
                            'in_progress' => 'В процессе',
                            'completed' => 'Завершено',
                            'rejected' => 'Отклонено'
                        ][$row['status']] ?? 'Неизвестно';

                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['title']}</td>
                                <td>{$row['description']}</td>
                                <td>{$row['created_at']}</td>
                                <td>{$statusText}</td>
                                <td class='actions'>
                                    <button class='edit' onclick='updateStatus({$row['id']}, \"in_progress\")'>В процессе</button>
                                    <button class='edit' onclick='updateStatus({$row['id']}, \"completed\")'>Завершить</button>
                                    <button class='delete' onclick='updateStatus({$row['id']}, \"rejected\")'>Отклонить</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Нет предложений</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Функция для фильтрации и сортировки предложений
        function filterSuggestions() {
            const status = document.getElementById('status').value;
            const sort = document.getElementById('sort').value;

            fetch(`manage_suggestions.php?action=filter&status=${status}&sort=${sort}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('suggestionsTable').innerHTML = data;
                })
                .catch(error => console.error('Ошибка:', error));
        }

        // Функция для обновления статуса предложения
        function updateStatus(suggestionId, status) {
            if (confirm("Вы уверены, что хотите изменить статус?")) {
                fetch(`manage_suggestions.php?action=update_status&id=${suggestionId}&status=${status}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Статус успешно обновлён");
                        filterSuggestions(); // Обновляем таблицу без перезагрузки страницы
                    } else {
                        alert("Ошибка при обновлении статуса: " + data.message);
                    }
                })
                .catch(error => console.error('Ошибка:', error));
            }
        }
    </script>
</body>
</html>