CREATE DATABASE IF NOT EXISTS assignment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE assignment_db;

-- Clear old tables to ensure new structure is applied
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS faqs;
DROP TABLE IF EXISTS users;

-- 1. Users table (Admin & Members)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255),
    email VARCHAR(150),
    phone VARCHAR(20),
    avatar VARCHAR(255) DEFAULT 'default_avatar.png',
    role ENUM('admin','user') DEFAULT 'user',
    status ENUM('active','locked') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Products table (Dried Food)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'placeholder.jpg',
    stock INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 4. News table
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'placeholder.jpg',
    author_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 5. Contacts table
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(150),
    message TEXT,
    status ENUM('unread','read','replied') DEFAULT 'unread',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 6. Settings table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE,
    `value` TEXT
);

-- 7. Comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    content TEXT NOT NULL,
    rating INT DEFAULT 5,
    status ENUM('pending','approved','hidden') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 8. FAQs table
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 9. Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'cod',
    shipping_name VARCHAR(255),
    shipping_phone VARCHAR(20),
    shipping_address TEXT,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 10. Order Items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Initial Data
INSERT INTO users (username, password, role, fullname)
VALUES ('admin', MD5('123456'), 'admin', 'System Administrator');

INSERT INTO settings (`key`, `value`) VALUES
('site_name', 'Khô Đặc Sản - Tinh Hoa Đồ Khô'),
('phone', '0988 123 456'),
('address', '123 Đường ABC, Quận 1, TP. HCM'),
('homepage_content', 'Chúng tôi cung cấp các loại khô đặc sản chất lượng nhất từ khắp mọi miền đất nước.'),
('about_us', 'Với hơn 10 năm kinh nghiệm, chúng tôi tự hào mang đến những sản phẩm khô sạch, an toàn và đậm đà hương vị truyền thống.'),
('about_page_full', 'Hành trình của chúng tôi bắt đầu từ niềm đam mê với ẩm thực vùng miền. Khô Đặc Sản cam kết mang đến những sản phẩm được tuyển chọn kỹ lưỡng, phơi khô tự nhiên và bảo quản đúng tiêu chuẩn vệ sinh an toàn thực phẩm.');

INSERT INTO categories (name, description) VALUES
('Khô Cá', 'Các loại khô cá biển, cá đồng phơi khô tự nhiên.'),
('Khô Mực', 'Mực khô, mực một nắng chất lượng cao.'),
('Khô Thịt', 'Khô bò, khô gà lá chanh, khô heo cháy tỏi.'),
('Khô Hải Sản', 'Tôm khô, mực khô và các loại hải sản đặc sản.'),
('Combo Quà Tặng', 'Các gói sản phẩm kết hợp, thích hợp làm quà biếu.');

INSERT INTO products (category_id, name, price, description, is_featured, image) VALUES
(1, 'Khô Cá Lóc Đồng', 250000, 'Khô cá lóc đồng chính gốc miền Tây, thịt thơm ngọt, dai ngon tự nhiên.', TRUE, 'khô cá lóc.png'),
(1, 'Khô Cá Đuối', 280000, 'Khô cá đuối đen thơm nồng, thích hợp nướng chấm mắm me.', FALSE, 'khô cá đuối.png'),
(2, 'Mực Khô Loại 1', 850000, 'Mực khô Phan Thiết size lớn, thịt dày, ngọt lịm, nướng lên cực thơm.', TRUE, 'khô mực.png'),
(3, 'Khô Gà Lá Chanh', 150000, 'Khô gà xé cay giòn rụm, thơm nồng mùi lá chanh tươi.', TRUE, 'khô gà.png'),
(3, 'Khô Bò Sợi', 450000, 'Khô bò sợi cao cấp, tẩm ướp gia vị đậm đà, cay cay ngọt ngọt.', FALSE, 'khô bò.png'),
(4, 'Tôm Khô Đất', 650000, 'Tôm khô đất Cà Mau chính gốc, màu đỏ tự nhiên, vị ngọt thanh.', TRUE, 'tôm khô.png'),
(5, 'Combo Tiết Kiệm', 1200000, 'Sự kết hợp hoàn hảo của các loại khô bán chạy nhất, tiết kiệm hơn khi mua lẻ.', TRUE, 'combo.png'),
(1, 'Khô Cá Sặc', 220000, 'Khô cá sặc rằn (sặc bổi) béo ngậy, thịt thơm, đặc sản nổi tiếng miền Tây.', FALSE, 'khô cá sặc.png'),
(1, 'Khô Cá Chạch', 350000, 'Khô cá chạch đồng, món ăn dân dã nhưng cực kỳ đưa cơm, giàu dinh dưỡng.', FALSE, 'khô cá chạch.png'),
(1, 'Khô Cá Dứa', 480000, 'Khô cá dứa 1 nắng cao cấp, thịt trắng, vị béo bùi đặc trưng.', FALSE, 'khô cá dứa.png'),
(1, 'Khô Cá Lưỡi Trâu', 190000, 'Khô cá lưỡi trâu tẩm vị, giòn ngon, thích hợp làm món nhắm hoặc ăn với cơm.', FALSE, 'khô cá lưỡi trâu.png');

INSERT INTO news (title, content) VALUES
('Cách chọn mực khô ngon', 'Làm sao để phân biệt mực khô chất lượng và mực giả? Hãy cùng tìm hiểu về độ dày của thịt, màu sắc và mùi thơm đặc trưng của mực thật...'),
('Bí quyết làm khô cá lóc tại nhà', 'Chỉ với vài bước đơn giản như làm sạch, ướp gia vị theo tỷ lệ chuẩn và phơi nắng giòn, bạn có thể tự tay làm món khô cá lóc thơm ngon cho gia đình.');

INSERT INTO faqs (question, answer) VALUES
('Sản phẩm có đảm bảo vệ sinh không?', 'Tất cả sản phẩm của chúng tôi đều được kiểm định an toàn thực phẩm, không sử dụng chất bảo quản độc hại.'),
('Giao hàng mất bao lâu?', 'Thời gian giao hàng nội thành HCM là 24h, các tỉnh khác từ 2-3 ngày làm việc.');