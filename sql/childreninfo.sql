-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 09:13 AM
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
-- Database: `profile`
--

-- --------------------------------------------------------

--
-- Table structure for table `childreninfo`
--

CREATE TABLE `childreninfo` (
  `email` varchar(255) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `kidNumber` varchar(20) NOT NULL,
  `birthday` date DEFAULT NULL,
  `school` varchar(100) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `childreninfo`
--

INSERT INTO `childreninfo` (`email`, `id`, `name`, `gender`, `kidNumber`, `birthday`, `school`, `year`) VALUES
('12345@gmail.com', 1, 'Yuna', 'Female', '170909-01-7788', '2017-09-09', 'SJKC Kulai 2', 'Year 1'),
('12345@gmail.com', 2, 'John Doe', 'Male', '170115-01-2634', '2017-01-15', 'SJKC Kulai 2', 'Year 1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `childreninfo`
--
ALTER TABLE `childreninfo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kidNumber` (`kidNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `childreninfo`
--
ALTER TABLE `childreninfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
