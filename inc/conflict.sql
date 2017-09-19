-- phpMyAdmin SQL Dump
-- version 4.7.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 19, 2017 at 09:58 AM
-- Server version: 5.5.40-MariaDB-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `conflict`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_list`
--

CREATE TABLE `access_list` (
  `id` int(11) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `name` tinytext,
  `login` tinytext,
  `pass` tinytext,
  `phpsessid` tinytext,
  `enc` tinytext,
  `third_cookie` tinytext,
  `cathedral_id` int(11) DEFAULT NULL,
  `autologin` enum('0','1') DEFAULT NULL,
  `cathedral` enum('0','1') DEFAULT NULL,
  `refresh` enum('0','1') DEFAULT NULL,
  `proxy_ip` tinytext,
  `proxy_port` tinytext,
  `proxy_user` tinytext,
  `proxy_pass` tinytext,
  `ua` tinytext,
  `id_mast` int(11) DEFAULT NULL,
  `id_union` int(11) DEFAULT NULL,
  `status` enum('0','1') DEFAULT NULL,
  `trade_id` int(11) DEFAULT '0',
  `bot` tinyint(4) DEFAULT '0',
  `nation` enum('1','2') DEFAULT NULL,
  `notification` text,
  `notification_url` text,
  `unit_hole` int(11) DEFAULT '0',
  `arm_hole` int(11) DEFAULT '0',
  `archeology` enum('0','1') DEFAULT '0',
  `res_hole` int(11) DEFAULT '0'
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bot`
--

CREATE TABLE `bot` (
  `id` int(11) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `text` text
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `text` text,
  `time` int(11) DEFAULT NULL
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `type` tinytext,
  `time` int(11) DEFAULT NULL,
  `text` mediumtext
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `patterns`
--

CREATE TABLE `patterns` (
  `id` int(11) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `id_pattern` int(11) DEFAULT NULL,
  `pattern_name` tinytext,
  `pattern_attack` tinytext,
  `pattern_defence` int(11) DEFAULT NULL,
  `pattern_hp` int(11) DEFAULT NULL,
  `pattern_wood` int(11) DEFAULT NULL,
  `pattern_coal` int(11) DEFAULT NULL,
  `pattern_time` float DEFAULT NULL,
  `pattern_type` tinyint(4) DEFAULT '0'
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proxy`
--

CREATE TABLE `proxy` (
  `id` int(11) NOT NULL,
  `ip` tinytext,
  `port` tinytext,
  `login` tinytext,
  `pass` tinytext,
  `used` tinyint(4) DEFAULT '0'
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proxy_status`
--

CREATE TABLE `proxy_status` (
  `proxy` varchar(255) NOT NULL DEFAULT '',
  `port` int(10) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `status_text` varchar(1024) DEFAULT NULL,
  `time` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `razv`
--

CREATE TABLE `razv` (
  `id` int(11) NOT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `x` int(11) DEFAULT NULL,
  `y` int(11) DEFAULT NULL,
  `guard` enum('0','1') DEFAULT '0',
  `acc_id` int(11) DEFAULT NULL,
  `wait` enum('0','1') DEFAULT NULL
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `task_cathedral`
--

CREATE TABLE `task_cathedral` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `id_cathedral` int(11) DEFAULT NULL,
  `range` mediumtext,
  `status` enum('0','1') DEFAULT NULL
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `task_mast`
--

CREATE TABLE `task_mast` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `id_mast` int(11) DEFAULT NULL,
  `id_pattern` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `range` mediumtext,
  `status` enum('0','1') DEFAULT NULL
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `task_res`
--

CREATE TABLE `task_res` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `id_building` int(11) DEFAULT NULL,
  `action` enum('1','2') DEFAULT NULL,
  `id_res` enum('1','2','3','4','5') DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `range` mediumtext,
  `status` enum('0','1') DEFAULT NULL
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `text`
--

CREATE TABLE `text` (
  `id` int(11) NOT NULL,
  `text` text
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` tinytext,
  `password` tinytext,
  `level` tinytext,
  `sid` tinytext,
  `max` int(11) DEFAULT '20',
  `bot_access` enum('0','1') DEFAULT NULL,
  `theme` tinyint(4) DEFAULT '1',
  `ban` enum('0','1') DEFAULT '0'
) ENGINE=Aria DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_list`
--
ALTER TABLE `access_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bot`
--
ALTER TABLE `bot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patterns`
--
ALTER TABLE `patterns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proxy`
--
ALTER TABLE `proxy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proxy_status`
--
ALTER TABLE `proxy_status`
  ADD PRIMARY KEY (`proxy`);

--
-- Indexes for table `razv`
--
ALTER TABLE `razv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_cathedral`
--
ALTER TABLE `task_cathedral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_mast`
--
ALTER TABLE `task_mast`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_res`
--
ALTER TABLE `task_res`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `text`
--
ALTER TABLE `text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_list`
--
ALTER TABLE `access_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bot`
--
ALTER TABLE `bot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patterns`
--
ALTER TABLE `patterns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `proxy`
--
ALTER TABLE `proxy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `razv`
--
ALTER TABLE `razv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_cathedral`
--
ALTER TABLE `task_cathedral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_mast`
--
ALTER TABLE `task_mast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_res`
--
ALTER TABLE `task_res`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `text`
--
ALTER TABLE `text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
