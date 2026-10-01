<?php
require_once __DIR__ . '/functions.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM games WHERE id = ? AND is_active = 1');
$stmt->execute([$id]);
$game = $stmt->fetch();

if (!$game) {
    http_response_code(404);
    exit('Игра не найдена');
}

require __DIR__ . '/header.php';
?>

<section class="game-page">
    <img class="game-cover" src="<?= e(game_image($game['image_url'])) ?>" alt="<?= e($game['title']) ?>">
    <div class="game-info">
        <p class="eyebrow"><?= e($game['genre']) ?> / <?= e($game['platform']) ?></p>
        <h1><?= e($game['title']) ?></h1>
        <p><?= nl2br(e($game['description'])) ?></p>
        <div class="stats">
            <span>Рейтинг: <strong><?= e((string) $game['rating']) ?></strong></span>
            <span>В наличии: <strong><?= (int) $game['stock'] ?></strong></span>
        </div>
        <div class="buy-row">
            <strong><?= money($game['price']) ?></strong>
            <form method="post" action="<?= e(url('/cart.php')) ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="game_id" value="<?= (int) $game['id'] ?>">
                <button class="btn btn-primary">Добавить в корзину</button>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
