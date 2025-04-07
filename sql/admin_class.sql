-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 07:10 AM
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
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_class`
--

CREATE TABLE `admin_class` (
  `class_id` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `day` varchar(20) NOT NULL,
  `time` varchar(50) NOT NULL,
  `teacher` varchar(100) NOT NULL,
  `capacity` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_class`
--

INSERT INTO `admin_class` (`class_id`, `subject_id`, `year`, `day`, `time`, `teacher`, `capacity`) VALUES
('Eng0001', 11245, 'Year 1', 'Monday', '2:30pm - 4:30pm', 'Mr. John', '0/30'),
('Eng2001', 22534, 'Year 2', 'Monday', '5:00pm - 7:00pm', 'Mr. John', '0/30'),
('Mat0001', 11132, 'Year 1', 'Wednesday', '2:30pm - 4:30pm', 'Mr. David', '3/30'),
('Mat2001', 22134, 'Year 2', 'Wednesday', '5:00pm - 7:00pm', 'Mr. David', '0/30'),
('Mly0001', 11351, 'Year 1', 'Tuesday', '2:30pm - 4:30pm', 'Ms. Lily', '0/30'),
('Mly2001', 22345, 'Year 2', 'Tuesday', '5:00pm - 7:00pm', 'Ms. Lily', '1/30');

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
DELIMITER $$
CREATE TRIGGER `after_admin_class_insert` AFTER INSERT ON `admin_class` FOR EACH ROW BEGIN
    -- 解析 subject_id 对应的科目名称
    DECLARE subject_name VARCHAR(50);
    DECLARE max_capacity INT;
    DECLARE enrolled_count INT;

    -- 判断科目类型
    IF NEW.subject_id BETWEEN 11000 AND 11999 THEN
        SET subject_name = 'English';
    ELSEIF NEW.subject_id BETWEEN 22000 AND 22999 THEN
        SET subject_name = 'Malay';
    ELSEIF NEW.subject_id BETWEEN 33000 AND 33999 THEN
        SET subject_name = 'Math';
    ELSE
        SET subject_name = 'Unknown';
    END IF;

    -- 解析 capacity (已报名 / 总容量)
    SET enrolled_count = CAST(SUBSTRING_INDEX(NEW.capacity, '/', 1) AS UNSIGNED);
    SET max_capacity = CAST(SUBSTRING_INDEX(NEW.capacity, '/', -1) AS UNSIGNED);

    -- 插入 Part A (January - June)
    INSERT INTO tuition_centre.classdetail (class_name, part, month, time, capacity, enrolled, status)
    VALUES (
        CONCAT(NEW.year, ' ', subject_name),
        'Part A',
        'January - June',
        CONCAT(NEW.day, ' ', NEW.time),
        max_capacity,
        enrolled_count,
        CASE 
            WHEN enrolled_count = max_capacity THEN 'unavailable'
            ELSE 'available'
        END
    );

    -- 插入 Part B (July - December)
    INSERT INTO tuition_centre.classdetail (class_name, part, month, time, capacity, enrolled, status)
    VALUES (
        CONCAT(NEW.year, ' ', subject_name),
        'Part B',
        'July - December',
        CONCAT(NEW.day, ' ', NEW.time),
        max_capacity,
        0,  -- Part B 默认还没人报名
        'unavailable'
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_admin_class_update` AFTER UPDATE ON `admin_class` FOR EACH ROW BEGIN
    -- 解析 subject_id 对应的科目名称
    DECLARE subject_name VARCHAR(50);

    -- 判断科目类型
    IF NEW.subject_id BETWEEN 11000 AND 11999 THEN
        SET subject_name = 'English';
    ELSEIF NEW.subject_id BETWEEN 22000 AND 22999 THEN
        SET subject_name = 'Malay';
    ELSEIF NEW.subject_id BETWEEN 33000 AND 33999 THEN
        SET subject_name = 'Math';
    ELSE
        SET subject_name = 'Unknown';
    END IF;

    -- 更新 Part A（January - June）
    UPDATE tuition_centre.classdetail
    SET class_name = CONCAT(NEW.year, ' ', subject_name),
        time = CONCAT(NEW.day, ' ', NEW.time),
        capacity = CAST(SUBSTRING_INDEX(NEW.capacity, '/', -1) AS UNSIGNED),
        enrolled = CAST(SUBSTRING_INDEX(NEW.capacity, '/', 1) AS UNSIGNED),
        status = CASE
                    WHEN enrolled = capacity THEN 'unavailable'
                    ELSE 'available'
                  END
    WHERE class_name = CONCAT(OLD.year, ' ', subject_name) 
    AND part = 'Part A';

    -- **更新 Part B（July - December），但保留原来的 `enrolled` 和 `capacity`**
    UPDATE tuition_centre.classdetail
    SET class_name = CONCAT(NEW.year, ' ', subject_name),
        time = CONCAT(NEW.day, ' ', NEW.time)  -- **只更新日期，不修改其他数据**
    WHERE class_name = CONCAT(OLD.year, ' ', subject_name) 
    AND part = 'Part B';

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
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `subject_id` (`subject_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
