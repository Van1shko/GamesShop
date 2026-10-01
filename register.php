<?php
require_once __DIR__ . '/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (mb_strlen($password) < 6) {
        $error = 'Пароль должен быть минимум 6 символов';
    } else {
        try {
            $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'user']);
            $_SESSION['user_id'] = (int) db()->lastInsertId();
            redirect('/index.php');
        } catch (PDOException) {
            $error = 'Пользователь с таким email уже есть';
        }
    }
}

require __DIR__ . '/header.php';
?>

<section class="auth">
    <form method="post" class="auth-card">
        <p class="eyebrow">Регистрация</p>
        <h1>Создать аккаунт</h1>
        <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
        <label>Имя<input type="text" name="name" required></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Пароль<input type="password" name="password" minlength="6" required></label>
        <button class="btn btn-primary">Зарегистрироваться</button>
        <p>Уже есть аккаунт? <a href="<?= e(url('/login.php')) ?>">Войти</a></p>
    </form>
</section>

<?php require __DIR__ . '/footer.php'; ?>
