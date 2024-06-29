-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2024 at 06:24 AM
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
-- Database: `ridesharing`
--

-- --------------------------------------------------------

--
-- Table structure for table `poolalerts`
--

CREATE TABLE `poolalerts` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `sourceAddress` varchar(255) NOT NULL,
  `sourceLatitude` double NOT NULL,
  `sourceLongitude` double NOT NULL,
  `destinationAddress` varchar(255) NOT NULL,
  `destinationLatitude` double NOT NULL,
  `destinationLongitude` double NOT NULL,
  `vehicleType` enum('car','bike') NOT NULL,
  `vacantSeats` int(11) NOT NULL CHECK (`vacantSeats` <= `advertisedSeats`),
  `advertisedSeats` int(11) NOT NULL,
  `time` time NOT NULL,
  `date` date NOT NULL,
  `status` enum('booked','available') NOT NULL DEFAULT 'available',
  `createdDate` datetime DEFAULT current_timestamp(),
  `updatedDate` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poolalerts`
--

INSERT INTO `poolalerts` (`id`, `userId`, `sourceAddress`, `sourceLatitude`, `sourceLongitude`, `destinationAddress`, `destinationLatitude`, `destinationLongitude`, `vehicleType`, `vacantSeats`, `advertisedSeats`, `time`, `date`, `status`, `createdDate`, `updatedDate`) VALUES
(1, 2, 'M97P+R78, Dadhikot road, Anantalingeshwar, Nepal', 27.6645348, 85.3857099, 'M8XF+6H2, Kathmandu 44600, Nepal', 27.6980136, 85.3238935, 'car', 0, 0, '23:53:00', '2024-06-26', 'available', '2024-06-26 23:53:20', '2024-06-26 23:53:20'),
(2, 2, 'M97P+R78, Dadhikot road, Anantalingeshwar, Nepal', 27.6645348, 85.3857099, 'New Baneshwor, Kathmandu 44600, Nepal', 27.6915196, 85.3420486, 'car', 6, 6, '23:53:00', '2024-06-26', 'available', '2024-06-26 23:56:01', '2024-06-26 23:56:01'),
(3, 2, 'MCFG+GQV, Madhyapur Thimi 44800, Nepal', 27.6732779, 85.3800917, 'Maitighar, Kathmandu 44600, Nepal', 27.6919767, 85.3229913, 'car', 4, 4, '00:05:00', '2024-06-27', 'available', '2024-06-27 00:05:25', '2024-06-27 00:05:25'),
(4, 2, 'MCFG+GQV, Madhyapur Thimi 44800, Nepal', 27.6732779, 85.3800917, 'P88G+XFG, Gairidhara Sadak, Kathmandu 44600, Nepal', 27.7174323, 85.3261882, 'bike', 2, 2, '00:26:00', '2024-06-27', 'available', '2024-06-27 00:27:06', '2024-06-27 00:27:06'),
(5, 2, 'M97P+R78, Dadhikot road, Anantalingeshwar, Nepal', 27.6645348, 85.3857099, 'Maitighar, Kathmandu 44600, Nepal', 27.6919767, 85.3229913, 'car', 2, 2, '00:55:00', '2024-06-27', 'available', '2024-06-27 00:55:46', '2024-06-27 00:55:46'),
(6, 2, 'M97P+R78, Dadhikot road, Anantalingeshwar, Nepal', 27.6645348, 85.3857099, 'Maitighar, Kathmandu 44600, Nepal', 27.6919767, 85.3229913, 'car', 5, 5, '00:55:00', '2024-06-27', 'available', '2024-06-27 00:56:08', '2024-06-27 00:56:08');

-- --------------------------------------------------------

--
-- Table structure for table `poolmappings`
--

CREATE TABLE `poolmappings` (
  `id` int(11) NOT NULL,
  `poolRequestId` int(11) NOT NULL,
  `poolAlertId` int(11) NOT NULL,
  `bookedSeats` int(11) NOT NULL,
  `status` enum('read','unread') NOT NULL DEFAULT 'unread',
  `isNew` enum('yes','no') NOT NULL DEFAULT 'yes',
  `createdDate` datetime DEFAULT current_timestamp(),
  `updatedDate` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poolrequests`
