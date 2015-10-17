-- phpMyAdmin SQL Dump
-- version 4.4.9
-- http://www.phpmyadmin.net
--
-- Host: 172.17.0.39:3306
-- Generation Time: Oct 17, 2015 at 10:52 AM
-- Server version: 5.5.44
-- PHP Version: 5.6.9-1+deb.sury.org~trusty+2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev`
--

SET FOREIGN_KEY_CHECKS = 0;
  SET GROUP_CONCAT_MAX_LEN=32768;
  SET @tables = NULL;
  SELECT GROUP_CONCAT(table_name) INTO @tables
    FROM information_schema.tables
    WHERE table_schema = (SELECT DATABASE());
  SELECT IFNULL(@tables,'dummy') INTO @tables;
  SET @tables = CONCAT('DROP TABLE IF EXISTS ', @tables);
  PREPARE stmt FROM @tables;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
  SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `id` int(11) NOT NULL,
  `artiest_id` int(11) NOT NULL DEFAULT '0',
  `album` varchar(100) NOT NULL,
  `jaar` int(4) NOT NULL DEFAULT '0',
  `titelnummer` varchar(8) NOT NULL,
  `recensies` int(11) NOT NULL DEFAULT '0',
  `lijsten` int(11) NOT NULL DEFAULT '0',
  `materiaal` char(3) NOT NULL,
  `url` varchar(100) NOT NULL,
  `hd` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `artiest`
--

CREATE TABLE IF NOT EXISTS `artiest` (
  `id` int(11) NOT NULL,
  `artiest` varchar(100) NOT NULL DEFAULT '',
  `sArtiest` varchar(100) NOT NULL DEFAULT '',
  `albums` int(11) NOT NULL DEFAULT '0',
  `recensies` int(11) NOT NULL DEFAULT '0',
  `lijsten` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
  `id` int(11) NOT NULL,
  `genre` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `genre2recensent`
--

CREATE TABLE IF NOT EXISTS `genre2recensent` (
  `recensent_id` int(11) NOT NULL DEFAULT '0',
  `genre_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `kenmerk`
--

