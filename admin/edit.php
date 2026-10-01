<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM games WHERE id = ?');
$stmt->execute([$id]);
$game = $stmt->fetch();

if (!$game) {
    http_response_code(404);
    exit('Игра не найдена');
}

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
        $id,
    ];

    if ($data[0] === '' || $data[2] <= 0) {
        $error = 'Заполни название и цену';
    } else {
        $stmt = db()->prepare('UPDATE games SET title = ?, description = ?, price = ?, genre = ?, platform = ?, image_url = ?, rating = ?, stock = ?, is_active = ? WHERE id = ?');
        $stmt->execute($data);
        redirect('/admin/index.php');
    }
}

require __DIR__ . '/../header.php';
?>

<section class="admin-form">
    <p class="eyebrow">Admin</p>
    <h1>Редактировать игру</h1>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <?php require __DIR__ . '/form.php'; ?>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