--

CREATE TABLE `poolrequests` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `sourceAddress` varchar(255) NOT NULL,
  `sourceLatitude` double NOT NULL,
  `sourceLongitude` double NOT NULL,
  `destinationAddress` varchar(255) NOT NULL,
  `destinationLatitude` double NOT NULL,
  `destinationLongitude` double NOT NULL,
  `vehicleType` enum('car','bike') NOT NULL,
  `appliedSeats` int(11) DEFAULT NULL,
  `time` datetime NOT NULL,
  `date` date NOT NULL,
  `status` enum('booked','available') NOT NULL DEFAULT 'available',
  `createdDate` datetime DEFAULT current_timestamp(),
  `updatedDate` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poolrequests`
--

INSERT INTO `poolrequests` (`id`, `userId`, `sourceAddress`, `sourceLatitude`, `sourceLongitude`, `destinationAddress`, `destinationLatitude`, `destinationLongitude`, `vehicleType`, `appliedSeats`, `time`, `date`, `status`, `createdDate`, `updatedDate`) VALUES
(2, 1, 'Kathmandu School of Law', 27.664610794805267, 85.38568843834916, 'Maitighar', 27.694526274520637, 85.32031739602279, 'car', 4, '2022-10-10 10:00:00', '2022-10-10', 'available', '2024-06-26 16:10:14', '2024-06-26 16:10:14'),
(3, 1, 'Kathmandu School of Law', 27.664610794805267, 85.38568843834916, 'Tinkune', 27.68228235535513, 85.34930274767896, 'car', 4, '2022-10-10 10:00:00', '2022-10-10', 'available', '2024-06-26 16:12:00', '2024-06-26 16:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fristName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `bloodGroup` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `DOB` date NOT NULL,
  `citizenshipNo` varchar(100) DEFAULT NULL,
  `licenseNo` varchar(100) DEFAULT NULL,
  `isRider` enum('yes','no') DEFAULT 'no',
  `token` varchar(100) DEFAULT NULL,
  `tokenExpiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fristName`, `lastName`, `email`, `password`, `phone`, `address`, `bloodGroup`, `DOB`, `citizenshipNo`, `licenseNo`, `isRider`, `token`, `tokenExpiry`) VALUES
(1, 'Jane', 'Doe', 'arunstha547@gmail.com', 'user', '1234567890', '123 Main St, City, Country', 'A+', '1991-01-25', '1234567890', '1234567890', 'no', NULL, NULL),
(2, 'Jane', 'Doe', 'arunstha5471@gmail.com', 'user', '12347567890', '123 Main St, City, Country', 'A+', '1991-01-25', '12345675890', '12345675890', 'no', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `poolalerts`
--
ALTER TABLE `poolalerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `poolmappings`
--
ALTER TABLE `poolmappings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poolRequestId` (`poolRequestId`),
  ADD KEY `poolAlertId` (`poolAlertId`);

--
-- Indexes for table `poolrequests`
--
ALTER TABLE `poolrequests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `poolalerts`
--
ALTER TABLE `poolalerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `poolmappings`
--
ALTER TABLE `poolmappings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poolrequests`
--
ALTER TABLE `poolrequests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `poolalerts`
--
ALTER TABLE `poolalerts`
  ADD CONSTRAINT `poolalerts_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `poolmappings`
--
ALTER TABLE `poolmappings`
  ADD CONSTRAINT `poolmappings_ibfk_1` FOREIGN KEY (`poolRequestId`) REFERENCES `poolrequests` (`id`),
  ADD CONSTRAINT `poolmappings_ibfk_2` FOREIGN KEY (`poolAlertId`) REFERENCES `poolalerts` (`id`);

--
-- Constraints for table `poolrequests`
--
ALTER TABLE `poolrequests`
  ADD CONSTRAINT `poolrequests_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
