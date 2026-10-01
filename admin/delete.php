<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = db()->prepare('DELETE FROM games WHERE id = ?');
    $stmt->execute([$id]);
}

redirect('/admin/index.php');
