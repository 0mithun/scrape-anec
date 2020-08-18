-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 18, 2020 at 03:28 PM
-- Server version: 10.5.4-MariaDB
-- PHP Version: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anecbak`
--

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

CREATE TABLE `channels` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `channels`
--

INSERT INTO `channels` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Entertainment', 'Entertainment', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(2, 'Other', 'Other', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(3, 'Architecture', 'Architecture', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(4, 'Art', 'Art', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(5, 'Books', 'Books', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(6, 'Business', 'Business', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(7, 'Celebrities', 'Celebrities', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(8, 'Death', 'Death', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(9, 'Dumb', 'Dumb', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(10, 'Education', 'Education', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(11, 'Food', 'Food', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(12, 'Funny', 'Funny', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(13, 'History', 'History', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(14, 'Insults', 'Insults', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(15, 'Life', 'Life', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(16, 'Love', 'Love', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(17, 'Mistakes', 'Mistakes', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(18, 'Money', 'Money', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(19, 'Movies', 'Movies', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(20, 'Music', 'Music', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(21, 'Politics', 'Politics', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(22, 'Pranks', 'Pranks', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(23, 'Religion', 'Religion', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(24, 'Science', 'Science', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(25, 'Sex', 'Sex', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(26, 'Sports', 'Sports', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(27, 'Travel', 'Travel', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(28, 'Television', 'Television', '2020-07-30 08:00:09', '2020-07-30 08:00:09'),
(29, 'War', 'War', '2020-07-30 08:00:09', '2020-07-30 08:00:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `channels`
--
ALTER TABLE `channels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
