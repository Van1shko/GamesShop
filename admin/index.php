<?php
require_once __DIR__ . '/../functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowedStatuses = ['new', 'paid', 'processing', 'completed', 'cancelled'];
    $status = $_POST['status'];

    if (in_array($status, $allowedStatuses, true)) {
        $stmt = db()->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, (int) $_POST['order_id']]);
    }

    redirect('/admin/index.php');
}

$games = db()->query('SELECT * FROM games ORDER BY created_at DESC')->fetchAll();
$orders = db()->query(
    'SELECT o.*, u.email, u.name
     FROM orders o
     JOIN users u ON u.id = o.user_id
     ORDER BY o.created_at DESC
     LIMIT 20'
)->fetchAll();

require __DIR__ . '/../header.php';
?>

<section>
    <div class="section-head">
        <div>
            <p class="eyebrow">Admin</p>
            <h1>Управление играми</h1>
        </div>
        <a class="btn btn-primary" href="<?= e(url('/admin/create.php')) ?>">Добавить игру</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Жанр</th>
                    <th>Платформа</th>
                    <th>Цена</th>
                    <th>Остаток</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= (int) $game['id'] ?></td>
                        <td><?= e($game['title']) ?></td>
                        <td><?= e($game['genre']) ?></td>
                        <td><?= e($game['platform']) ?></td>
                        <td><?= money($game['price']) ?></td>
                        <td><?= (int) $game['stock'] ?></td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/edit.php?id=' . (int) $game['id'])) ?>">Редактировать</a>
                            <a class="danger" href="<?= e(url('/admin/delete.php?id=' . (int) $game['id'])) ?>" onclick="return confirm('Удалить игру?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section>
    <div class="section-head">
        <div>
            <p class="eyebrow">Orders</p>
            <h2>Последние заказы</h2>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Покупатель</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= (int) $order['id'] ?></td>
                        <td><?= e($order['name']) ?><br><span class="muted"><?= e($order['email']) ?></span></td>
                        <td><?= money($order['total_price']) ?></td>
                        <td><?= e(order_status_label($order['status'])) ?></td>
                        <td><?= e($order['created_at']) ?></td>
                        <td>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                <select name="status">
                                    <?php foreach (['new', 'paid', 'processing', 'completed', 'cancelled'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e(order_status_label($status)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-secondary">OK</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
