-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2025 at 04:36 AM
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
  `admin_address` varchar(255) DEFAULT NULL,
  `admin_phone_number` varchar(20) DEFAULT NULL,
  `admin_password` varchar(255) NOT NULL,
  `role` enum('Admin','Super Admin') NOT NULL DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_gender`, `admin_email`, `admin_address`, `admin_phone_number`, `admin_password`, `role`) VALUES
(11111, 'kaini', 'Female', 'chaikaini@gmail.com', 'Jalan 1', '0167827196', '$2y$10$lQlGrowkkRVMDwrfxWOTveKgHOLxPIdZgaz/qrMs6lJkCIvPEaM/e', 'Super Admin'),
(11112, 'jiaxin', 'Female', 'jiaxin@gmail.com', 'Jalan Putra 1', '0127818656', '$2y$10$TJwacQClqzn00.t5QwJpoOUo/RjVZQAYUdtK22DKoAcKuqdTc2.ja', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `class_id` varchar(20) NOT NULL,
  `child_id` int(11) NOT NULL,
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `parent_id`, `class_id`, `child_id`, `deleted`) VALUES
(1, 1, 'Mat0001', 1, 1),
(2, 3, 'Eng2001', 3, 1),
(3, 12, 'Mat0001', 4, 1),
(4, 12, 'Eng0001', 4, 1),
(5, 12, 'Mly0001', 4, 1),
(6, 12, 'Mat2001', 5, 1),
(7, 12, 'Mly2001', 5, 1),
(8, 12, 'Eng2001', 5, 1),
(9, 4, 'Mat0001', 6, 1),
(10, 4, 'Eng0001', 6, 1),
(11, 4, 'Mly0001', 6, 1),
(12, 4, 'Mat2001', 7, 1),
(13, 4, 'Mly2001', 7, 1),
(14, 4, 'Eng2001', 7, 1),
(15, 5, 'Mat0001', 8, 1),
(16, 5, 'Eng0001', 8, 1),
(17, 5, 'Mly0001', 8, 1),
(18, 5, 'Mat2001', 9, 1),
(19, 5, 'Mly2001', 9, 1),
(20, 5, 'Eng2001', 9, 1),
(21, 1, 'Mly0001', 1, 1),
(22, 1, 'Eng0001', 1, 1);

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
  `child_year` int(11) NOT NULL,
  `child_image` varchar(255) NOT NULL,
  `child_register_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `child`
--

