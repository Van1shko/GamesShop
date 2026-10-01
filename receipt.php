<?php
require_once __DIR__ . '/functions.php';

$user = require_customer();
$orderId = (int) ($_GET['id'] ?? 0);
$order = order_with_items($orderId, (int) $user['id']);

if (!$order) {
    http_response_code(404);
    exit('Заказ не найден');
}

require __DIR__ . '/header.php';
?>

<section class="receipt">
    <div class="section-head">
        <div>
            <p class="eyebrow">Чек</p>
            <h1>Заказ #<?= (int) $order['id'] ?></h1>
        </div>
        <a class="btn btn-secondary" href="<?= e(url('/account.php')) ?>">Назад</a>
    </div>

    <div class="receipt-box">
        <div class="receipt-row">
            <span>Покупатель</span>
            <strong><?= e($user['name']) ?>, <?= e($user['email']) ?></strong>
        </div>
        <div class="receipt-row">
            <span>Статус</span>
            <strong><?= e(order_status_label($order['status'])) ?></strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Игра</th>
                    <th>Платформа</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?= e($item['title']) ?></td>
                        <td><?= e($item['platform']) ?></td>
                        <td><?= (int) $item['quantity'] ?></td>
                        <td><?= money($item['price']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="checkout">
            <span>Итого</span>
            <strong><?= money($order['total_price']) ?></strong>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
