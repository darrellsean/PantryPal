-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 08:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

CREATE DATABASE IF NOT EXISTS `pantrypal`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `pantrypal`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for table `twofa_codes`
-- --------------------------------------------------------
CREATE TABLE `twofa_codes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `code` VARCHAR(6) DEFAULT NULL,
  `expires_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `twofa_codes` (`id`, `email`, `code`, `expires_at`) VALUES
(1, 'seanwarkey@gmail.com', '816360', '2025-10-14 05:39:20');

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `ID` INT(10) NOT NULL AUTO_INCREMENT,
  `firstName` VARCHAR(50) NOT NULL,
  `lastName` VARCHAR(50) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `twofa_enabled` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`ID`, `firstName`, `lastName`, `email`, `password`, `twofa_enabled`) VALUES
(1, 'Darrell', 'Warkey', 'seanwarkey@gmail.com', '25d55ad283aa400af464c76d713c07ad', 0),
(2, 'Kendall', 'Roy', 'kroy755@gmail.com', '25d55ad283aa400af464c76d713c07ad', 0),
(4, 'Leo', 'Dicaprio', 'tffbruv@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 1);

-- --------------------------------------------------------
-- Table structure for `food_item`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `food_item` (
  `item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `item_name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) DEFAULT NULL,
  `quantity` VARCHAR(20) DEFAULT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `status` ENUM('Available', 'For Donation', 'For Meal', 'Used', 'Expired') DEFAULT 'Available',
  PRIMARY KEY (`item_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `food_item` (`user_id`, `item_name`, `category`, `quantity`, `expiry_date`) VALUES
(1, 'Milk', 'Dairy', '2 bottles', '2025-10-18'),
(1, 'Chicken Breast', 'Meat', '1 kg', '2025-10-17'),
(2, 'Apples', 'Fruit', '5 pcs', '2025-10-20');

-- --------------------------------------------------------
-- Table structure for `meal_plans`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `meal_plans` (
  `plan_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) NOT NULL,
  `week_start` DATE NOT NULL,
  `meal_type` VARCHAR(50) NOT NULL,
  `custom_meal_name` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`plan_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `meal_plans` (`user_id`, `week_start`, `meal_type`, `custom_meal_name`) VALUES
(1, '2025-10-13', 'Dinner', 'Grilled Chicken Salad'),
(1, '2025-10-13', 'Lunch', 'Pasta Alfredo'),
(2, '2025-10-13', 'Breakfast', 'Omelette and Toast');

-- --------------------------------------------------------
-- Table structure for `notifications`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`notification_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Adjust auto increments
ALTER TABLE `twofa_codes`
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `ID` INT(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