INSERT INTO `child` (`child_id`, `parent_id`, `child_name`, `child_gender`, `child_kidNumber`, `child_birthday`, `child_school`, `child_year`, `child_image`, `child_register_date`) VALUES
(1, 1, 'Yuna Lim Xi Yue', 'Female', '180909-01-7788', '2018-09-09', 'SJKC Kulai 1', 1, 'uploads/child_images/68353bc32a6fd-100087514_708500463057448_7612975320529895424_o-1-e1591159367508.jpg', '2025-01-01 12:26:05'),
(2, 2, 'John Lim Wu Le', 'Male', '180101-04-5533', '2018-01-01', 'SJKC Kulai 2', 1, 'uploads/child_images/683c5cde04e1d-images.jpg', '2025-01-01 12:26:05'),
(3, 3, 'Chen Shu Hui', 'Female', '170909-01-6655', '2017-09-09', 'SJKC Kulai 2', 2, 'uploads/child_images/683c59b16a202-center.png', '2025-01-01 21:23:36'),
(4, 12, 'Soh Yi Yi', 'Female', '180123-01-7654', '2018-01-23', 'SJKC Kulai 1', 1, 'uploads/child_images/child_683dbfe4d3a563.40233732.jpg', '2025-01-01 23:14:44'),
(5, 12, 'Soh Yi En', 'Female', '170112-01-1234', '2017-01-12', 'SJKC Kulai 1', 2, 'uploads/child_images/child_683dc04598ffe7.15380968.jpg', '2025-01-01 23:16:21'),
(6, 4, 'Hong Ti Xing', 'Female', '180523-01-1531', '2018-05-23', 'SJKC Kulai 1', 1, 'uploads/child_images/child_683dc1e707a000.04566244.jpg', '2025-01-01 23:23:19'),
(7, 4, 'Lim En Rui', 'Female', '170923-01-1465', '2017-09-23', 'SJKC Kulai 1', 2, 'uploads/child_images/child_683dc2193787a7.47157231.jpg', '2025-01-01 23:24:09'),
(8, 5, 'Koh Ming Le ', 'Female', '180523-01-1603', '2018-05-23', 'SJKC Kulai 1', 1, 'uploads/child_images/child_683dc30fac1df7.31994522.jpg', '2025-01-01 23:28:15'),
(9, 5, 'Tan Kang Fu', 'Male', '170912-01-1123', '2017-09-12', 'SJKC Kulai 1', 2, 'uploads/child_images/child_683dc350bd4443.07862724.jpg', '2025-01-01 23:29:20');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` varchar(20) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 1,
  `subject_id` varchar(20) NOT NULL,
  `part_id` int(11) NOT NULL,
  `teacher_id` varchar(20) NOT NULL,
  `class_term` varchar(20) NOT NULL,
  `class_time` text NOT NULL,
  `class_venue` varchar(100) NOT NULL DEFAULT '',
  `class_capacity` int(11) NOT NULL,
  `class_enrolled` int(11) NOT NULL,
  `class_status` enum('Available','Unavailable','Full') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `admin_id`, `subject_id`, `part_id`, `teacher_id`, `class_term`, `class_time`, `class_venue`, `class_capacity`, `class_enrolled`, `class_status`) VALUES
('Eng0001', 11112, '11245', 1, '12233', '2025', 'Monday 2:30pm - 4:30pm', 'Room 1', 30, 4, 'Available'),
('Eng0002', 11111, '11245', 2, '12233', '2025', 'Monday 2:30pm - 4:30pm', 'Room 1', 30, 0, 'Unavailable'),
('Eng2001', 11111, '22534', 1, '12345', '2025', 'Monday 5:00pm - 7:00pm', 'Room 1', 30, 4, 'Available'),
('Eng2002', 11111, '22534', 2, '12345', '2025', 'Monday 5:00pm - 7:00pm', 'Room 1', 30, 0, 'Unavailable'),
('Mat0001', 11111, '11132', 1, '12123', '2025', 'Wednesday 2:30pm - 4:30pm', 'Room 1', 30, 3, 'Available'),
('Mat0002', 11111, '11132', 2, '12123', '2025', 'Wednesday 2:30pm - 4:30pm', 'Room 1', 30, 0, 'Unavailable'),
('Mat2001', 11111, '22134', 1, '12347', '2025', 'Wednesday 5:00pm - 7:00pm', 'Room 2', 30, 3, 'Available'),
('Mat2002', 11111, '22134', 2, '12347', '2025', 'Wednesday 5:00pm - 7:00pm', 'Room 2', 30, 0, 'Unavailable'),
('Mly0001', 11111, '11351', 1, '12345', '2025', 'Tuesday 2:30pm - 4:30pm', 'Room 2', 30, 4, 'Available'),
('Mly0002', 11111, '11351', 2, '12345', '2025', 'Tuesday 2:30pm - 4:30pm', 'Room 2', 30, 0, 'Unavailable'),
('Mly2001', 11111, '22345', 1, '12347', '2025', 'Tuesday 5:00pm - 7:00pm', 'Room 2', 30, 3, 'Available'),
('Mly2002', 11111, '22345', 2, '12347', '2025', 'Tuesday 5:30pm - 7:00pm', 'Room 2', 30, 0, 'Unavailable');

-- --------------------------------------------------------

--
-- Table structure for table `credit_cards`
--

CREATE TABLE `credit_cards` (
  `credit_card_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `card_number` varchar(255) NOT NULL,
  `expiry_date` varchar(5) NOT NULL,
  `last_four` varchar(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_cards`
--

INSERT INTO `credit_cards` (`credit_card_id`, `parent_id`, `card_number`, `expiry_date`, `last_four`, `created_at`) VALUES
(1, 1, '4111111111111111', '12/25', '1122', '2025-04-17 07:25:04'),
(2, 2, '5111111111111111', '12/25', '9988', '2025-04-17 11:56:09'),
(5, 3, '$2y$10$XsvTk3VMgWIPFwUNGUvCrOgld2a225pONVkqQ6jtVa31kICOlGbcO', '08/25', '5555', '2025-06-01 13:26:16'),
(6, 12, '$2y$10$kF48Ui6FouWPAnClTz06oO1eTTDHHQyROa7c4W/JJ1oP9iuuxx62O', '12/25', '4567', '2025-06-02 15:18:31'),
(7, 4, '$2y$10$IPXIr0zf.pR2IYFNBfFlTekpf8hEH5i6OmKgOJzLgqG8Wh7bFL9BS', '12/25', '5678', '2025-06-02 15:25:31'),
(8, 5, '$2y$10$LJNE6wB35fg0v6BhL98uhu/UrhD/OktYKa2PRBdh1raEBAmiOtE4W', '12/25', '3045', '2025-06-02 15:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `exam_result`
--

CREATE TABLE `exam_result` (
  `exam_result_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `class_id` varchar(20) DEFAULT NULL,
  `exam_result_midterm` decimal(5,2) DEFAULT NULL,
  `exam_result_final` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_result`
--

INSERT INTO `exam_result` (`exam_result_id`, `child_id`, `class_id`, `exam_result_midterm`, `exam_result_final`) VALUES
(1, 4, 'Mly0001', 77.00, 88.00),
(2, 6, 'Mly0001', 88.00, 85.00),
(3, 8, 'Mly0001', 79.00, 67.00),
(4, 1, 'Mly0001', 67.00, 88.00);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_type` enum('Teacher','Parent','Class') NOT NULL,
  `class_id` varchar(20) DEFAULT NULL,
  `notification_title` varchar(255) NOT NULL,
  `notification_content` text NOT NULL,
  `notification_document` varchar(255) DEFAULT NULL,
  `notification_created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `sender_id`, `recipient_type`, `class_id`, `notification_title`, `notification_content`, `notification_document`, `notification_created_at`) VALUES
(1, 12345, 'Class', 'Mly0001', '29/5（Thusrday）Class Cancelled', 'Dear parents and students, the class on 29/5 (Thursday) is cancelled due to my medical leave. A replacement class will be held on 30/5 (Friday) from 2:30 PM to 4:30 PM. Please contact me if that have any issue and problem. Thank you for your understanding.', NULL, '2025-05-26 18:33:30');

-- --------------------------------------------------------

--
-- Table structure for table `notification_receiver`
--

CREATE TABLE `notification_receiver` (
  `receiver_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `recipient_type` enum('Teacher','Parent','Class') NOT NULL,
  `read_status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_receiver`
--

INSERT INTO `notification_receiver` (`receiver_id`, `notification_id`, `parent_id`, `teacher_id`, `recipient_type`, `read_status`) VALUES
(1, 1, 1, NULL, 'Class', 'unread');

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
  `phone_number2` varchar(20) NOT NULL,
  `parent_relationship` enum('Mother','Father','Guardian') NOT NULL,
  `parent_gender` enum('Male','Female') NOT NULL,
  `parent_image` varchar(255) NOT NULL,
  `parent_name2` varchar(100) NOT NULL,
  `parent_relationship2` enum('Mother','Father','Guardian') NOT NULL,
  `parent_num2` varchar(20) NOT NULL,
  `parent_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`parent_id`, `parent_name`, `ic_number`, `parent_email`, `parent_address`, `phone_number`, `phone_number2`, `parent_relationship`, `parent_gender`, `parent_image`, `parent_name2`, `parent_relationship2`, `parent_num2`, `parent_password`) VALUES
(1, 'Aini Li Yu Xi', '900123-01-8899', '12345@gmail.com', '1122 Jalan 555', '012-8278590', '', 'Mother', 'Female', 'uploads/parent_images/68353bb8d6ca2-3887158569.jpg', 'Kewen', 'Guardian', '010-2324567', '$2y$10$1ziIP7HagY0m9.2KfhAONulxSqDEJplMptJze.1KeiMIRu8/l5Mvm'),
(2, 'Choo Fu Kang', '930123-05-8899', '123@gmail.com', '2222 Jalan Gemilang', '012-3334455', '', 'Guardian', 'Female', 'uploads/parent_images/683c5df6c126a-images (1).jpg', 'Nini', 'Mother', '016-7282049', '$2y$10$AQU/DCCmeW9Ce6S90.RUYejCFAFTxfAk0J05I02WFyleDRhh9HsPm'),
(3, 'Goh Wei Ting', '', 'weiting@gmail.com', '1122 Jalan 555, Taman Indahpura 81000 Kulai Johor', '012-666 8877', '', 'Mother', 'Female', '', '', 'Mother', '', '$2y$10$29775lWe1LKaF1Pu4op2n.sP0bwJ4ttLJuFouiIn3hmZJf9Fpwtfy'),
(4, 'Awe Chong Ting', '', 'awe123@gmail.com', 'alan Anggerik, No. 12, 81000 Kulai, Johor', '012-345 6789', '', 'Mother', 'Male', '', '', 'Mother', '', '$2y$10$cS8DywzTfbUPN/wT8EdB6.Gu0YtWQz6sFewmzuv6x9rGXSRu4CPr6'),
(5, 'Michael Tan Wei Ming', '', 'michael.tan@example.com', 'Jalan Mawar 5, Taman Indah, 81000 Kulai, Johor', '013-456 7890', '', 'Father', 'Male', '', '', 'Mother', '', '$2y$10$jJQX/pyGNB/hoETfMRg1NOfZMaHTTP2LreTqBAJSvyFoXihbGdzoe'),
(6, 'Tan Ling Wei', '', 'lingwei@gmail.com', 'Jalan Kenanga 8, Taman Bahagia, 81000 Kulai, Johor', '017-234 5678', '', 'Mother', 'Female', '', '', 'Mother', '', '$2y$10$Ay2hVNNnTpvTKakKeO5iCOpHExnLCilMT5PhEDHpRqdBzY8rzyEKG'),
(7, 'Chen Wei TIng', '', 'weiting@gmaail.com', 'Jalan Cempaka 3, Taman Sentosa, 81000 Kulai, Johor', '019-876 5432', '', 'Mother', 'Female', '', '', 'Mother', '', '$2y$10$URMRGRmgGetGqaV0ZyZC3Oa6c.t62imJA9aaw3Thd6zMbtMnHlTmy'),
(8, 'Jessica Lim Siew Ling', '', 'jessica.lim@gmail.com', 'Jalan Dahlia 10, Taman Permai, 81000 Kulai, Johor', '011-123 45678', '', 'Mother', 'Female', '', '', 'Mother', '', '$2y$10$iYLVHUqkDHaKg5cgw/bxQOSpLUN7QjUG6LQRrP7z/QmULAHBYrjDy'),
(9, 'Wong Kai Jun', '', 'wong.kai@gmail.com', 'Jalan Teratai 7, Taman Jaya, 81000 Kulai, Johor', '014-567 8901', '', 'Father', 'Male', '', '', 'Mother', '', '$2y$10$k54hoVaI1lP7beBr4D55uuBt.l6bhzQDFvXbJsKQ21jtoV/5.95JW'),
(10, 'David Lee Kok Wai', '', 'david.lee@gmail.com', 'Jalan Orkid 9, Taman Sinar, 81000 Kulai, Johor', '018-321 6547', '', 'Father', 'Male', '', '', 'Mother', '', '$2y$10$nt19jlLHdqa5F4w4./Caz.WU1fq/m2QoVXyQZhOHEbI8lepSERUha'),
(11, 'Chen Wei Sheng', '', 'chen.wei@gmail.com', 'Jalan Bunga Raya 1, Taman Mewah, 81000 Kulai, Johor', '010-987 6543', '', 'Father', 'Male', '', '', 'Mother', '', '$2y$10$TAmokQdr3tO9wFV9gf2VW.X.YZmsTqAdcP8zE4p6wSYf2oZhyTHTS'),
(12, 'Lim En Xi', '', 'enxi6387@gmail.com', 'Jalan Kemboja 4, Taman Damai, 81000 Kulai, Johor', '015-654 3210', '', 'Mother', 'Female', '', '', 'Mother', '', '$2y$10$a17AQLaFZJByOoSncsZ5w.pULjEN9U1Jar3o1o71wZJDMxMxaeHwi');

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
  `payment_total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `credit_card_id` int(11) NOT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `payment_total_amount`, `payment_method`, `credit_card_id`, `payment_time`) VALUES
