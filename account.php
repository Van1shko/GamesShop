<?php
require_once __DIR__ . '/functions.php';

$user = require_customer();

$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();

require __DIR__ . '/header.php';
?>

<section>
    <div class="section-head">
        <div>
            <p class="eyebrow">Личный кабинет</p>
            <h1>Мои заказы</h1>
        </div>
        <a class="btn btn-secondary" href="<?= e(url('/index.php#catalog')) ?>">В каталог</a>
    </div>

    <?php if (!$orders): ?>
        <p class="empty">Заказов пока нет. Самое время набрать корзину.</p>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <article class="order-card">
                    <div>
                        <p class="eyebrow">Заказ #<?= (int) $order['id'] ?></p>
                        <h3><?= money($order['total_price']) ?></h3>
                        <p><?= e($order['created_at']) ?> / <?= e(order_status_label($order['status'])) ?></p>
                    </div>
                    <a class="btn btn-secondary" href="<?= e(url('/receipt.php?id=' . (int) $order['id'])) ?>">Открыть чек</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.php'; ?>
