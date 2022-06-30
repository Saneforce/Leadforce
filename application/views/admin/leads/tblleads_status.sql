-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2020 at 05:13 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ttscrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblleads_status`
--

CREATE TABLE `tblleads_status` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `statusorder` int(11) DEFAULT NULL,
  `color` varchar(10) DEFAULT '#28B8DA',
  `isdefault` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tblleads_status`
--

INSERT INTO `tblleads_status` (`id`, `name`, `statusorder`, `color`, `isdefault`) VALUES
(1, 'Lead In', 5, '#7cb342', 1),
(2, 'Contact Made', 0, '#0288d1', 0),
(3, 'Negotiations Started', 2, '#757575', 0),
(4, 'Needs Defined', 1, '#fb3b3b', 0),
(5, 'Proposal Sent', 4, '#757575', 0),
(6, 'Working', 3, '#757575', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblleads_status`
--
ALTER TABLE `tblleads_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblleads_status`
--
ALTER TABLE `tblleads_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
