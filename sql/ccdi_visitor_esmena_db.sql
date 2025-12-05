-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2025 at 03:10 PM
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
-- Database: `ccdi_visitor_esmena_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('staff','manager','admin') DEFAULT 'staff',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', 'admin123', 'admin', 'active', '2025-12-04 03:18:34'),
(2, 'staff1', 'staff1@example.com', 'staff123', 'staff', 'active', '2025-12-04 03:18:34'),
(3, 'manager1', 'manager1@example.com', 'manager123', 'manager', 'active', '2025-12-04 03:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `visitor_id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `school_office` varchar(100) NOT NULL,
  `purpose_id` int(11) NOT NULL,
  `date_of_visit` date NOT NULL,
  `time_of_visit` time NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`visitor_id`, `visitor_name`, `address`, `contact_number`, `school_office`, `purpose_id`, `date_of_visit`, `time_of_visit`, `notes`, `created_by`, `created_at`) VALUES
(2, 'Maria Santos', '456 Oak Ave, Sorsogon', '09172234567', 'Registration Office', 2, '2025-12-04', '10:15:00', NULL, 2, '2025-12-04 03:18:34'),
(3, 'Jose Cruz', '789 Pine Rd, Sorsogon', '09173234567', 'Admissions', 3, '2025-12-04', '11:00:00', NULL, 1, '2025-12-04 03:18:34'),
(4, 'Ana Garcia', '321 Elm St, Sorsogon', '09174234567', 'Finance Office', 6, '2025-12-04', '13:30:00', NULL, 2, '2025-12-04 03:18:34'),
(5, 'Pedro Lopez', '654 Maple Dr, Sorsogon', '09175234567', 'Library', 8, '2025-12-04', '14:45:00', NULL, 1, '2025-12-04 03:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `log_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visit_purposes`
--

CREATE TABLE `visit_purposes` (
  `id` int(11) NOT NULL,
  `purpose_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visit_purposes`
--

INSERT INTO `visit_purposes` (`id`, `purpose_name`, `created_at`) VALUES
(1, 'Inquiry', '2025-12-04 03:18:34'),
(2, 'Exam', '2025-12-04 03:18:34'),
(3, 'Visit', '2025-12-04 03:18:34'),
(4, 'Registration', '2025-12-04 03:18:34'),
(5, 'Interview', '2025-12-04 03:18:34'),
(6, 'Consultation', '2025-12-04 03:18:34'),
(7, 'Document Request', '2025-12-04 03:18:34'),
(8, 'Other', '2025-12-04 03:18:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`visitor_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `purpose_id` (`purpose_id`);

--
-- Indexes for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `visit_purposes`
--
ALTER TABLE `visit_purposes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purpose_name` (`purpose_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visit_purposes`
--
ALTER TABLE `visit_purposes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `visitors_ibfk_2` FOREIGN KEY (`purpose_id`) REFERENCES `visit_purposes` (`id`);

--
-- Constraints for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD CONSTRAINT `visitor_logs_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`visitor_id`),
  ADD CONSTRAINT `visitor_logs_ibfk_2` FOREIGN KEY (`performed_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
