-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 04:12 AM
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
-- Table structure for table `admin_subject`
--

CREATE TABLE `admin_subject` (
  `subject_ID` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `year` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `rating` decimal(3,1) NOT NULL DEFAULT 0.0,
  `image` varchar(255) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_subject`
--

INSERT INTO `admin_subject` (`subject_ID`, `subject`, `year`, `price`, `description`, `rating`, `image`, `page`) VALUES
(11132, 'Year 1 Math', 'Year 1', 510.00, '', 4.6, 'img/math.jpg', 'Year 1 Math class.html'),
(11245, 'Year 1 English', 'Year 1', 510.00, NULL, 4.5, 'img/english.jpg', 'Year 1 English class.html'),
(11351, 'Year 1 Melayu', 'Year 1', 510.00, NULL, 4.3, 'img/malay1.jpg', 'Year 1 Malay class.html'),
(22134, 'Year 2 Math ', 'Year 2', 510.00, NULL, 4.5, 'img/math.jpg', 'Year 2 Math class.html'),
(22345, 'Year 2 Melayu', 'Year 2', 510.00, NULL, 4.2, 'img/malay1.jpg', 'Year 2 Malay class.html'),
(22534, 'Year 2 English', 'Year 2', 510.00, NULL, 4.8, 'img/english.jpg', 'Year 2 English class.html');

--
-- Triggers `admin_subject`
--
DELIMITER $$
CREATE TRIGGER `after_admin_subject_insert` 
AFTER INSERT ON `admin_subject` 
FOR EACH ROW 
BEGIN
    INSERT INTO admin.admin_subject (id, name, year, price, rating, image, page)
    VALUES (NEW.subject_ID, NEW.subject, NEW.year, NEW.price, NEW.rating, NEW.image, NEW.page)
    ON DUPLICATE KEY UPDATE
        name = NEW.subject,
        year = NEW.year,
        price = NEW.price,
        rating = NEW.rating,
        image = NEW.image,
        page = NEW.page;
END
$$
DELIMITER ;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_subject`
--
ALTER TABLE `admin_subject`
  ADD PRIMARY KEY (`subject_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
