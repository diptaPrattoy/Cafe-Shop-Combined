-- Cafe Shop Database (Unified Users Table Version)
-- phpMyAdmin SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `cafe_db`;
USE `cafe_db`;

-- --------------------------------------------------------
-- Unified users table
-- --------------------------------------------------------

CREATE TABLE `all_users` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `number` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(100) NOT NULL,
  `address` VARCHAR(500) DEFAULT NULL,
  `age` INT(3) DEFAULT NULL,
  `sex` VARCHAR(15) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT 'default.png',
  `type` ENUM('admin','employee','user') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Sample data
INSERT INTO `all_users`
(`id`, `username`, `email`, `number`, `password`, `address`, `age`, `sex`, `phone`, `profile_image`, `type`) VALUES
(1, 'admin', NULL, NULL, '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', NULL, NULL, NULL, NULL, 'default.png', 'admin'),
(2, 'mithun', NULL, NULL, '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', NULL, NULL, NULL, NULL, 'default.png', 'admin'),
(3, 'adminmk', NULL, NULL, '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', NULL, NULL, NULL, NULL, 'default.png', 'admin'),
(4, 'employee', 'mk@gmial.com', '1521509030', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 'badda, Dhaka 1212', 22, 'male', '1521509030', 'default.png', 'employee'),
(5, 'robin', '6884887987@gmial.com', '1521509031', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 'dhaka', 25, 'male', '1521509031', 'default.png', 'employee'),
(6, 'mithun', 'mi@gmail.com', '0152150903', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', '12, 5, Badda, xyz, dhaka, xyz, bangladesh - 1212', NULL, NULL, NULL, 'default.png', 'user');

-- --------------------------------------------------------
-- Cart
-- --------------------------------------------------------

CREATE TABLE `cart` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `user_id` INT(100) NOT NULL,
  `pid` INT(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `price` INT(10) NOT NULL,
  `quantity` INT(10) NOT NULL,
  `image` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `all_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(28, 6, 19, 'Espresso Con Panna', 20, 8, 'espresso-con-panna-1659544996.webp'),
(29, 6, 14, 'Red Eye', 20, 1, 'red-eye-1659544996.webp');

-- --------------------------------------------------------
-- Messages
-- --------------------------------------------------------

CREATE TABLE `messages` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `user_id` INT(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `number` VARCHAR(12) NOT NULL,
  `message` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `all_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `messages` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(1, 6, 'mithun', 'dfvvfdgf@gmaidsd.com', '845595', 'good'),
(2, 4, 'mxn vhxbcv', 'mk@gmial.com', '684684684', 'hcjhscbasjcabs'),
(3, 6, 'asif', 'mk@gmail.com', '89898', 'good site');

-- --------------------------------------------------------
-- Orders
-- --------------------------------------------------------

CREATE TABLE `orders` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `user_id` INT(100) NOT NULL,
  `name` VARCHAR(20) NOT NULL,
  `number` VARCHAR(20) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `method` VARCHAR(50) NOT NULL,
  `address` VARCHAR(500) NOT NULL,
  `total_products` VARCHAR(1000) NOT NULL,
  `total_price` INT(100) NOT NULL,
  `placed_on` DATE NOT NULL DEFAULT current_timestamp(),
  `payment_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `all_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(7, 11, 'dipto', '0152150903', 'di@gmail.com', 'credit card', '12, 5, Badda, xyz, dhaka, xyz, bangladesh - 1212', 'Cortado (20 x 1) - Cappuccino (20 x 1) - Macchiato (20 x 1) - ', 60, '2022-09-18', 'pending'),
(8, 7, 'niloy', '0152150903', 'ni@gmail.com', 'bkash', '12, 5, Badda, xyz, dhaka, xyz, bangladesh - 1212', 'Cortado (20 x 1) - ', 20, '2022-09-18', 'pending');

-- --------------------------------------------------------
-- Products
-- --------------------------------------------------------

CREATE TABLE `products` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `price` INT(10) NOT NULL,
  `image` VARCHAR(100) NOT NULL,
  `popularity` INT(8) DEFAULT NULL,
  `disprice` INT(10) DEFAULT NULL,
  `description` VARCHAR(1500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`id`, `name`, `category`, `price`, `image`, `popularity`, `disprice`, `description`) VALUES
(11, 'Cappuccino', 'coffee', 200, 'cappuccino-1659544996.png', 10, NULL, NULL),
(12, 'Cortado', 'coffee', 20, 'cortado-1659544996.webp', NULL, NULL, NULL),
(13, 'Latte', 'coffee', 20, 'latte-1659544996.webp', NULL, NULL, NULL),
(14, 'Red Eye', 'coffee', 20, 'red-eye-1659544996.webp', NULL, NULL, NULL),
(15, 'Mocha', 'coffee', 20, 'mocha-1659544996.webp', NULL, NULL, NULL),
(16, 'Raf', 'coffee', 20, 'raf-1659544996.webp', NULL, NULL, NULL),
(17, 'Macchiato', 'coffee', 20, 'macchiato-1659544996.webp', 8, NULL, NULL),
(18, 'Cold Brew', 'coffee', 20, 'cold-brew-1659544996.webp', NULL, NULL, NULL),
(19, 'Espresso Con Panna', 'coffee', 20, 'espresso-con-panna-1659544996.webp', 5, NULL, NULL),
(20, 'Café Cubano', 'coffee', 20, 'cafe-cubano-1659544996.webp', NULL, NULL, NULL),
(21, 'Espresso Romano', 'coffee', 20, 'espresso-romano-1659544996.webp', NULL, NULL, NULL),
(22, 'Long Black', 'coffee', 20, 'long-black-1659544996.webp', 6, NULL, NULL),
(23, 'Caffè Breve', 'coffee', 20, 'caffe-breve-1659544996.webp', NULL, NULL, NULL),
(24, 'Affogato', 'coffee', 20, 'affogato-1659544996.webp', NULL, NULL, NULL),
(25, 'Quad shots', 'coffee', 20, 'quad-shots-1659544996.webp', NULL, NULL, NULL),
(26, 'Mexican coffee', 'coffee', 20, 'mexican-coffee-1659544996.webp', NULL, NULL, NULL);

(100, 'Espresso', 'coffee', 100, 'delicious-quality-coffee-cup.png', 15, NULL, NULL);

-- --------------------------------------------------------
-- Ratings
-- --------------------------------------------------------

CREATE TABLE `rating` (
  `id` INT(100) NOT NULL AUTO_INCREMENT,
  `user_id` INT(100) NOT NULL,
  `User_name` VARCHAR(20) NOT NULL,
  `rating` INT(2) NOT NULL,
  `review` VARCHAR(1500) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `all_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
