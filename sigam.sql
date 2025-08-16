-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 16, 2025 at 05:51 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sigam`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminId` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `kategori` enum('admin') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminId`, `username`, `email`, `password`, `foto`, `tanggal_dibuat`, `kategori`) VALUES
(1, 'Rahmawati', 'admin@gmail.com', 'admin123', 'upload/profile/admin_688b141ec7730.jpeg', '2025-07-29 00:00:00', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `detail_soal`
--

CREATE TABLE `detail_soal` (
  `detail_id` int UNSIGNED NOT NULL,
  `soal_id` int UNSIGNED NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban` varchar(255) NOT NULL,
  `skor` smallint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_soal`
--

INSERT INTO `detail_soal` (`detail_id`, `soal_id`, `pertanyaan`, `jawaban`, `skor`) VALUES
(1, 1, '2 + 3', '5', 5),
(2, 1, '5 + 4', '9', 5),
(3, 1, '10 + 2', '12', 5),
(4, 2, '3 x 2', '6', 5),
(5, 2, '4 x 5', '20', 5),
(6, 2, '6 x 6', '36', 5),
(11, 6, '1 + 2', '3', 5),
(12, 6, '1 + 3', '4', 5),
(13, 6, '1 + 4', '5', 5),
(14, 6, '1 + 5', '6', 5),
(15, 6, '1 + 6', '6', 5),
(17, 7, '9 + 2', '11', 5),
(18, 7, '8 + 1', '9', 5),
(19, 8, '10 x 1', '10', 5),
(20, 8, '1 x 1', '1', 5),
(21, 8, '5 x 1', '5', 5),
(22, 8, '6 x 1', '6', 5),
(23, 8, '7 x 1', '7', 5),
(24, 8, '20 x 1', '20', 5),
(25, 8, '13 x 1', '13', 5),
(27, 10, '11 + 22', '33', 10),
(28, 10, '22 + 1', '23', 7),
(29, 10, '15 + 15', '30', 8),
(30, 10, '10 + 2', '12', 5),
(31, 10, '10 + 10', '20', 6),
(33, 9, '11 + 11', '22', 11),
(34, 11, '1 x 1', '1', 5),
(35, 11, '1 x 2', '2', 5),
(36, 11, '1 x 3', '3', 5),
(37, 11, '1 x 4', '4', 5),
(38, 11, '1 x 5', '5', 5),
(39, 11, '1 x 6', '6', 5),
(40, 11, '1 x 7', '7', 5),
(41, 11, '1 x 8', '8', 5),
(42, 11, '1 x 10', '10', 5);

-- --------------------------------------------------------

--
-- Table structure for table `hasil_permainan`
--

CREATE TABLE `hasil_permainan` (
  `id` int UNSIGNED NOT NULL,
  `siswa_id` int DEFAULT NULL,
  `soal_id` int UNSIGNED DEFAULT NULL,
  `skor` int UNSIGNED NOT NULL,
  `jawaban_benar` int UNSIGNED NOT NULL,
  `total_pertanyaan` int UNSIGNED NOT NULL,
  `tingkat_kesulitan` varchar(50) NOT NULL,
  `tanggal_main` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hasil_permainan`
--

