<?php
require_once __DIR__ . '/functions.php';

$user = require_customer();
$cart = cart_items();

if (!$cart) {
    redirect('/cart.php');
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = db()->prepare("SELECT id, title, price FROM games WHERE id IN ($placeholders)");
$stmt->execute($ids);
$games = $stmt->fetchAll();

$total = 0;
foreach ($games as $game) {
    $total += (float) $game['price'] * ($cart[$game['id']] ?? 1);
}

db()->beginTransaction();
$orderStmt = db()->prepare('INSERT INTO orders (user_id, total_price, status, customer_email, customer_name) VALUES (?, ?, ?, ?, ?)');
$orderStmt->execute([$user['id'], $total, 'processing', $user['email'], $user['name']]);
$orderId = (int) db()->lastInsertId();

$itemStmt = db()->prepare('INSERT INTO order_items (order_id, game_id, quantity, price) VALUES (?, ?, ?, ?)');
foreach ($games as $game) {
    $itemStmt->execute([$orderId, $game['id'], $cart[$game['id']], $game['price']]);
}
db()->commit();

$_SESSION['cart'] = [];
$order = order_with_items($orderId, (int) $user['id']);
if ($order) {
    send_receipt($order, $user);
}

require __DIR__ . '/header.php';
?>

<section class="success">
    <p class="eyebrow">Заказ оформлен</p>
    <h1>Спасибо за покупку, <?= e($user['name']) ?>!</h1>
    <p>Заказ #<?= $orderId ?> на сумму <?= money($total) ?> принят в обработку. Чек отправлен на <?= e($user['email']) ?>, а локальная копия сохранена в <code>data/mail.log</code>.</p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/account.php')) ?>">Открыть кабинет</a>
        <a class="btn btn-secondary" href="<?= e(url('/index.php')) ?>">Вернуться в каталог</a>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
