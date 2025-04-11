
CREATE DATABASE IF NOT EXISTS secure_php_auth;
USE secure_php_auth;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    address TEXT,
    phone VARCHAR(20),
    password VARCHAR(255),
    token VARCHAR(255),
    token_expiry DATETIME
);