INSERT INTO `hasil_permainan` (`id`, `siswa_id`, `soal_id`, `skor`, `jawaban_benar`, `total_pertanyaan`, `tingkat_kesulitan`, `tanggal_main`) VALUES
(27, 42, 11, 100, 9, 9, 'easy', '2025-08-14 12:25:39'),
(29, 42, 9, 14, 1, 1, 'easy', '2025-08-14 13:39:27'),
(30, 40, 11, 100, 9, 9, 'easy', '2025-08-14 13:45:19');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `siswaId` int NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `tanggal_dibuat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `kategori` enum('siswa') NOT NULL DEFAULT 'siswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`siswaId`, `nisn`, `username`, `password`, `foto`, `kelas`, `tanggal_dibuat`, `kategori`) VALUES
(30, '0045678910', 'Ahmad Rizky Pratama', '$2y$10$98wEiY5rXJvBSup1Aw6wp.T0KZpGaZRDK8gh4RGlo6DJcvmwWDtza', 'upload/profile/siswa_6889e0b0af243.jpg', '3A', '2025-07-30 09:06:56', 'siswa'),
(31, '0045678911', 'Siti Nurhaliza', '$2y$10$rgC0DpHcI6xvl.Zd7JF0uuy//8KTdelNhDQZ1lCyUK6LKt.EDuu7a', 'upload/profile/siswa_6889e0ddbdca3.jpg', '3A', '2025-07-30 09:07:41', 'siswa'),
(32, '0045678912', 'Budi Santoso', '$2y$10$7SpRlW6VM6S3pk1HZ8P/SORlkH7Dg0oXnwhYLysOkZQ9taGw5OY5W', 'upload/profile/siswa_6889e13eeddc0.jpg', '3B', '2025-07-30 09:09:18', 'siswa'),
(33, '0045678913', 'Dina Amalia Putri', '$2y$10$SCY/pjckZMbscLknqqZhUuF1sXrvrlGZklL6N5wuEFgUuAF18j9Fa', 'upload/profile/siswa_6889e15e35531.jpg', '4A', '2025-07-30 09:09:50', 'siswa'),
(34, '0045678914', 'Fajar Nugroho2', '$2y$10$hbp9cwqT.VhIEqZc.pEqee/68/WiVccmeHEa1iEPeHDrvHpeXIXb2', 'upload/profile/siswa_688b4ea934c79.jpeg', '5A', '2025-07-30 09:10:08', 'siswa'),
(35, '0045678915', 'Indah Permata Sari', '$2y$10$kIUCnuaUPVJSnR2fPRvtd.M.LyF5wktA.AO1WbaA.FU308EJnXGn6', 'upload/profile/siswa_6889e181de5e2.jpg', '4A', '2025-07-30 09:10:25', 'siswa'),
(37, '00993322', 'Faisal A. Lagonah', '$2y$10$JBIFZBmBdE0.zapybYHlHeY/F.zHmwaLtwmVdh.o6P0yCWs18nk.K', 'upload/profile/siswa_688b0f0bb271d.jpg', '5a', '2025-07-31 06:36:59', 'siswa'),
(38, '009923212', 'Sifa Hadju', '$2y$10$a7WHcF4SphJL8IcW9hUvV.pQZo8Aw5sYROey43Ag/2RLtBFHhA3QK', 'upload/profile/siswa_688b7631ca0cc.jpg', '3B', '2025-07-31 13:57:05', 'siswa'),
(39, '00115244233', 'Andalas2', '$2y$10$dL8LXE9.8idHY5MhG9JGAOUhWH5gwSkwIZQqgaVLAp/iFjzb.DZ3i', 'upload/profile/siswa_688b765aa7a23.png', '3D', '2025-07-31 13:57:46', 'siswa'),
(40, '0088444511', 'Enjelita', '$2y$10$g7OZJ1d58ZcZ5cWclQHXreEVBe3MCeBvYzCijsfXuIymIXAhW2sSa', 'upload/profile/siswa_688c260f754b6.jpg', '5B', '2025-07-31 14:26:01', 'siswa'),
(42, '11223344', 'Matt', '$2y$10$.2KZT8vWiuTOe912aN2wUOa3XxSyysi93K5bXrFwlpm.0oUqteYOS', 'upload/profile/profile_688f18206225d.jpeg', '3B', '2025-08-01 02:28:41', 'siswa');

-- --------------------------------------------------------

--
-- Table structure for table `soal`
--

CREATE TABLE `soal` (
  `soal_id` int UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `kelas` tinyint UNSIGNED NOT NULL,
  `tingkat` enum('Mudah','Sedang','Sulit') NOT NULL DEFAULT 'Mudah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `soal`
--

INSERT INTO `soal` (`soal_id`, `nama`, `kategori`, `kelas`, `tingkat`) VALUES
(1, 'Soal Penjumlahan Kelas 3', 'Penjumlahan', 3, 'Mudah'),
(2, 'Soal Perkalian Kelas 4', 'Perkalian', 4, 'Sedang'),
(6, 'Soal Perjumlahan Kelas 1', 'Perjumlahan', 2, 'Mudah'),
(7, 'Soal Perjumlahan kelas 2', 'Perjumlahan', 4, 'Sulit'),
(8, 'Soal Perkalian Kelas 5', 'Perkalian', 5, 'Mudah'),
(9, 'Soal penjumlahan kelas 3', 'Penjumlahan', 3, 'Sedang'),
(10, 'Soal Penjumlahan kelas 3', 'Penjumlahan', 3, 'Mudah'),
(11, 'Soal Perkalian Kelas 3', 'Perkalian', 3, 'Mudah');

-- --------------------------------------------------------

--
-- Table structure for table `walikelas`
--

CREATE TABLE `walikelas` (
  `walkesId` int NOT NULL,
  `nip` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tanggal_dibuat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `kategori` enum('wali kelas') NOT NULL DEFAULT 'wali kelas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `walikelas`
--

INSERT INTO `walikelas` (`walkesId`, `nip`, `username`, `email`, `password`, `tanggal_dibuat`, `kategori`) VALUES
(8, '198706272010122008', 'rahmat.hidayat', 'rahmat.hidayat@gmail.com', '123', '2025-07-30 13:07:51', 'wali kelas'),
(9, '197810062005121009', 'sri.wahyuni', 'sri.wahyuni@gmail.com', '123', '2025-07-30 13:07:51', 'wali kelas'),
(10, '198901292012121010', 'aditya.nugroho', 'aditya.nugroho@guru.sch.id', '123', '2025-07-30 13:07:51', 'wali kelas'),
(11, '197612202006041011', 'widya.susanti', 'widya.susanti@guru.sch.id', '123', '2025-07-30 13:29:05', 'wali kelas'),
(12, '198309172009122012', 'agus.pranoto', 'agus.pranoto@gmail.com', '123', '2025-07-30 13:29:05', 'wali kelas'),
(13, '197911112007012013', 'yuni.astuti', 'yuni.astuti@gmail.com', '123', '2025-07-30 13:29:05', 'wali kelas'),
(14, '198202052008032014', 'hendro.saputra', 'hendro.saputra@gmail.com', '123', '2025-07-30 13:29:05', 'wali kelas'),
(15, '198505062010122015', 'mira.hidayati', 'mira.hidayati@gmail.com', '$2y$10$imAC1nZvmEtHzh7ilub0DeWPwmpOr5xKmnp8VPt8LyC4rAinkux8C', '2025-07-30 13:29:05', 'wali kelas'),
(16, '1985050655555', 'Suci Awalia Laiya', 'suciawalia@gmail.com', '$2y$10$jgLPMMcl8uT0y/YK0Hl8W.9ET7MpAu1MtEcryO9W8IzMqI0rSGM3m', '2025-07-31 05:54:35', 'wali kelas'),
(17, '11188458453', 'Agus Smanto', 'Agus@gmail.com', '$2y$10$P67EfOYz8maojbvOD9Iej.jbxaLoNjhPhG6zMgmVFhBeSyD5rhU1i', '2025-07-31 14:45:06', 'wali kelas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `detail_soal`
--
ALTER TABLE `detail_soal`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `idx_soal_id` (`soal_id`);

--
-- Indexes for table `hasil_permainan`
--
ALTER TABLE `hasil_permainan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `fk_soal_id` (`soal_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`siswaId`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- Indexes for table `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`soal_id`);

--
-- Indexes for table `walikelas`
--
ALTER TABLE `walikelas`
  ADD PRIMARY KEY (`walkesId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_soal`
--
ALTER TABLE `detail_soal`
  MODIFY `detail_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `hasil_permainan`
--
ALTER TABLE `hasil_permainan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `siswaId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `soal`
--
ALTER TABLE `soal`
  MODIFY `soal_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `walikelas`
--
ALTER TABLE `walikelas`
  MODIFY `walkesId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_soal`
--
ALTER TABLE `detail_soal`
  ADD CONSTRAINT `fk_detail_soal_soal` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`soal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hasil_permainan`
--
ALTER TABLE `hasil_permainan`
  ADD CONSTRAINT `fk_soal_id` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`soal_id`),
  ADD CONSTRAINT `hasil_permainan_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswaId`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
