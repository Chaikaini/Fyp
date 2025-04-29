-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 05:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `admin`

-- --------------------------------------------------------

-- Table structure for table `admin_class`

DROP TABLE IF EXISTS `admin_class`;

CREATE TABLE `admin_class` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `class_id` VARCHAR(20) NOT NULL,
  `subject_id` INT(11) NOT NULL,
  `year` VARCHAR(20) NOT NULL,
  `part` VARCHAR(255) NOT NULL,
  `month` VARCHAR(255) NOT NULL,
  `time` VARCHAR(50) NOT NULL,
  `teacher` VARCHAR(100) NOT NULL,
  `enrolled` INT NOT NULL DEFAULT 0,
  `capacity` INT NOT NULL,
  `status` ENUM('available','unavailable') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `admin_class` (`id`, `class_id`, `subject_id`, `year`, `part`, `month`, `time`, `teacher`, `enrolled`, `capacity`, `status`) VALUES
(1, 'Eng0001', 11245, 'Year 1', 'Part A', 'January - June', 'Tuesday 2:30pm - 4:30pm', 'Mr. John', 0, 30, 'available'),
(2, '', 11245, 'Year 1', 'Part B', 'July - December', 'Tuesday 2:30pm - 4:30pm', 'Mr. John', 0, 30, 'unavailable'),
(3, 'Eng2001', 22534, 'Year 2', 'Part A', 'January - June', 'Monday 5:00pm - 7:00pm', 'Mr. John', 0, 30, 'available'),
(4, '', 22534, 'Year 2', 'Part B', 'July - December', 'Monday 5:00pm - 7:00pm', 'Mr. John', 0, 30, 'unavailable'),
(5, 'Mat0001', 11132, 'Year 1', 'Part A', 'January - June', 'Wednesday 2:30pm - 4:30pm', 'Mr. David', 3, 30, 'available'),
(6, '', 11132, 'Year 1', 'Part B', 'July - December', 'Wednesday 2:30pm - 4:30pm', 'Mr. David', 0, 30, 'unavailable'),
(7, 'Mat2001', 22134, 'Year 2', 'Part A', 'January - June', 'Wednesday 5:00pm - 7:00pm', 'Mr. David', 0, 30, 'available'),
(8, '', 22134, 'Year 2', 'Part B', 'July - December', 'Wednesday 5:00pm - 7:00pm', 'Mr. David', 0, 30, 'unavailable'),
(9, 'Mly0001', 11351, 'Year 1', 'Part A', 'January - June', 'Tuesday 2:30pm - 4:30pm', 'Ms. Lily', 0, 30, 'available'),
(10, '', 11351, 'Year 1', 'Part B', 'July - December', 'Tuesday 2:30pm - 4:30pm', 'Mr. Lily', 0, 30, 'unavailable'),
(11, 'Mly2001', 22345, 'Year 2', 'Part A', 'January - June', 'Tuesday 5:00pm - 7:00pm', 'Ms. Lily', 0, 30, 'available'),
(12, '', 22345, 'Year 2', 'Part B', 'July - December', 'Tuesday 5:30pm - 7:00pm', 'Mr. Lily', 0, 30, 'unavailable');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