(7, 3160.00, 'Credit Card', 6, '2025-01-01 15:18:31'),
(8, 3160.00, 'Credit Card', 7, '2025-01-01 15:25:31'),
(9, 3160.00, 'Credit Card', 8, '2025-01-01 15:30:35'),
(10, 610.00, 'Credit Card', 1, '2025-01-01 06:22:48'),
(11, 610.00, 'Credit Card', 5, '2025-01-01 06:37:44'),
(12, 610.00, 'Credit Card', 1, '2025-01-01 06:49:11');

-- --------------------------------------------------------

--
-- Table structure for table `registration_class`
--

CREATE TABLE `registration_class` (
  `registration_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `class_id` varchar(20) NOT NULL,
  `child_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration_class`
--

INSERT INTO `registration_class` (`registration_id`, `parent_id`, `class_id`, `child_id`, `payment_id`) VALUES
(1, 12, 'Mat0001', 4, 7),
(2, 12, 'Eng0001', 4, 7),
(3, 12, 'Mly0001', 4, 7),
(4, 12, 'Mat2001', 5, 7),
(5, 12, 'Mly2001', 5, 7),
(6, 12, 'Eng2001', 5, 7),
(7, 4, 'Mat0001', 6, 8),
(8, 4, 'Eng0001', 6, 8),
(9, 4, 'Mly0001', 6, 8),
(10, 4, 'Mat2001', 7, 8),
(11, 4, 'Mly2001', 7, 8),
(12, 4, 'Eng2001', 7, 8),
(13, 5, 'Eng0001', 8, 9),
(14, 5, 'Mat0001', 8, 9),
(15, 5, 'Mly0001', 8, 9),
(16, 5, 'Eng2001', 9, 9),
(17, 5, 'Mat2001', 9, 9),
(18, 5, 'Mly2001', 9, 9),
(19, 1, 'Mly0001', 1, 10),
(20, 3, 'Eng2001', 3, 11),
(21, 1, 'Eng0001', 1, 12);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` varchar(20) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `year` text NOT NULL,
  `subject_price` decimal(8,2) NOT NULL,
  `subject_description` text DEFAULT NULL,
  `subject_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `admin_id`, `subject_name`, `year`, `subject_price`, `subject_description`, `subject_image`) VALUES
('11132', 11111, 'Math', 'Year 1', 510.00, 'This Math subject introduces Year 1 students to numbers, counting, shapes, and simple addition. Fun activities help children learn and enjoy basic math skills.', 'img/math.jpg'),
('11245', 11111, 'English', 'Year 1', 510.00, 'This English subject helps Year 1 students build strong reading, writing, and speaking skills. Fun lessons and simple grammar make learning easy and enjoyable!', 'img/english.jpg'),
('11351', 11112, 'Melayu', 'Year 1', 510.00, 'This Bahasa Melayu subject helps Year 1 students learn basic words, sentences, and pronunciation through fun stories and songs. Great for building confidence!', 'img/malay1.jpg'),
('22134', 11111, 'Math', 'Year 2', 510.00, 'This Math subject helps Year 2 students learn addition, subtraction, measurements, and problem-solving with fun exercises and real-life examples.', 'img/math.jpg'),
('22345', 11111, 'Melayu', 'Year 2', 510.00, 'In Year 2 Bahasa Melayu, students improve reading, speaking, and writing skills. Lessons include short texts, grammar practice, and fun language activities.', 'img/malay1.jpg'),
('22534', 11111, 'English', 'Year 2', 510.00, 'Year 2 English builds on reading and writing skills with more vocabulary, grammar, and short stories. Students learn to speak and write with confidence.', 'img/english.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `teacher_ic_number` varchar(20) NOT NULL,
  `teacher_gender` enum('Male','Female') NOT NULL,
  `teacher_email` varchar(100) NOT NULL,
  `teacher_image` varchar(255) DEFAULT NULL,
  `teacher_phone_number` varchar(20) DEFAULT NULL,
  `teacher_address` text DEFAULT NULL,
  `teacher_join_date` date DEFAULT NULL,
  `teacher_status` enum('Active','Inactive') DEFAULT 'Active',
  `teacher_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `admin_id`, `teacher_name`, `teacher_ic_number`, `teacher_gender`, `teacher_email`, `teacher_image`, `teacher_phone_number`, `teacher_address`, `teacher_join_date`, `teacher_status`, `teacher_password`) VALUES
