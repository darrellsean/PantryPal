CREATE DATABASE IF NOT EXISTS pantrypal
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
USE pantrypal;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS food_item (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    quantity VARCHAR(50),
    expiry_date DATE,
    is_for_donation BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

INSERT INTO users (email, password, name)
VALUES ('ihejirikaisioma@gmail.com','Andre2007@', 'Demo User');