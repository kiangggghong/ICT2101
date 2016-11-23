-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 22, 2016 at 07:52 PM
-- Server version: 5.6.33
-- PHP Version: 5.6.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ict2101`
--

-- --------------------------------------------------------

--
-- Table structure for table `Delivery`
--

CREATE TABLE `Delivery` (
  `vehiclePlateNumber` varchar(8) NOT NULL,
  `orderNo` int(11) NOT NULL,
  `ETA` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Delivery`
--

INSERT INTO `Delivery` (`vehiclePlateNumber`, `orderNo`, `ETA`, `sequence`) VALUES
('SGC3322P', 1, 20, 1),
('SGC3322P', 2, 40, 2),
('SGC3322P', 3, 45, 3),
('SGC3322P', 4, 50, 4);

-- --------------------------------------------------------

--
-- Table structure for table `Drivers`
--

CREATE TABLE `Drivers` (
  `vehiclePlateNumber` varchar(8) NOT NULL,
  `status` varchar(15) NOT NULL,
  `longitude` varchar(30) NOT NULL,
  `latitude` varchar(30) NOT NULL,
  `emp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Drivers`
--

INSERT INTO `Drivers` (`vehiclePlateNumber`, `status`, `longitude`, `latitude`, `emp_id`) VALUES
('SGC1144K', 'IDLE', '11111111.00000', '11111111.00000', 2),
('SGC3322P', 'DELIVERING', '11111222.00000', '11111222.00000', 3);

-- --------------------------------------------------------

--
-- Table structure for table `Employees`
--

CREATE TABLE `Employees` (
  `emp_id` int(11) NOT NULL,
  `userName` varchar(150) NOT NULL,
  `password` varchar(200) NOT NULL,
  `firstName` varchar(150) NOT NULL,
  `lastName` varchar(150) NOT NULL,
  `contactNumber` varchar(8) NOT NULL,
  `r_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Employees`
--

INSERT INTO `Employees` (`emp_id`, `userName`, `password`, `firstName`, `lastName`, `contactNumber`, `r_id`) VALUES
(1, 'kelvin.chan', '12345678', 'Kelvin', 'Chan', '93332211', 2),
(2, 'john.tan', '12345678', 'John', 'Tan', '91145566', 1),
(3, 'bob.koh', '12345678', 'Bob', 'Koh', '98700022', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Packages`
--

CREATE TABLE `Packages` (
  `orderNo` int(11) NOT NULL,
  `custName` varchar(150) NOT NULL,
  `custContactNo` varchar(8) NOT NULL,
  `address` varchar(255) NOT NULL,
  `unitNumber` varchar(7) NOT NULL,
  `postalCode` varchar(6) NOT NULL,
  `date_of_delivery` date NOT NULL,
  `time_of_delivery` int(11) DEFAULT '1200' COMMENT 'Time to delivery',
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Packages`
--

INSERT INTO `Packages` (`orderNo`, `custName`, `custContactNo`, `address`, `unitNumber`, `postalCode`, `date_of_delivery`, `time_of_delivery`, `status`) VALUES
(1, 'Chen Xia Tian', '90001111', 'Hougang ST91 BLK969', '11-174', '530969', '2016-12-01', 1200, 'PENDING'),
(2, 'Emma Chua', '91233321', 'Hougang Ave 8 Blk 564', '15-432', '530969', '2016-12-01', 1400, 'PENDING'),
(3, 'Nick Tang', '81120066', 'Hougang ST52 Blk534', '04-44', '530534', '2016-11-01', 1600, 'PENDING'),
(4, 'Alicia Tay', '90078007', 'Hougang ST52 Blk 538', '08-78', '530538', '2016-11-01', 1200, 'PENDING');

-- --------------------------------------------------------

--
-- Table structure for table `Roles`
--

CREATE TABLE `Roles` (
  `r_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Roles`
--

INSERT INTO `Roles` (`r_id`, `type`) VALUES
(1, 'Driver'),
(2, 'Manager');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Drivers`
--
ALTER TABLE `Drivers`
  ADD PRIMARY KEY (`vehiclePlateNumber`);

--
-- Indexes for table `Employees`
--
ALTER TABLE `Employees`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `Packages`
--
ALTER TABLE `Packages`
  ADD PRIMARY KEY (`orderNo`);

--
-- Indexes for table `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`r_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Employees`
--
ALTER TABLE `Employees`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Packages`
--
ALTER TABLE `Packages`
  MODIFY `orderNo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `Roles`
--
ALTER TABLE `Roles`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;