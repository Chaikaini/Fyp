-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 06:25 AM
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
-- Database: `the seeds`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_gender` enum('Male','Female') NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_gender`, `admin_email`, `admin_password`) VALUES
(11111, 'Anua', 'Male', 'anua@gmail.com', '11111');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `attendance_name` varchar(100) NOT NULL,
  `attendance_day` date NOT NULL,
  `attendance_result` enum('Present','Absent','Late') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `class_id` varchar(20) NOT NULL,
  `child_id` int(11) NOT NULL,
  `subject_id` varchar(20) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `child`
--

CREATE TABLE `child` (
  `child_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `child_name` varchar(100) NOT NULL,
  `child_gender` enum('Male','Female') NOT NULL,
  `child_kidNumber` varchar(50) NOT NULL,
  `child_birthday` date NOT NULL,
  `child_school` varchar(100) NOT NULL,
  `child_year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `child`
--

INSERT INTO `child` (`child_id`, `parent_id`, `child_name`, `child_gender`, `child_kidNumber`, `child_birthday`, `child_school`, `child_year`) VALUES
(1, 1, 'Yuna', 'Female', '180909-01-7788', '2017-09-09', 'Kulai 1', 1),
(2, 2, 'John', 'Male', '180101-04-5533', '2018-01-01', 'Kulai 2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` varchar(20) NOT NULL,
  `subject_id` varchar(20) NOT NULL,
  `part_id` int(11) NOT NULL,
  `class_time` text NOT NULL,
  `class_capacity` int(11) NOT NULL,
  `class_enrolled` int(11) NOT NULL,
  `class_status` enum('Available','Unavailable','Full') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `subject_id`, `part_id`, `class_time`, `class_capacity`, `class_enrolled`, `class_status`) VALUES
('Eng0001', '11245', 1, 'Tuesday 2:30pm - 4:30pm', 30, 0, 'Available'),
('Eng0002', '11245', 2, 'Tuesday 2:30pm - 4:30pm', 30, 0, 'Unavailable'),
('Eng2001', '22534', 1, 'Monday 5:00pm - 7:00pm', 30, 0, 'Available'),
('Eng2002', '22534', 2, 'Monday 5:00pm - 7:00pm', 30, 0, 'Unavailable'),
('Mat0001', '11132', 1, 'Wednesday 2:30pm - 4:30pm', 30, 0, 'Available'),
('Mat0002', '11132', 2, 'Wednesday 2:30pm - 4:30pm', 30, 0, 'Unavailable'),
('Mat2001', '22134', 1, 'Wednesday 5:00pm - 7:00pm', 30, 0, 'Available'),
('Mat2002', '22134', 2, 'Wednesday 5:00pm - 7:00pm', 30, 0, 'Unavailable'),
('Mly0001', '11351', 1, 'Tuesday 2:30 pm - 4:30 pm', 30, 2, 'Available'),
('Mly0002', '11351', 2, 'Tuesday 2:30pm - 4:30pm', 30, 0, 'Unavailable'),
('Mly2001', '22345', 1, 'Tuesday 5:00pm - 7:00pm', 30, 0, 'Available'),
('Mly2002', '22345', 2, 'Tuesday 5:30pm - 7:00pm', 30, 0, 'Unavailable');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `class_id` varchar(10) DEFAULT NULL,
  `subject_id` varchar(50) DEFAULT NULL,
  `comment_rating` int(11) DEFAULT NULL,
  `comment_text` text DEFAULT NULL,
  `comment_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `class_id` varchar(20) NOT NULL,
  `notification_category` varchar(100) NOT NULL,
  `notification_content` text NOT NULL,
  `notification_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `parent_id` int(11) NOT NULL,
  `parent_name` varchar(100) NOT NULL,
  `ic_number` varchar(20) NOT NULL,
  `parent_email` varchar(100) NOT NULL,
  `parent_address` text NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `phone_number2` varchar(20) DEFAULT NULL,
  `parent_relationship` enum('Mother','Father','Guardian') NOT NULL,
  `parent_gender` enum('Male','Female') NOT NULL,
  `parent_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`parent_id`, `parent_name`, `ic_number`, `parent_email`, `parent_address`, `phone_number`, `phone_number2`, `parent_relationship`, `parent_gender`, `parent_password`) VALUES
(1, 'Aini', '900123-01-8899', '12345@gmail.com', '1122 Jalan 555, Taman Indahpura 81000 Kulai Johor', '012-8278590', '', 'Mother', 'Female', '$2y$10$1ziIP7HagY0m9.2KfhAONulxSqDEJplMptJze.1KeiMIRu8/l5Mvm'),
(2, 'WW', '930123-05-8899', '123@gmail.com', '2222 Jalan Gemilang', '012-3334455', '', 'Guardian', 'Female', '$2y$10$AQU/DCCmeW9Ce6S90.RUYejCFAFTxfAk0J05I02WFyleDRhh9HsPm');

-- --------------------------------------------------------

--
-- Table structure for table `part`
--

CREATE TABLE `part` (
  `part_id` int(11) NOT NULL,
  `part_name` varchar(100) NOT NULL,
  `part_duration` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `part`
--

INSERT INTO `part` (`part_id`, `part_name`, `part_duration`) VALUES
(1, 'Part A', 'January - June'),
(2, 'Part B', 'July - December');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `payment_total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `master_card_number` varchar(16) DEFAULT NULL,
  `payment_status` enum('Pending','Completed','Failed') NOT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `parent_id`, `payment_total_amount`, `payment_method`, `master_card_number`, `payment_status`, `payment_time`) VALUES
