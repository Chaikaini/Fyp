-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 01:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tuition_centre`
--

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `year` varchar(50) NOT NULL,
  `teacher` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `year`, `teacher`, `price`, `rating`, `image`, `page`) VALUES
(11132, 'Year 1 Math', 'Year 1', 'Mr. David', 510.00, 4.3, 'img/math.jpg', 'Year 1 Math class.html'),
(11245, 'Year 1 English', 'Year 1', 'Mr. John', 510.00, 4.6, 'img/english.jpg', 'Year 1 English class.html'),
(11351, 'Year 1 Melayu', 'Year 1', 'Ms. Lily', 510.00, 4.5, 'img/malay1.jpg', 'Year 1 Malay class.html'),
(22134, 'Year 2 Math', 'Year 2', 'Mr. David', 510.00, 4.8, 'img/math.jpg', 'Year 2 Math class.html'),
(22345, 'Year 2 Melayu', 'Year 2', 'Ms. Lily', 510.00, 4.2, 'img/malay1.jpg', 'Year 2 Malay class.html'),
(22534, 'Year 2 English', 'Year 2', 'Mr. John', 510.00, 4.5, 'img/english.jpg', 'Year 2 English class.html');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23416;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
