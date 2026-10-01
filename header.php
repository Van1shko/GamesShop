<?php
header('Content-Type: text/html; charset=UTF-8');
$user = current_user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <a class="logo" href="<?= e(url('/index.php')) ?>">EzGameStore</a>
    <nav>
        <a href="<?= e(url('/index.php#catalog')) ?>">Каталог</a>
        <a href="<?= e(url('/cart.php')) ?>">Корзина<?= cart_count() ? ' (' . cart_count() . ')' : '' ?></a>
        <?php if ($user): ?>
            <?php if ($user['role'] !== 'admin'): ?>
                <a href="<?= e(url('/account.php')) ?>">Кабинет</a>
            <?php endif; ?>
            <span><?= e($user['name']) ?></span>
            <a href="<?= e(url('/logout.php')) ?>">Выйти</a>
        <?php else: ?>
            <a href="<?= e(url('/login.php')) ?>">Войти</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
