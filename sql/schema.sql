SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS ezgamestore
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ezgamestore;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    genre VARCHAR(80) NOT NULL,
    platform VARCHAR(80) NOT NULL,
    image_url VARCHAR(500) NULL,
    rating DECIMAL(3,1) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(180) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('new', 'paid', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    game_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password_hash, role) VALUES
('Admin', 'admin@ezgame.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO games (title, description, price, genre, platform, image_url, rating, stock) VALUES
('Cyberpunk 2077', 'Большая ролевая игра про Найт-Сити, импланты, свободу прохождения и сюжетные решения.', 2499, 'RPG', 'PC', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=80', 8.9, 23),
('Elden Ring', 'Мрачное фэнтези с открытым миром, сложными боссами и исследованием Междуземья.', 3999, 'Action RPG', 'PC', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=80', 9.6, 12),
('Forza Horizon 5', 'Аркадные гонки по Мексике с яркими трассами, сотнями машин и сетевыми событиями.', 2999, 'Racing', 'Xbox', 'https://images.unsplash.com/photo-1511994477422-b69e44bd4ea9?auto=format&fit=crop&w=900&q=80', 9.1, 18),
('Hades', 'Динамичный roguelike про побег из подземного царства с отличной боевой системой.', 899, 'Roguelike', 'PC', 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?auto=format&fit=crop&w=900&q=80', 9.4, 40),
('The Witcher 3', 'Культовая RPG про Геральта, охоту на чудовищ и сильные сюжетные квесты.', 1199, 'RPG', 'PlayStation', 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?auto=format&fit=crop&w=900&q=80', 9.7, 16),
('Stardew Valley', 'Уютный симулятор фермы с развитием хозяйства, отношениями и исследованием шахт.', 599, 'Simulation', 'Nintendo Switch', 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?auto=format&fit=crop&w=900&q=80', 9.0, 35);
