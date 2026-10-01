<?php $game = $game ?? []; ?>
<form method="post" class="form-grid">
    <label>Название
        <input type="text" name="title" value="<?= e($game['title'] ?? '') ?>" required>
    </label>
    <label>Цена
        <input type="number" name="price" min="1" step="0.01" value="<?= e((string) ($game['price'] ?? '')) ?>" required>
    </label>
    <label>Жанр
        <input type="text" name="genre" value="<?= e($game['genre'] ?? '') ?>" required>
    </label>
    <label>Платформа
        <input type="text" name="platform" value="<?= e($game['platform'] ?? '') ?>" required>
    </label>
    <label>Рейтинг
        <input type="number" name="rating" min="0" max="10" step="0.1" value="<?= e((string) ($game['rating'] ?? '8.5')) ?>">
    </label>
    <label>Остаток
        <input type="number" name="stock" min="0" value="<?= e((string) ($game['stock'] ?? '10')) ?>">
    </label>
    <label class="wide">Ссылка на обложку
        <input type="url" name="image_url" value="<?= e($game['image_url'] ?? '') ?>">
    </label>
    <label class="wide">Описание
        <textarea name="description" rows="6" required><?= e($game['description'] ?? '') ?></textarea>
    </label>
    <label class="check">
        <input type="checkbox" name="is_active" <?= ($game['is_active'] ?? 1) ? 'checked' : '' ?>>
        Показывать в каталоге
    </label>
    <div class="form-actions">
        <button class="btn btn-primary">Сохранить</button>
        <a class="btn btn-secondary" href="<?= e(url('/admin/index.php')) ?>">Отмена</a>
    </div>
</form>
