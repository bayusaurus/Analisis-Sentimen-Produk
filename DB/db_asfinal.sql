-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2019 at 02:13 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_asfinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `email`, `password`, `status`) VALUES
(1, 'Bayusaurus', 'bayusaurus18@gmail.com', 'a430e06de5ce438d499c2e4063d60fd6', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bobot_idf`
--

CREATE TABLE `bobot_idf` (
  `id_bobot` int(11) NOT NULL,
  `kata_bobot` varchar(25) NOT NULL,
  `bobot` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bobot_idf`
--

INSERT INTO `bobot_idf` (`id_bobot`, `kata_bobot`, `bobot`) VALUES
(1696, 'iphone', 0),
(1697, 'kamera', 0.30103),
(1698, 'bagus', 0.60206),
(1699, 'mantap', 0.60206),
(1700, 'boros', 0.30103),
(1701, 'buat', 0.60206),
(1702, 'gaming', 0.60206),
(1703, 'baterai', 0.60206);

-- --------------------------------------------------------

--
-- Table structure for table `hasil_uji_pengguna`
--

CREATE TABLE `hasil_uji_pengguna` (
  `id` int(11) NOT NULL,
  `komentar` varchar(280) NOT NULL,
  `sentimen` tinyint(1) NOT NULL,
  `kesesuaian` tinyint(1) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `kata`
--

CREATE TABLE `kata` (
  `id_kata` int(11) NOT NULL,
  `kata` varchar(25) NOT NULL,
  `id_komentar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kata`
--

INSERT INTO `kata` (`id_kata`, `kata`, `id_komentar`) VALUES
(13844, 'iphone', 515),
(13845, 'kamera', 515),
(13846, 'bagus', 515),
(13847, 'iphone', 516),
(13848, 'mantap', 516),
(13849, 'kamera', 516),
(13850, 'boros', 517),
(13851, 'iphone', 517),
(13852, 'buat', 517),
(13853, 'gaming', 517),
(13854, 'baterai', 518),
(13855, 'iphone', 518),
(13856, 'boros', 518);

-- --------------------------------------------------------

--
-- Table structure for table `komentar_latih`
--

CREATE TABLE `komentar_latih` (
  `id_komentar` int(11) NOT NULL,
  `komentar` varchar(280) NOT NULL,
  `sentimen` tinyint(1) NOT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `komentar_latih`
--

INSERT INTO `komentar_latih` (`id_komentar`, `komentar`, `sentimen`, `id_admin`) VALUES
(515, ' @bayu18 iphone kameranya bagus!', 1, 1),
(516, ' @zain_xx iphone mantap di kameranya', 1, 1),
(517, ' @nanda_loween boros iphone buat gaming', 0, 1),
(518, ' @Todd_AAP Baterai Iphone boros', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `bobot_idf`
--
ALTER TABLE `bobot_idf`
  ADD PRIMARY KEY (`id_bobot`);

--
-- Indexes for table `hasil_uji_pengguna`
--
ALTER TABLE `hasil_uji_pengguna`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kata`
--
ALTER TABLE `kata`
  ADD PRIMARY KEY (`id_kata`),
  ADD KEY `id_komentar` (`id_komentar`);

--
-- Indexes for table `komentar_latih`
--
ALTER TABLE `komentar_latih`
  ADD PRIMARY KEY (`id_komentar`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bobot_idf`
--
ALTER TABLE `bobot_idf`
  MODIFY `id_bobot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1704;

--
-- AUTO_INCREMENT for table `hasil_uji_pengguna`
--
ALTER TABLE `hasil_uji_pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kata`
--
ALTER TABLE `kata`
  MODIFY `id_kata` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13857;

--
-- AUTO_INCREMENT for table `komentar_latih`
--
ALTER TABLE `komentar_latih`
  MODIFY `id_komentar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=519;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kata`
--
ALTER TABLE `kata`
  ADD CONSTRAINT `kata_ibfk_1` FOREIGN KEY (`id_komentar`) REFERENCES `komentar_latih` (`id_komentar`);

--
-- Constraints for table `komentar_latih`
--
ALTER TABLE `komentar_latih`
  ADD CONSTRAINT `komentar_latih_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
