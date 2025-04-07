-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
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

--
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_class`
--

CREATE TABLE `admin_class` (
  `id` int(11) NOT NULL,
  `class_id` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `part` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `time` varchar(50) NOT NULL,
  `teacher` varchar(100) NOT NULL,
  `capacity` varchar(10) NOT NULL,
  `status` enum('available','unavailable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_class`
--

INSERT INTO `admin_class` (`id`, `class_id`, `subject_id`, `year`, `part`, `month`, `time`, `teacher`, `capacity`, `status`) VALUES
(1, 'Eng0001', 11245, 'Year 1', 'Part A', 'January - June', 'Tuesday 2:30pm - 4:30pm', 'Mr. John', '0/30', 'available'),
(2, '', 11245, 'Year 1', 'Part B', 'July - December', 'Tuesday 2:30pm - 4:30pm', 'Mr. John', '0/30', 'unavailable'),
(3, 'Eng2001', 22534, 'Year 2', 'Part A', 'January - June\r\n', 'Monday 5:00pm - 7:00pm', 'Mr. John', '0/30', 'available'),
(4, '', 22534, 'Year 2', 'Part B', 'July - December', 'Mondayday 5:00pm - 7:00pm', 'Mr. John', '0/30', 'unavailable'),
(5, 'Mat0001', 11132, 'Year 1', 'Part A', 'January - June', 'Wednesday 2:30pm - 4:30pm', 'Mr. David', '3/30', 'available'),
(6, '', 11132, 'Year 1', 'Part B', 'July - December', 'Wednesday 2:30pm - 4:30pm', 'Mr. David', '0/30', 'unavailable'),
(7, 'Mat2001', 22134, 'Year 2', 'Part A', 'January - June', 'Wednesday 5:00pm - 7:00pm', 'Mr. David', '0/30', 'available'),
(8, '', 22134, 'Year 2', 'Part B', 'July - December', 'Wednesday 5:00pm - 7:00pm', 'Mr. David', '0/30', 'unavailable'),
(9, 'Mly0001', 11351, 'Year 1', 'Part A', 'January - June', 'Tuesday 2:30pm - 4:30pm', 'Ms. Lily', '0/30', 'available'),
(10, '', 11351, 'Year 1', 'Part B', 'July - December', 'Tuesday 2:30pm - 4:30pm', 'Mr. Lily', '0/30', 'unavailable'),
(11, 'Mly2001', 22345, 'Year 2', 'Part A', 'January - June', 'Tuesday 5:00pm - 7:00pm', 'Ms. Lily', '1/30', 'available'),
(12, '', 22345, 'Year 2', 'Part B', 'July - December', 'Tuesday 5:30pm - 7:00pm', 'Mr. Lily', '0/30', 'unavailable');

--
-- Triggers `admin_class`
--
DELIMITER $$
CREATE TRIGGER `after_admin_class_delete` AFTER DELETE ON `admin_class` FOR EACH ROW BEGIN
    -- 删除 tuition_centre.classdetail 中的相关记录（Part A 和 Part B）
    DELETE FROM tuition_centre.classdetail
    WHERE class_name = CONCAT(OLD.year, ' ', 
                              CASE 
                                WHEN OLD.subject_id BETWEEN 11000 AND 11999 THEN 'English'
                                WHEN OLD.subject_id BETWEEN 22000 AND 22999 THEN 'Malay'
                                WHEN OLD.subject_id BETWEEN 33000 AND 33999 THEN 'Math'
                                ELSE 'Unknown'
                              END)
    AND part IN ('Part A', 'Part B'); -- 删除 Part A 和 Part B
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_class`
--
ALTER TABLE `admin_class`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_class`
--
ALTER TABLE `admin_class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
