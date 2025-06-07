-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 03:13 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ojt_attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time DEFAULT NULL,
  `user_email` varchar(255) NOT NULL,
  `session_info` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `date`, `time_in`, `time_out`, `user_email`, `session_info`) VALUES
(32, '12', '2024-11-17', '07:47:55', '08:24:32', 'mark@gmail.com', 'Present Today'),
(33, '211111', '2024-11-17', '08:57:12', NULL, 'mark@gmail.com', 'Time In');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_description`) VALUES
(1, 'BSIT', 'bsit'),
(2, 'HRM', 'hrm');

-- --------------------------------------------------------

--
-- Table structure for table `establishments`
--

CREATE TABLE `establishments` (
  `id` int(11) NOT NULL,
  `establishment_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` int(11) NOT NULL DEFAULT 1 COMMENT '0-admin 1-user',
  `createAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `establishments`
--

INSERT INTO `establishments` (`id`, `establishment_name`, `address`, `contact_person`, `phone`, `email`, `password`, `usertype`, `createAt`) VALUES
(1, 'MUTI', 'banga', 'reymark', '09123123', 'mark@gmail.com', '$2y$10$gkbbP1AmMo/kTppuwoTNuOQoBXJV1iy8vFoOpQJKwFrbXq1cOzXq.', 1, '2024-11-08 14:43:48'),
(6, 'Admin', 'admin', 'admin', '00000000000', 'admin@gmail.com', '$2y$10$/6uSL23MPlAqVn/WmbPQJu83W7RlsOyB3rKQdCfY3/dvjDTywDYZy', 0, '2024-11-13 12:36:09'),
(8, 'RUSI', 'rusi', 'rusi', '09123123123', 'rusi@gmail.com', '$2y$10$YlcfuLww3bFWFFSTlpg8sOHqBbVl5.65nGg890wV1XrocjCcEV6bq', 1, '2024-11-14 15:08:59');

-- --------------------------------------------------------

--
-- Table structure for table `establishment_settings`
--

CREATE TABLE `establishment_settings` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `max_overtime` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `establishment_settings`
--

INSERT INTO `establishment_settings` (`id`, `user_email`, `time_in`, `time_out`, `max_overtime`, `created_at`, `updated_at`) VALUES
(1, 'rusi@gmail.com', '23:28:00', '23:35:00', 5, '2024-11-19 15:20:44', '2024-11-19 15:38:53'),
(2, 'mark@gmail.com', '23:22:00', '23:24:00', 12, '2024-11-19 15:22:07', '2024-11-19 15:22:07');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `school_name`, `school_address`) VALUES
(2, 'RMMC-MI', 'banga');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `school` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `number_hours` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `name`, `course`, `school`, `start_date`, `number_hours`, `user_email`, `created_at`) VALUES
(4, 2111021312, 'ReyMark', 'BSIT', 'RMMC', '2024-11-09', 12, '', '2024-11-09 13:46:33'),
(5, 211105007, 'Markrey', 'BSIT', 'RMMC', '2024-11-09', 360, '', '2024-11-09 14:21:02'),
(6, 211111, 'mark', 'BSIT', 'RMMC', '2024-11-10', 160, 'mark@gmail.com', '2024-11-10 15:32:14'),
(7, 12, 'asd', 'bist', 'rmmc', '2024-11-10', 106, 'mark@gmail.com', '2024-11-10 15:34:30'),
(8, 21, 'ma', 'bsit', 'ma', '2024-11-15', 160, 'rusi@gmail.com', '2024-11-11 11:15:04'),
(9, 2222, 'ReyMark', 'bsit', 'RMMC', '2024-11-12', 123, 'rusi@gmail.com', '2024-11-12 02:05:26'),
(10, 22, 'ma', 'asd', 'ab', '2024-11-12', 213, 'rusi@gmail.com', '2024-11-12 02:06:50'),
(11, 11, 'ma', 'BSIT', 'RMMC-MI', '2024-11-17', 12, 'mark@gmail.com', '2024-11-16 14:14:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `establishments`
--
ALTER TABLE `establishments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `establishment_name` (`establishment_name`);

--
-- Indexes for table `establishment_settings`
--
ALTER TABLE `establishment_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `establishments`
--
ALTER TABLE `establishments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `establishment_settings`
--
ALTER TABLE `establishment_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `establishment_settings`
--
ALTER TABLE `establishment_settings`
  ADD CONSTRAINT `establishment_settings_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `establishments` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