CREATE TABLE IF NOT EXISTS `kenmerk` (
  `id` int(11) NOT NULL,
  `kenmerk` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `kenmerk2recensent`
--

CREATE TABLE IF NOT EXISTS `kenmerk2recensent` (
  `recensent_id` int(11) NOT NULL DEFAULT '0',
  `kenmerk_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lijsten`
--

CREATE TABLE IF NOT EXISTS `lijsten` (
  `id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL DEFAULT '0',
  `lijst_id` int(11) NOT NULL DEFAULT '0',
  `ak` int(11) NOT NULL DEFAULT '0',
  `punten` int(11) NOT NULL DEFAULT '0',
  `pos` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lijstenb`
--

CREATE TABLE IF NOT EXISTS `lijstenb` (
  `id` int(11) NOT NULL,
  `soort_id` int(11) NOT NULL DEFAULT '0',
  `lijst` varchar(5) NOT NULL DEFAULT '',
  `jaar` int(4) NOT NULL DEFAULT '0',
  `bron` varchar(100) NOT NULL DEFAULT '',
  `omschrijving` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `type` char(3) NOT NULL DEFAULT '',
  `canon` tinyint(4) NOT NULL DEFAULT '0',
  `individueel` int(11) NOT NULL DEFAULT '0',
  `zichtbaar` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lijsteni`
--

CREATE TABLE IF NOT EXISTS `lijsteni` (
  `lijsten_id` int(11) NOT NULL DEFAULT '0',
  `recensent_id` int(11) NOT NULL DEFAULT '0',
  `pos` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recensent`
--

CREATE TABLE IF NOT EXISTS `recensent` (
  `id` int(11) NOT NULL,
  `nRecensie` int(11) NOT NULL DEFAULT '0',
  `recensent` varchar(100) NOT NULL DEFAULT '',
  `sRecensent` varchar(100) NOT NULL DEFAULT '',
  `aRecensent` varchar(20) NOT NULL DEFAULT '0',
  `url` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recensie`
--

CREATE TABLE IF NOT EXISTS `recensie` (
  `id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL DEFAULT '0',
  `tijdschrift_id` int(11) NOT NULL DEFAULT '0',
  `recensent_id` int(11) NOT NULL DEFAULT '0',
  `jaar` int(11) NOT NULL DEFAULT '0',
  `maand` int(11) NOT NULL DEFAULT '0',
  `nummer` varchar(10) NOT NULL DEFAULT '0',
  `waardering` float NOT NULL DEFAULT '0',
  `rubriek` char(3) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rubriek`
--

CREATE TABLE IF NOT EXISTS `rubriek` (
  `id` int(11) NOT NULL,
  `tijdschrift_id` int(11) NOT NULL DEFAULT '0',
  `aRubriek` varchar(6) NOT NULL DEFAULT '',
  `rubriek` varchar(25) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `soort`
--

CREATE TABLE IF NOT EXISTS `soort` (
  `soort_id` int(11) NOT NULL,
  `soort` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

INSERT INTO `soort` (`soort_id`, `soort`) VALUES
(1, 'Algemeen'),
(2, 'Blues'),
(3, 'C&W'),
(4, 'Dance'),
(5, 'Folk'),
(6, 'Funk'),
(7, 'Heavy'),
(8, 'Hiphop'),
(9, 'Jazz'),
(10, 'Punk'),
(11, 'Reggae'),
(12, 'Soul'),
(13, 'Soundtrack'),
(14, 'Underground'),
(15, 'World');

--
-- Table structure for table `tijdschrift`
--

CREATE TABLE IF NOT EXISTS `tijdschrift` (
  `id` int(11) NOT NULL,
  `tijdschrift` varchar(100) NOT NULL DEFAULT '',
  `waardering` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tijdschrift`
--

INSERT INTO `tijdschrift` (`id`, `tijdschrift`, `waardering`) VALUES
(1, 'Oor', 0),
(2, 'Heaven', 5),
(3, 'Aloha', 5),
(4, 'Mojo', 5),
(5, 'Aloha People and Music', 0),
(6, 'Revolver', 5),
(7, 'A1', 0),
(8, 'A2', 0),
(12, '1001 Songs', 0),
(9, 'Muziekweb (CDR)', 3),
(10, "Revolver's Lust for life", 5),
(11, 'Muziekweb CDR', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artiest_id` (`artiest_id`);

--
-- Indexes for table `artiest`
--
ALTER TABLE `artiest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genre2recensent`
--
ALTER TABLE `genre2recensent`
  ADD KEY `recensent_id` (`recensent_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `kenmerk`
--
ALTER TABLE `kenmerk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kenmerk2recensent`
--
ALTER TABLE `kenmerk2recensent`
  ADD KEY `recensent_id` (`recensent_id`),
  ADD KEY `kenmerk_id` (`kenmerk_id`);

--
-- Indexes for table `lijsten`
--
ALTER TABLE `lijsten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lijst_id` (`lijst_id`),
  ADD KEY `album_id` (`album_id`);

--
-- Indexes for table `lijstenb`
--
ALTER TABLE `lijstenb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `soort_id` (`soort_id`);

--
-- Indexes for table `lijsteni`
--
ALTER TABLE `lijsteni`
  ADD KEY `lijsten_id` (`lijsten_id`),
  ADD KEY `recensent_id` (`recensent_id`);

--
-- Indexes for table `recensent`
--
ALTER TABLE `recensent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recensie`
--
ALTER TABLE `recensie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `tijdschrift_id` (`tijdschrift_id`);

--
-- Indexes for table `rubriek`
--
ALTER TABLE `rubriek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tijdschrift_id` (`tijdschrift_id`);

--
-- Indexes for table `soort`
--
ALTER TABLE `soort`
  ADD PRIMARY KEY (`soort_id`);

--
-- Indexes for table `tijdschrift`
--
ALTER TABLE `tijdschrift`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `album`
--
ALTER TABLE `album`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `artiest`
--
ALTER TABLE `artiest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kenmerk`
--
ALTER TABLE `kenmerk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lijsten`
--
ALTER TABLE `lijsten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lijstenb`
--
ALTER TABLE `lijstenb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `recensent`
--
ALTER TABLE `recensent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `recensie`
--
ALTER TABLE `recensie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rubriek`
--
ALTER TABLE `rubriek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `soort`
--
ALTER TABLE `soort`
  MODIFY `soort_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tijdschrift`
--
ALTER TABLE `tijdschrift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
