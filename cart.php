<?php
require_once __DIR__ . '/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gameId = (int) ($_POST['game_id'] ?? 0);

    if ($action === 'add' && $gameId > 0) {
        $_SESSION['cart'][$gameId] = ($_SESSION['cart'][$gameId] ?? 0) + 1;
    }

    if ($action === 'update' && $gameId > 0) {
        $qty = max(0, (int) ($_POST['qty'] ?? 0));
        if ($qty === 0) {
            unset($_SESSION['cart'][$gameId]);
        } else {
            $_SESSION['cart'][$gameId] = $qty;
        }
    }

    redirect('/cart.php');
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    redirect('/cart.php');
}

$cart = cart_items();
$games = [];
$total = 0;

if ($cart) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM games WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $games = $stmt->fetchAll();
}

require __DIR__ . '/header.php';
?>

<section>
    <div class="section-head">
        <div>
            <p class="eyebrow">Корзина</p>
            <h1>Твой заказ</h1>
        </div>
        <?php if ($cart): ?>
            <a class="btn btn-secondary" href="<?= e(url('/cart.php?action=clear')) ?>">Очистить</a>
        <?php endif; ?>
    </div>

    <?php if (!$games): ?>
        <p class="empty">Корзина пустая. Пора выбрать пару игр.</p>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($games as $game): ?>
                <?php
                $qty = $cart[$game['id']] ?? 1;
                $sum = (float) $game['price'] * $qty;
                $total += $sum;
                ?>
                <div class="cart-item">
                    <img src="<?= e(game_image($game['image_url'])) ?>" alt="<?= e($game['title']) ?>">
                    <div>
                        <h3><?= e($game['title']) ?></h3>
                        <p><?= money($game['price']) ?> за штуку</p>
                    </div>
                    <form method="post" class="qty-form">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="game_id" value="<?= (int) $game['id'] ?>">
                        <input type="number" name="qty" min="0" value="<?= (int) $qty ?>">
                        <button class="btn btn-secondary">Обновить</button>
                    </form>
                    <strong><?= money($sum) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="checkout">
            <span>Итого</span>
            <strong><?= money($total) ?></strong>
            <a class="btn btn-primary" href="<?= e(current_user() ? url('/checkout.php') : url('/login.php?next=checkout')) ?>">Оформить заказ</a>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.php'; ?>
