document.addEventListener("DOMContentLoaded", () => {
    // Проверяем статус авторизации через сервер
    fetch('server/auth_status.php', {
        credentials: 'include' // Добавляем передачу cookies
    })
    .then(response => response.json())
    .then(data => {
        if (data.authenticated) {
            // Если пользователь авторизован
            document.getElementById('register-btn').style.display = 'none';
            document.getElementById('login-btn').style.display = 'none';
            document.getElementById('account-icon').style.display = 'block';
            document.getElementById('logout-btn').style.display = 'block';
        } else {
            // Если пользователь не авторизован
            document.getElementById('register-btn').style.display = 'inline-block';
            document.getElementById('login-btn').style.display = 'inline-block';
            document.getElementById('account-icon').style.display = 'none';
            document.getElementById('logout-btn').style.display = 'none';
        }
    })
    .catch(error => console.error('Ошибка при проверке авторизации:', error));

    // Обработчик для модалки подтверждения выхода
    const logoutBtn = document.getElementById('logout-btn');
    const logoutModal = document.getElementById('logout-modal');
    const confirmLogout = document.getElementById('confirm-logout');
    const cancelLogout = document.getElementById('cancel-logout');

    logoutBtn.addEventListener('click', (event) => {
        event.preventDefault(); // Предотвращаем переход по ссылке
        logoutModal.style.display = 'flex'; // Показываем модальное окно
    });

    cancelLogout.addEventListener('click', () => {
        logoutModal.style.display = 'none'; // Скрываем модальное окно
    });

    confirmLogout.addEventListener('click', () => {
        fetch('server/logout.php', {
            method: 'POST',
            credentials: 'include' // Добавляем передачу cookies
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем интерфейс после выхода
                document.getElementById('register-btn').style.display = 'inline-block';
                document.getElementById('login-btn').style.display = 'inline-block';
                document.getElementById('account-icon').style.display = 'none';
                document.getElementById('logout-btn').style.display = 'none';
                logoutModal.style.display = 'none';
                // Перенаправляем на главную страницу
                window.location.href = 'index.html';
            } else {
                console.error('Ошибка при выходе из аккаунта:', data.error);
            }
        })
        .catch(error => console.error('Ошибка при выходе из аккаунта:', error));
    });
});
