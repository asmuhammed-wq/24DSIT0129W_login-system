-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 06:29 PM
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
-- Database: `eduhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `course_code` varchar(50) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`, `teacher_id`) VALUES
(1, 'APPLIED STATISTICS', 'C04', 3),
(2, 'ART AND DESIGN', 'C02', 4),
(3, 'SOFTWARE ENGINEERING', 'C03', 6),
(5, 'SKILLS COURSE', 'C05', 6),
(7, 'COMPUTER SCIENCE', 'CO6', 10),
(8, 'COMPUTER MATHS', 'C07', 10);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`) VALUES
(1, 3, 1),
(0, 11, 7),
(0, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'TUMUSIIME ALLAN', 'allantumusiime@gmail.com', 'allantumusiime@gmail.com', 'allan', 'student', '2026-04-04 07:40:09'),
(2, 'ARTHUR TERO', 'arthurtero@gmail.com', 'arthurtero@gmail.com', 'tero', 'student', '2026-04-04 07:41:55');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_content`
--

CREATE TABLE `tbl_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_content`
--

INSERT INTO `tbl_content` (`id`, `title`, `description`, `image_url`) VALUES
(1, 'School updates', 'Welcome students', 'https://images.unsplash.com/photo-1580582932707-520aed937b7b'),
(2, 'Enrollments are on going ', 'Choose at your convenience', 'Admision.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `teecher_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `teecher_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'T001', 'MUSA HUSSEIN', 'musahussein@gmail.com', 'musa', 'teacher', '2026-04-04 07:55:34'),
(2, 'T002', 'JUUKO MUZAMIR', 'juukomuzamir@gmail.com', 'juuko', 'teacher', '2026-04-04 07:58:28'),
(3, 'T003', 'NAMUDDU SARAH', 'sarahnamuddu@gmail.com', 'sarah', 'teacher', '2026-04-04 07:59:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'MUHAMMED ASHIRAF', 'ashirafmuhammed@gmail.com', '$2y$12$XsHUtwSjo6XvAtwiVX.DL.G9XbpmQ.jl6gSaPqldAPBwI8OHOBnHe', 'admin', '2026-04-12 07:25:23'),
(2, 'TUMUSIIME ALLAN', 'allantumusiime@gmail.com', 'allan', 'student', '2026-04-12 07:27:03'),
(3, 'ARTHUR TERO', 'arthurtero05@gmail.com', 'tero', 'teacher', '2026-04-12 07:28:50'),
(4, 'MUSA HUSSEIN', 'musahussein@gmail.com', 'musa', 'teacher', '2026-04-12 07:32:36'),
(5, 'JUUKO MUZAMIR', 'juukomuzamir@gmail.com', 'juuko', 'teacher', '2026-04-12 07:33:59'),
(6, 'NAMUDDU SARAH', 'sarahnamuddu@gmail.com', 'sarah', 'teacher', '2026-04-12 07:35:45'),
(7, 'MANZI GODFREY', 'godfreymanzi@gmail.com', 'manzi', 'student', '2026-04-12 07:38:20'),
(8, 'MAYA ETHROT', 'mayaethrot@gmail.com', 'maya', 'teacher', '2026-04-12 07:39:39'),
(9, 'MUSUUZA EDDIE', 'musuuzaeddie@gmail.com', '$2y$10$XCy2.8YrlO5FZzhTHVQJQumnwVnooMr.UY4C0.HtFyu18fIbvj0ne', 'teacher', '2026-04-12 07:40:50'),
(10, 'MUNIR KHAN', 'munirkhan@gmail.com', '$2y$10$ct1LptZlLHR9WQSG/UN6E.WjqXQy3ieLU4LDO2WXX66Lalt9bmsoC', 'teacher', '2026-04-12 07:41:38'),
(11, 'MUHAMMED HAMISA', 'hamisamuhammed@gmail.com', '$2y$10$s2nDnRRTLG/RJFa8TeTNtuudZP4w9fjeRCEd85GIh3A2lMNQtdweO', 'student', '2026-04-12 07:51:00'),
(14, 'HAMISA NAZZAR', 'hamisanazzar@gmail.com', '$2y$10$lB8roYs8RUlOZRsh7S1iZukrRfTFyLMaHWBpU.7LcF5uBQpylWn.m', 'student', '2026-04-12 07:58:29'),
(15, 'FABIOLA DUACIOSA', 'fabioladuaciosa@gmail.com', '$2y$10$kC6.flXPhQVKaZp5mLSEZu/caalFTuScQ23KavjlUw3tRsVwWGKNa', 'student', '2026-04-12 08:01:53'),
(16, 'SHAHIDAH MAYA', 'shahidahmaya@gmail.com', '$2y$10$7bjm/8kmUXuFNYVz4/fjI.8tSeBkqqCcNnhF5MoG21yOiHn7iTPHq', 'student', '2026-04-12 16:17:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`id`) USING BTREE,
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `teacher_id_2` (`teacher_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`) USING BTREE;

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_content`
--
ALTER TABLE `tbl_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teechers_id` (`teecher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