(12123, 11111, 'Mr. David', '001123-01-7824', 'Male', '12123@gmail.com', 'uploads/teacher_images/683c5a4be6398-avatar_kj2umdknm2b.jpg', '0117098524', '7036 Jalan Sena 35/7 Taman Indahpura 81000 Kulai Johor.', '2023-01-01', 'Active', '$2y$10$WFTN2ROURBX07pDHXxO9F.yfb4HgM.rY514NQp9p/6PclzGRi5/ny'),
(12233, 11111, 'Mr. John', '920408-01-1572', 'Male', '12233@gmail.com', 'uploads/teacher_images/683c5bcc0b11f-19080812073933.jpg', '0168208964', '7188 Jalan Seri 37/5 Taman Indahpura 81000 Kulai Johor', '2023-01-02', 'Active', '$2y$10$JXS.0RC3gXjTaanRUyv8Vuo2NKDA1J5O/jtGWv73QPL96/IbbsxdO'),
(12345, 11112, 'Ms. Lily', '971203-01-8065', 'Female', 'lily@gmail.com', 'uploads/teacher_images/68219532d4b99-WhatsApp Image 2025-05-05 at 22.06.09_f8489a52.jpg', '0178238204', '7019 Jalan Sena 35/3 Taman Indahpura 81000 Kulai Johor', '2023-01-01', 'Active', '$2y$10$IYSV2rC83rR6w62WYDuSDeOxRDnY7YHh.qUT0Ps.Ztp0dU8dhE91K'),
(12347, 11111, 'Ms. Enxi', '950718-01-4258', 'Female', 'enxi6387@gmail.com', 'uploads/teacher_images/683c605396c54-center.png', '0111827834', '8277 Jalan Sena 35/34 Taman Indahpura 81000 Kulai Johor', '2023-01-01', 'Active', '$2y$10$BJS3O/xa/atfMJ0rf1FobOgkHVXbpc/lO3Tu5u5MlEv2VtrkkjXse');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_comment`
--

CREATE TABLE `teacher_comment` (
  `teacher_comment_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `class_id` varchar(20) NOT NULL,
  `teacher_comment_text` text DEFAULT NULL,
  `teacher_comment_created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_comment`
