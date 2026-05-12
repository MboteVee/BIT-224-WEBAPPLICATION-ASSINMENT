CREATE DATABASE IF NOT EXISTS real_estate_db;
USE real_estate_db;

--create user database
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('buyer','seller') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NULL;

);


--create properties database
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    location VARCHAR(150) NOT NULL,
    bedrooms INT,
    bathrooms INT,
    property_type VARCHAR(50),
    image VARCHAR(255),
    status VARCHAR(20) DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (seller_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
-- ── SELLERS ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS sellers (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120)  NOT NULL,
  email       VARCHAR(180)  NOT NULL UNIQUE,
  phone       VARCHAR(30)   NOT NULL,
  password    VARCHAR(255)  NOT NULL,  -- bcrypt hash
  avatar      VARCHAR(255)  DEFAULT NULL,
  created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) 
 
-- ── PROPERTIES ──────────────────────────────────
CREATE TABLE IF NOT EXISTS properties (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  seller_id   INT           NOT NULL,
  title       VARCHAR(200)  NOT NULL,
  price       VARCHAR(60)   NOT NULL,   -- stored as formatted string e.g. "KSh 8,500,000"
  listing_type ENUM('sale','rent') NOT NULL DEFAULT 'sale',
  location    VARCHAR(200)  NOT NULL,
  bedrooms    TINYINT       DEFAULT 0,
  bathrooms   TINYINT       DEFAULT 0,
  size_sqm    SMALLINT      DEFAULT 0,
  description TEXT          DEFAULT NULL,
  status      ENUM('active','sold','rented','hidden') DEFAULT 'active',
  views       INT           DEFAULT 0,
  created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE
) 
 
-- ── PROPERTY IMAGES ─────────────────────────────
CREATE TABLE IF NOT EXISTS property_images (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT           NOT NULL,
  url         VARCHAR(255)  NOT NULL,
  is_primary  TINYINT(1)    DEFAULT 0,
  created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)
 
-- ── INQUIRIES ───────────────────────────────────
CREATE TABLE IF NOT EXISTS inquiries (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT           NOT NULL,
  buyer_name  VARCHAR(120)  NOT NULL,
  buyer_email VARCHAR(180)  NOT NULL,
  buyer_phone VARCHAR(30)   DEFAULT NULL,
  message     TEXT          NOT NULL,
  is_read     TINYINT(1)    DEFAULT 0,
  created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)