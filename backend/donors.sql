-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2024 at 05:28 PM
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
-- Database: `donors`
--

-- --------------------------------------------------------

--
-- Table structure for table `donerstable`
--

CREATE TABLE `donerstable` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int(15) NOT NULL,
  `Gender` enum('m','f','o') NOT NULL,
  `Blood_Type` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') NOT NULL,
  `governorate` enum('Alexandria','Assiut','Aswan','Beheira','Beni_Suef','Cairo','Dakahlia','Damietta','Fayoum','Gharbia','Giza','Ismailia','Luxor','Matrouh','Minya','Monufia','New_Valley','North_Sinai','Port_Said','Qalyubia','Qena','Red_Sea','Sharkia','Sohag','South_Sinai','Suez') NOT NULL,
  `donation_type` enum('blood','plasma') NOT NULL,
  `Q1` enum('Yes','No') NOT NULL,
  `Q2` enum('Yes','No') NOT NULL,
  `add_Q2` varchar(200) NOT NULL,
  `Q3` enum('Yes','No') NOT NULL,
  `add_Q3` varchar(200) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `available` enum('Yes','No') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `donerstable`
--

INSERT INTO `donerstable` (`id`, `name`, `email`, `age`, `Gender`, `Blood_Type`, `governorate`, `donation_type`, `Q1`, `Q2`, `add_Q2`, `Q3`, `add_Q3`, `phone`, `available`) VALUES
(1, 'Mohamed elsayed', 'mohammed.elaksher.2@gmail.com', 20, 'm', 'A+', 'Qalyubia', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'No'),
(2, 'Mohamed elsayed', 'mohammed.elaksher.2@gmail.com', 20, 'm', 'A+', 'Qalyubia', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'Yes'),
(3, 'Mohamed elsayed', 'mohammed.elaksher.2@gmail.com', 20, 'm', 'A-', 'Qalyubia', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'Yes'),
(4, 'Mohamed elsayed', 'mohammed.elaksher.2@gmail.com', 20, 'm', 'A-', 'Qalyubia', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'Yes'),
(5, 'Mohamed elsayed', 'mohammed.elaksher.2@gmail.com', 20, 'm', 'O-', 'Qalyubia', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'Yes'),
(6, 'Mohamed elsayed', 'mohammed.elaksher.2@gmail.com', 20, 'm', 'O+', 'Qalyubia', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'Yes'),
(7, 'Mohamed elsayed', 'mohammed.elaksher.2@gmail.com', 40, 'm', 'B+', 'Qalyubia', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'Yes'),
(8, 'Mostafe Ahmed ', 'maaryu2020@gmail.com', 70, 'm', 'AB-', 'Alexandria', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'No'),
(9, 'Mostafe Ahmed ', 'mohammed.elaksher.2@gmail.com', 70, 'm', 'AB-', 'Alexandria', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'No'),
(10, 'Mostafe Ahmed ', 'mohammed.elaksher.2@gmail.com', 70, 'm', 'AB-', 'Alexandria', 'blood', 'Yes', 'No', '', 'No', '', '01098487069', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `blood_type` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') NOT NULL,
  `governorate` enum('Alexandria','Assiut','Aswan','Beheira','Beni_Suef','Cairo','Dakahlia','Damietta','Fayoum','Gharbia','Giza','Ismailia','Luxor','Matrouh','Minya','Monufia','New_Valley','North_Sinai','Port_Said','Qalyubia','Qena','Red_Sea','Sharkia','Sohag','South_Sinai','Suez') NOT NULL,
  `location` varchar(100) NOT NULL,
  `age` int(15) NOT NULL,
  `done` enum('Yes','No') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `name`, `email`, `blood_type`, `governorate`, `location`, `age`, `done`) VALUES
(1, 'Mohamed elsayed', 'maaryu2020@gmail.com', 'A+', 'Sohag', 'Elkalafawy', 20, 'Yes'),
(2, 'elsayed Ahmed', 'maaryu2020@gmail.com', 'AB-', 'Port_Said', 'mmm', 40, 'Yes'),
(3, 'elsayed Ahmed', 'mohammed.elaksher.2@gmail.com', 'AB-', 'Port_Said', 'mmm', 40, 'Yes'),
(4, 'elsayed Ahmed', 'mohammed.elaksher.2@gmail.com', 'AB-', 'Port_Said', 'mmm', 40, 'Yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donerstable`
--
ALTER TABLE `donerstable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donerstable`
--
ALTER TABLE `donerstable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
