<?php
require_once __DIR__ . '/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        redirect(($_GET['next'] ?? '') === 'checkout' ? '/checkout.php' : '/index.php');
    }

    $error = 'Неверный email или пароль';
}

require __DIR__ . '/header.php';
?>

<section class="auth">
    <form method="post" class="auth-card">
        <p class="eyebrow">Вход</p>
        <h1>С возвращением</h1>
        <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
        <label>Email<input type="email" name="email" required></label>
        <label>Пароль<input type="password" name="password" required></label>
        <button class="btn btn-primary">Войти</button>
        <p>Нет аккаунта? <a href="<?= e(url('/register.php')) ?>">Зарегистрироваться</a></p>
    </form>
</section>

<?php require __DIR__ . '/footer.php'; ?>
