<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        trim($_POST['title'] ?? ''),
        trim($_POST['description'] ?? ''),
        (float) ($_POST['price'] ?? 0),
        trim($_POST['genre'] ?? ''),
        trim($_POST['platform'] ?? ''),
        trim($_POST['image_url'] ?? ''),
        (float) ($_POST['rating'] ?? 0),
        (int) ($_POST['stock'] ?? 0),
        isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($data[0] === '' || $data[2] <= 0) {
        $error = 'Заполни название и цену';
    } else {
        $stmt = db()->prepare('INSERT INTO games (title, description, price, genre, platform, image_url, rating, stock, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute($data);
        redirect('/admin/index.php');
    }
}

require __DIR__ . '/../header.php';
?>

<section class="admin-form">
    <p class="eyebrow">Admin</p>
    <h1>Добавить игру</h1>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <?php require __DIR__ . '/form.php'; ?>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
