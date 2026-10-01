<?php
require_once __DIR__ . '/functions.php';

$genres = db()->query('SELECT DISTINCT genre FROM games ORDER BY genre')->fetchAll(PDO::FETCH_COLUMN);
$platforms = db()->query('SELECT DISTINCT platform FROM games ORDER BY platform')->fetchAll(PDO::FETCH_COLUMN);

$search = trim($_GET['q'] ?? '');
$genre = trim($_GET['genre'] ?? '');
$platform = trim($_GET['platform'] ?? '');
$sort = $_GET['sort'] ?? 'new';

$sql = 'SELECT * FROM games WHERE is_active = 1';
$params = [];

if ($search !== '') {
    $sql .= ' AND (title LIKE ? OR description LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($genre !== '') {
    $sql .= ' AND genre = ?';
    $params[] = $genre;
}

if ($platform !== '') {
    $sql .= ' AND platform = ?';
    $params[] = $platform;
}

$orders = [
    'cheap' => 'price ASC',
    'expensive' => 'price DESC',
    'rating' => 'rating DESC',
    'new' => 'created_at DESC',
];
$sql .= ' ORDER BY ' . ($orders[$sort] ?? $orders['new']);

$stmt = db()->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

require __DIR__ . '/header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Digital games store</p>
        <h1>Игры, ключи и админка в одном PHP-проекте</h1>
        <p>Каталог с поиском, фильтрами, корзиной, авторизацией и управлением товарами. Нормальный проект для портфолио, который можно показать на hh.ru.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="#catalog">Смотреть каталог</a>
        </div>
    </div>
    <div class="hero-panel">
        <span>Сегодня в подборке</span>
        <strong><?= count($games) ?> игр</strong>
        <p>Фильтры по жанру и платформе, сортировка по цене и рейтингу.</p>
    </div>
</section>

<section id="catalog" class="catalog">
    <div class="section-head">
        <div>
            <p class="eyebrow">Каталог</p>
            <h2>Выбери игру</h2>
        </div>
    </div>

    <form class="filters" method="get">
        <input type="search" name="q" placeholder="Поиск по названию" value="<?= e($search) ?>">
        <select name="genre">
            <option value="">Все жанры</option>
            <?php foreach ($genres as $item): ?>
                <option value="<?= e($item) ?>" <?= $genre === $item ? 'selected' : '' ?>><?= e($item) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="platform">
            <option value="">Все платформы</option>
            <?php foreach ($platforms as $item): ?>
                <option value="<?= e($item) ?>" <?= $platform === $item ? 'selected' : '' ?>><?= e($item) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort">
            <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Сначала новые</option>
            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>По рейтингу</option>
            <option value="cheap" <?= $sort === 'cheap' ? 'selected' : '' ?>>Дешевле</option>
            <option value="expensive" <?= $sort === 'expensive' ? 'selected' : '' ?>>Дороже</option>
        </select>
        <button class="btn btn-primary" type="submit">Найти</button>
    </form>

    <div class="game-grid">
        <?php foreach ($games as $game): ?>
            <article class="game-card">
                <a href="<?= e(url('/game.php?id=' . (int) $game['id'])) ?>">
                    <img src="<?= e(game_image($game['image_url'])) ?>" alt="<?= e($game['title']) ?>">
                </a>
                <div class="game-card-body">
                    <div class="meta">
                        <span><?= e($game['genre']) ?></span>
                        <span><?= e($game['platform']) ?></span>
                    </div>
                    <h3><a href="<?= e(url('/game.php?id=' . (int) $game['id'])) ?>"><?= e($game['title']) ?></a></h3>
                    <p><?= e(mb_strimwidth($game['description'], 0, 110, '...')) ?></p>
                    <div class="card-bottom">
                        <strong><?= money($game['price']) ?></strong>
                        <form method="post" action="<?= e(url('/cart.php')) ?>">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="game_id" value="<?= (int) $game['id'] ?>">
                            <button class="btn-icon" title="В корзину">+</button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (!$games): ?>
        <p class="empty">Ничего не найдено. Попробуй изменить фильтры.</p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/footer.php'; ?>
