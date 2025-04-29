-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 02:14 PM
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
-- Table structure for table `classdetail`
--

CREATE TABLE `classdetail` (
  `id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `part` varchar(255) NOT NULL,
  `month` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `enrolled` int(11) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classdetail`
--

INSERT INTO `classdetail` (`id`, `class_name`, `part`, `month`, `time`, `capacity`, `enrolled`, `status`) VALUES
(1, 'Year 1 English', 'Part A', 'January - June', 'Monday 2.30pm-4.30pm', 30, 20, 'available'),
(2, 'Year 1 English', 'Part B', 'July - December', 'Monday 2.30pm-4.30pm', 30, 0, 'unavailable'),
(3, 'Year 1 Melayu', 'Part A', 'January - June', 'Tuesday 2.30pm-4.30pm', 30, 20, 'available'),
(4, 'Year 1 Melayu', 'Part B', 'July - December', 'Tuesday 2.30pm-4.30pm', 30, 0, 'unavailable'),
(5, 'Year 1 Math', 'Part A', 'January - June', 'Wednesday 2.30pm-4.30pm', 30, 20, 'available'),
(6, 'Year 1 Math', 'Part B', 'July - December', 'Wednesday 2.30pm-4.30pm', 30, 0, 'unavailable'),
(7, 'Year 2 English', 'Part A', 'January - June', 'Monday 5.00pm-7.00pm', 30, 20, 'available'),
(8, 'Year 2 English', 'Part B', 'July - December', 'Monday 5.00pm-7.00pm', 30, 0, 'unavailable'),
(9, 'Year 2 Melayu', 'Part A', 'January - June', 'Tuesday 5.00pm-7.00pm', 30, 20, 'available'),
(10, 'Year 2 Melayu', 'Part B', 'July - December', 'Tuesday 5.00pm-7.00pm', 30, 0, 'unavailable'),
(11, 'Year 2 Math', 'Part A', 'January - June', 'Wednesday 5.00pm-7.00pm', 30, 20, 'available'),
(12, 'Year 2 Math', 'Part B', 'July - December', 'Wednesday 5.00pm-7.00pm', 30, 0, 'unavailable');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classdetail`
--
ALTER TABLE `classdetail`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classdetail`
--
ALTER TABLE `classdetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
