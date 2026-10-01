<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function url(string $path = ''): string
{
    $basePath = parse_url(APP_URL, PHP_URL_PATH) ?: '';
    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function require_auth(): array
{
    $user = current_user();
    if (!$user) {
        redirect('/login.php');
    }

    return $user;
}

function require_admin(): array
{
    $user = require_auth();
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        exit('Доступ запрещён');
    }

    return $user;
}

function require_customer(): array
{
    $user = require_auth();
    if ($user['role'] === 'admin') {
        redirect('/admin/index.php');
    }

    return $user;
}

function money(float|int|string $value): string
{
    return number_format((float) $value, 0, ',', ' ') . ' ₽';
}

function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int
{
    return array_sum(cart_items());
}

function game_image(?string $image): string
{
    return $image ?: 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=900&q=80';
}

function order_with_items(int $orderId, int $userId): ?array
{
    $stmt = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if (!$order) {
        return null;
    }

    $itemsStmt = db()->prepare(
        'SELECT oi.quantity, oi.price, g.title, g.platform
         FROM order_items oi
         JOIN games g ON g.id = oi.game_id
         WHERE oi.order_id = ?
         ORDER BY oi.id'
    );
    $itemsStmt->execute([$orderId]);
    $order['items'] = $itemsStmt->fetchAll();

    return $order;
}

function receipt_text(array $order, array $user): string
{
    $lines = [
        'EzGameStore',
        'Чек по заказу #' . $order['id'],
        'Покупатель: ' . $user['name'] . ' <' . $user['email'] . '>',
        'Дата: ' . $order['created_at'],
        '',
        'Товары:',
    ];

    foreach ($order['items'] as $item) {
        $lines[] = sprintf(
            '- %s (%s), %d шт. x %s',
            $item['title'],
            $item['platform'],
            (int) $item['quantity'],
            money($item['price'])
        );
    }

    $lines[] = '';
    $lines[] = 'Итого: ' . money($order['total_price']);
    $lines[] = 'Статус: ' . order_status_label($order['status']);
    $lines[] = '';
    $lines[] = 'Спасибо за покупку!';

    return implode(PHP_EOL, $lines);
}

function send_receipt(array $order, array $user): void
{
    $subject = 'Чек EzGameStore по заказу #' . $order['id'];
    $message = receipt_text($order, $user);
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: EzGameStore <no-reply@ezgamestore.local>',
    ];

    @mail($user['email'], $subject, $message, implode("\r\n", $headers));

    $logDir = __DIR__ . '/data';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    file_put_contents(
        $logDir . '/mail.log',
        '[' . date('Y-m-d H:i:s') . '] To: ' . $user['email'] . PHP_EOL . $message . PHP_EOL . str_repeat('-', 60) . PHP_EOL,
        FILE_APPEND
    );
}

function order_status_label(string $status): string
{
    return [
        'new' => 'Новый',
        'paid' => 'Оплачен',
        'processing' => 'В обработке',
        'completed' => 'Выполнен',
        'cancelled' => 'Отменён',
    ][$status] ?? $status;
}
