CREATE DATABASE IF NOT EXISTS warungreza CHARACTER SET utf8mb4;
USE warungreza;

DROP TABLE IF EXISTS reviews, promos, cart, order_details, orders, menus, categories, users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin','pelanggan') DEFAULT 'pelanggan'
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60)
);

CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100),
    description TEXT,
    price INT,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    menu_id INT,
    qty INT,
    note VARCHAR(150) DEFAULT '',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('dine-in','takeaway'),
    table_no VARCHAR(10) DEFAULT '',
    payment VARCHAR(20), 
    payment_status VARCHAR(30) DEFAULT 'Bayar di Kasir',
    total INT,
    status ENUM('baru','diproses','siap','selesai','dibatalkan') DEFAULT 'baru',
    cancel_reason VARCHAR(200) DEFAULT '',
    cancelled_by VARCHAR(20) DEFAULT '',
    refund_status VARCHAR(40) DEFAULT '-',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    menu_id INT,
    qty INT,
    price INT,
    note VARCHAR(150),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@email.com', 'admin1234', 'admin'),
('Asep', 'asep@email.com', 'asep1234', 'pelanggan');

INSERT INTO categories (name) VALUES ('Makanan Utama'),('Minuman'),('Camilan');

INSERT INTO menus (category_id,name,description,price,stock,image) VALUES
(1,'Ayam Geprek (Free Es Teh)','Nasi ayam geprek, sambal, dan es teh',18000,50,'https://upload.wikimedia.org/wikipedia/commons/2/24/Ayam_geprek.png?utm_source=id.wikipedia.org&utm_campaign=index&utm_content=original'),
(1,'Ayam Bakar Khas Ngawi','Ayam bakar dengan lalapan',28000,30,'https://buckets.sasa.co.id/v1/AUTH_Assets/Assets/p/website/medias/page_medias/nasi_ayam_bakar.jpg'),
(1,'Bakmi Goreng Jawa','Mie goreng bumbu khas Jawa',22000,25,'https://assets.unileversolutions.com/v1/55546655.jpg'),
(1,'Magelangan', 'Nasi goreng campur mie',25000,30,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcThtUWpQhouC60lzPXlF8Sx_wJro_2cV7_zOdKs4vSOrDtpUZW45wQ3EOY&s=10'),
(2,'Es Teh Manis','Teh dingin segar',5000,50,'https://asset.kompas.com/crops/GRGOpdUcySMF4sTvMlSEpJpFcTM=/0x0:4939x3293/750x500/data/photo/2022/09/25/63300cfd403f0.jpg'),
(2,'Es Jeruk','Jeruk dingin segar',6000,20,'https://dcostseafood.id/wp-content/uploads/2021/12/ES-JERUK-murni.jpg'),
(3,'Bakwan jagung','Bakwan isi jagung renyah',12000,40,'https://buckets.sasa.co.id/v1/AUTH_Assets/Assets/p/website/medias/page_medias/7b37abf4b24c1ffdfdec0eeec3c7ca4d.jpg.webp');

