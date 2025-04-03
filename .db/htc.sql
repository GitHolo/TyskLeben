-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2025 at 08:15 PM
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
-- Database: `htc`
--

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `user_ID` int(255) NOT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `food` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`user_ID`, `money`, `food`) VALUES
(18, 29.84, 999999),
(22, 100.00, 100);

-- --------------------------------------------------------

--
-- Table structure for table `hamsters`
--

CREATE TABLE `hamsters` (
  `user_id` int(11) NOT NULL,
  `color1` varchar(7) DEFAULT NULL,
  `color2` varchar(7) DEFAULT NULL,
  `shadow1` varchar(7) DEFAULT NULL,
  `shadow2` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hamsters`
--

INSERT INTO `hamsters` (`user_id`, `color1`, `color2`, `shadow1`, `shadow2`) VALUES
(1, '#ff8b1f', '#ffffff', NULL, NULL),
(18, '#a8a8a8', '#a3a3a3', 'rgb(134', 'rgb(130'),
(22, '#532d2d', '#583232', 'rgb(66,', 'rgb(70,');

-- --------------------------------------------------------

--
-- Table structure for table `hats`
--

CREATE TABLE `hats` (
  `hat_id` int(11) NOT NULL,
  `hat_name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hats`
--

INSERT INTO `hats` (`hat_id`, `hat_name`, `price`, `image_url`) VALUES
(1, 'Bowler', 15, './assets/svg/bowler-cropped.svg'),
(2, 'Cowboy', 12, './assets/svg/cowboy-cropped.svg'),
(3, 'Fedora', 20, './assets/svg/fedora-cropped.svg'),
(4, 'Fez', 10, './assets/svg/fez-cropped.svg'),
(5, 'Newsboy', 15, './assets/svg/newsboy-cropped.svg');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `user_ID` int(255) NOT NULL,
  `login` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `createDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`user_ID`, `login`, `email`, `password`, `createDate`) VALUES
(18, 'admin', 'admin@admin.com', '$2y$10$RstZaM9gYVwe9vZ.K6eplOeiY.b1DfL3F1njlqC3ozOdOGIntmqHm', '2025-03-10 21:26:50'),
(22, 'admin2', 'admin2@admin.com', '$2y$10$zWh85FTyUE04UB2LGfDXS.3Sd4T7SteukahKz9VgXzzdKo2Y7x3QC', '2025-03-16 21:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_hats`
--

CREATE TABLE `user_hats` (
  `user_id` int(11) DEFAULT NULL,
  `hat_id` tinyint(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hamsters`
--
ALTER TABLE `hamsters`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `hats`
--
ALTER TABLE `hats`
  ADD PRIMARY KEY (`hat_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`user_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hamsters`
--
ALTER TABLE `hamsters`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `hats`
--
ALTER TABLE `hats`
  MODIFY `hat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `user_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
