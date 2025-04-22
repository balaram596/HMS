-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 10:41 PM
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
-- Database: `user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `pat_id` int(11) DEFAULT NULL,
  `doc_id` int(11) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `prescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `pat_id`, `doc_id`, `appointment_date`, `status`, `prescription`) VALUES
(1, 0, 0, '2025-04-24 14:00:00', 'cancelled', NULL),
(2, 0, 0, '2025-04-24 09:00:00', 'completed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doc_id` int(11) NOT NULL,
  `doc_name` varchar(100) NOT NULL,
  `doc_specl` varchar(100) DEFAULT NULL,
  `doc_username` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doc_id`, `doc_name`, `doc_specl`, `doc_username`, `password`) VALUES
(0, 'Harsha Vardhan', 'Gynacologist', 'harsha', '$2y$10$f2v2VcIDM34aZAaWZKU1Kemi1Bx1H0rq5UezPFidPeYZWWojNi4be');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_sessions`
--

CREATE TABLE `doctor_sessions` (
  `doc_id` int(11) DEFAULT NULL,
  `day_of_week` varchar(10) DEFAULT NULL,
  `session_number` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_sessions`
--

INSERT INTO `doctor_sessions` (`doc_id`, `day_of_week`, `session_number`, `start_time`, `end_time`) VALUES
(0, 'Monday', 1, '09:00:00', '12:00:00'),
(0, 'Monday', 2, '14:00:00', '15:00:00'),
(0, 'Tuesday', 1, '09:00:00', '12:00:00'),
(0, 'Tuesday', 2, '14:00:00', '15:00:00'),
(0, 'Wednesday', 1, '09:00:00', '12:00:00'),
(0, 'Wednesday', 2, '14:00:00', '15:00:00'),
(0, 'Thursday', 1, '09:00:00', '12:00:00'),
(0, 'Thursday', 2, '14:00:00', '15:00:00'),
(0, 'Friday', 1, '09:00:00', '12:00:00'),
(0, 'Friday', 2, '14:00:00', '15:00:00'),
(0, 'Saturday', 1, '09:00:00', '12:00:00'),
(0, 'Saturday', 2, '14:00:00', '15:00:00'),
(0, 'Sunday', 1, '09:00:00', '12:00:00'),
(0, 'Sunday', 2, '14:00:00', '15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `pat_username` varchar(255) NOT NULL,
  `pat_name` varchar(100) NOT NULL,
  `pat_age` int(11) NOT NULL,
  `pat_gender` enum('Male','Female','Other') NOT NULL,
  `pat_phone` varchar(15) NOT NULL,
  `pat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`pat_username`, `pat_name`, `pat_age`, `pat_gender`, `pat_phone`, `pat_id`) VALUES
('ramu', 'Ramesh', 23, 'Male', '9087958394', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('patient','doctor','admin') NOT NULL DEFAULT 'patient'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(9, 'admin', '$2y$10$ODUswtw1XAmPOCzE71bYXe/.q7TP25gEiOXxqOH4wBBbtHnG9/Bsm', 'admin'),
(0, 'ramu', '$2y$10$aQBtUpaoVhuDbkR0hWdrh.r6Fj0a3GRnKOIRbl03KbLw9hSJgLFQW', 'patient'),
(0, 'ramu', '$2y$10$ziEzuyJIGHBvDZgggeXscOY/.leW8ApAjPxIniIfTYfnVpuPszlO6', 'patient');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