--

INSERT INTO `teacher_comment` (`teacher_comment_id`, `child_id`, `class_id`, `teacher_comment_text`, `teacher_comment_created_at`) VALUES
(1, 1, 'Mly0001', 'Yuna is a hard-working student.', '2025-05-17 14:34:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_cart` (`parent_id`,`child_id`,`class_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `child_id` (`child_id`);

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
  ADD KEY `part_id` (`part_id`),
  ADD KEY `fk_class_admin_id` (`admin_id`);

--
-- Indexes for table `credit_cards`
--
ALTER TABLE `credit_cards`
  ADD PRIMARY KEY (`credit_card_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `exam_result`
--
ALTER TABLE `exam_result`
  ADD PRIMARY KEY (`exam_result_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_class` (`class_id`);

--
-- Indexes for table `notification_receiver`
--
ALTER TABLE `notification_receiver`
  ADD PRIMARY KEY (`receiver_id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `teacher_id` (`teacher_id`);

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
  ADD KEY `credit_card_id` (`credit_card_id`);

--
-- Indexes for table `registration_class`
--
ALTER TABLE `registration_class`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `fk_subject_admin` (`admin_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `teacher_ic_number` (`teacher_ic_number`),
  ADD UNIQUE KEY `teacher_ic_number_2` (`teacher_ic_number`),
  ADD KEY `fk_teacher_admin_id` (`admin_id`);

--
-- Indexes for table `teacher_comment`
--
ALTER TABLE `teacher_comment`
  ADD PRIMARY KEY (`teacher_comment_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `class_id` (`class_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11113;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `child`
--
ALTER TABLE `child`
  MODIFY `child_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `credit_cards`
--
ALTER TABLE `credit_cards`
  MODIFY `credit_card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exam_result`
--
ALTER TABLE `exam_result`
  MODIFY `exam_result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notification_receiver`
--
ALTER TABLE `notification_receiver`
  MODIFY `receiver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `part`
--
ALTER TABLE `part`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `registration_class`
--
ALTER TABLE `registration_class`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12352;

--
-- AUTO_INCREMENT for table `teacher_comment`
--
ALTER TABLE `teacher_comment`
  MODIFY `teacher_comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`),
