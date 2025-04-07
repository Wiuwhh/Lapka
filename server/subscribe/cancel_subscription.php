    <?php
    session_start();

    // Проверка авторизации
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.html');
        exit;
    }

    require_once __DIR__ . '/../db_connection.php';

    // Включение отладки
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Проверка авторизации
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.html');
        exit;
    }

    // Получаем ID подписки из запроса
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscription_id'])) {
        $subscription_id = intval($_POST['subscription_id']);
        $user_id = $_SESSION['user_id'];

        // Проверяем, принадлежит ли подписка пользователю
        $sql = "SELECT id FROM subscriptions WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $subscription_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            die("Подписка не найдена или вы не можете её отменить.");
        }

        // Обновляем статус подписки на "inactive"
        $sql = "UPDATE subscriptions SET status = 'inactive', end_date = CURDATE() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $subscription_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header('Location: /webproject/my_subscriptions.php');
            exit;
        } else {
            die("Ошибка при отмене подписки.");
        }

        $conn->close();
    } else {
        header('Location: /webproject/pets.php');
        exit;
    }
    ?>