(1, 1, 510.00, 'Credit Card', '111111111', 'Completed', '2025-04-17 07:25:04'),
(2, 2, 510.00, 'Credit Card', '11111111', 'Completed', '2025-04-17 11:56:09');

-- --------------------------------------------------------

--
-- Table structure for table `registration_class`
--

CREATE TABLE `registration_class` (
  `registration_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `class_id` varchar(20) NOT NULL,
  `child_id` int(11) NOT NULL,
  `subject_id` varchar(20) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration_class`
--

INSERT INTO `registration_class` (`registration_id`, `parent_id`, `class_id`, `child_id`, `subject_id`, `teacher_id`, `payment_id`) VALUES
(1, 1, 'Mly0001', 1, '11351', 12345, 1),
(2, 2, 'Mly0001', 2, '11351', 12345, 2);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` varchar(20) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `year` text NOT NULL,
  `subject_price` decimal(8,2) NOT NULL,
  `subject_description` text DEFAULT NULL,
  `subject_image` varchar(255) DEFAULT NULL,
  `page` varchar(255) NOT NULL,
  `page_generated` tinyint(1) NOT NULL,
  `page_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `teacher_id`, `admin_id`, `subject_name`, `year`, `subject_price`, `subject_description`, `subject_image`, `page`, `page_generated`, `page_path`) VALUES
('11132', 12123, 11111, 'Math', 'Year 1', 510.00, NULL, 'img/math.jpg', 'Year 1 Math class.html', 0, ''),
('11245', 12233, 11111, 'English', 'Year 1', 510.00, NULL, 'img/english.jpg', 'Year 1 English class.html', 0, ''),
('11351', 12345, 11111, 'Melayu', 'Year 1', 510.00, NULL, 'img/malay1.jpg', 'Year 1 Malay class.html', 0, ''),
('22134', 12123, 11111, 'Math', 'Year 2', 510.00, NULL, 'img/math.jpg', 'Year 2 Math class.html', 0, ''),
('22345', 12345, 11111, 'Melayu', 'Year 2', 510.00, NULL, 'img/malay1.jpg', 'Year 2 Malay class.html', 0, ''),
('22534', 12233, 11111, 'English', 'Year 2', 510.00, NULL, 'img/english.jpg', 'Year 2 English class.html', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `teacher_gender` enum('Male','Female') NOT NULL,
  `teacher_email` varchar(100) NOT NULL,
  `teacher_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `teacher_name`, `teacher_gender`, `teacher_email`, `teacher_password`) VALUES
(12123, 'Mr. David', 'Male', '12123@gmail.com', '12123'),
(12233, 'Mr. John', 'Male', '12233@gmail.com', '12233'),
(12345, 'Ms. Lily', 'Female', 'lily@gmail.com', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `child`
--
ALTER TABLE `child`
  ADD PRIMARY KEY (`child_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `part_id` (`part_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `part`
--
ALTER TABLE `part`
  ADD PRIMARY KEY (`part_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `registration_class`
--
ALTER TABLE `registration_class`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `child`
--
ALTER TABLE `child`
  MODIFY `child_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `part`
--
ALTER TABLE `part`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `registration_class`
--
ALTER TABLE `registration_class`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration_class` (`registration_id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`),
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`child_id`) REFERENCES `child` (`child_id`),
  ADD CONSTRAINT `cart_ibfk_4` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`),
  ADD CONSTRAINT `cart_ibfk_5` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `child`
--
ALTER TABLE `child`
  ADD CONSTRAINT `child_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `class_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`),
  ADD CONSTRAINT `class_ibfk_2` FOREIGN KEY (`part_id`) REFERENCES `part` (`part_id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`);

--
-- Constraints for table `registration_class`
--
ALTER TABLE `registration_class`
  ADD CONSTRAINT `registration_class_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`),
  ADD CONSTRAINT `registration_class_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`),
  ADD CONSTRAINT `registration_class_ibfk_3` FOREIGN KEY (`child_id`) REFERENCES `child` (`child_id`),
  ADD CONSTRAINT `registration_class_ibfk_4` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`),
  ADD CONSTRAINT `registration_class_ibfk_5` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `registration_class_ibfk_6` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`);

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `subject_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
