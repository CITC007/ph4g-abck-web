-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 11:17 AM
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
-- Database: `ph4g-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_23_033622_create_students_table', 1),
(5, '2025_06_23_033622_create_teachers_table', 1),
(6, '2025_06_23_034959_create_scores_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `point` int(11) NOT NULL DEFAULT 1,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id`, `student_id`, `teacher_id`, `reason`, `point`, `month`, `year`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'ช่วยเก็บของในห้องเรียน', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(2, 2, 1, 'ทำความสะอาดโต๊ะ', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(3, 3, 1, 'ตั้งใจเรียน', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(4, 4, 1, 'ส่งการบ้านครบ', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(5, 5, 1, 'เป็นผู้นำกลุ่ม', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(6, 6, 2, 'ช่วยเพื่อนเรียน', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(7, 7, 2, 'ทำเวรประจำวัน', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(8, 8, 2, 'ไม่รบกวนเพื่อน', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(9, 9, 2, 'ช่วยคุณครูยกของ', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(10, 10, 2, 'ดูแลความสะอาดห้องน้ำ', 1, 6, 2025, '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(11, 1, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:23:55', '2025-06-22 21:23:55'),
(12, 2, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:23:55', '2025-06-22 21:23:55'),
(13, 3, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:23:55', '2025-06-22 21:23:55'),
(14, 4, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:23:55', '2025-06-22 21:23:55'),
(15, 5, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:23:55', '2025-06-22 21:23:55'),
(16, 1, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:25:10', '2025-06-22 21:25:10'),
(17, 2, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:25:10', '2025-06-22 21:25:10'),
(18, 3, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:25:10', '2025-06-22 21:25:10'),
(19, 4, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:25:10', '2025-06-22 21:25:10'),
(20, 5, 1, 'กกกกกกกกกกก', 1, 6, 2025, '2025-06-22 21:25:10', '2025-06-22 21:25:10'),
(21, 1, 1, 'ddd', 1, 6, 2025, '2025-06-22 21:43:52', '2025-06-22 21:43:52'),
(22, 3, 1, 'เพิ่มคะแนนให้น้อง ซี', 1, 6, 2025, '2025-06-22 21:45:27', '2025-06-22 21:45:27'),
(23, 4, 1, '1000', 1, 6, 2025, '2025-06-22 22:04:15', '2025-06-22 22:04:15'),
(24, 7, 1, 'กกก', 1, 6, 2025, '2025-06-22 22:04:53', '2025-06-22 22:04:53'),
(25, 7, 1, 'กกก', 1, 6, 2025, '2025-06-22 22:04:57', '2025-06-22 22:04:57'),
(26, 1, 4, 'ครูโด', 1, 6, 2025, '2025-06-22 23:07:52', '2025-06-22 23:07:52'),
(27, 7, 4, 'ครูโดให้คะแนน', 1, 6, 2025, '2025-06-22 23:08:28', '2025-06-22 23:08:28'),
(28, 1, 5, 'ครูดาวให้คะแนน', 1, 6, 2025, '2025-06-22 23:17:41', '2025-06-22 23:17:41'),
(29, 1, 1, 'ครูจินเพ่ิมคะแนนเดี่ยว', 1, 6, 2025, '2025-06-23 00:49:50', '2025-06-23 00:49:50'),
(30, 1, 1, 'แบบกลุ่มทดสอบครูจิน', 1, 6, 2025, '2025-06-23 01:16:31', '2025-06-23 01:16:31'),
(31, 2, 1, 'แบบกลุ่มทดสอบครูจิน', 1, 6, 2025, '2025-06-23 01:16:31', '2025-06-23 01:16:31'),
(32, 3, 1, 'แบบกลุ่มทดสอบครูจิน', 1, 6, 2025, '2025-06-23 01:16:31', '2025-06-23 01:16:31'),
(33, 4, 1, 'แบบกลุ่มทดสอบครูจิน', 1, 6, 2025, '2025-06-23 01:16:31', '2025-06-23 01:16:31'),
(34, 5, 1, 'แบบกลุ่มทดสอบครูจิน', 1, 6, 2025, '2025-06-23 01:16:31', '2025-06-23 01:16:31'),
(35, 1, 1, 'แบบเดี่ยวพอได้ไหม', 1, 6, 2025, '2025-06-23 01:16:46', '2025-06-23 01:16:46');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('LXyHkBl5A3L4onBV2TgiRLbazsGTWHWnuQotRhmA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0hxbUxNa2dtbXhuNkhRTEpyenI1eE9WVlZGV0VGS0R0anViRTdWZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zY29yZS1lbnRyeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750657736),
('z0xCmnFc3w2DXcweSmyu37ir9i6Iz4zlkSYWI6uU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiNkYwUDNTaWxsQ3RRYVNYQ0lOQ3ZBc0gwbGZCWGNvS29yQktsTzIwaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6ODY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXBvcnQvY2xhc3Mtc2NvcmVzP2NsYXNzX3Jvb209JUUwJUI4JTlCLjElMkYxJm1vbnRoPTYmeWVhcj0yMDI1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMDoidGVhY2hlcl9pZCI7aToxO3M6MTI6InRlYWNoZXJfbmFtZSI7czozOToi4LiE4Lij4Li54LiI4Li04LiZ4LiU4Liy4Lij4Lix4LiV4LiZ4LmMIjtzOjE4OiJ0ZWFjaGVyX2NsYXNzX3Jvb20iO3M6Nzoi4LibLjEvMSI7fQ==', 1750669557);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_code` varchar(255) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `class_room` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_code`, `student_name`, `class_room`, `created_at`, `updated_at`) VALUES
(1, 'S001', 'เด็กชายเอ', 'ป.1/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(2, 'S002', 'เด็กหญิงบี', 'ป.1/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(3, 'S003', 'เด็กชายซี', 'ป.1/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(4, 'S004', 'เด็กหญิงดี', 'ป.1/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(5, 'S005', 'เด็กชายอี', 'ป.1/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(6, 'S006', 'เด็กหญิงแอฟ', 'ป.2/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(7, 'S007', 'เด็กชายกร', 'ป.2/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(8, 'S008', 'เด็กหญิงขวัญ', 'ป.2/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(9, 'S009', 'เด็กชายจิ๋ว', 'ป.2/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(10, 'S010', 'เด็กหญิงแจ๋ว', 'ป.2/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_name` varchar(255) NOT NULL,
  `class_room` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `teacher_name`, `class_room`, `created_at`, `updated_at`) VALUES
(1, 'ครูจินดารัตน์', 'ป.1/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(2, 'ครูสมใจ', 'ป.2/1', '2025-06-23 04:21:59', '2025-06-23 04:21:59'),
(4, 'ครูโด', 'ป.1/1', '2025-06-22 23:07:31', '2025-06-22 23:07:31'),
(5, 'ครูดาวคนสวย', 'อนุบาลห้อง3', '2025-06-22 23:17:27', '2025-06-22 23:17:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scores_student_id_foreign` (`student_id`),
  ADD KEY `scores_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_code_unique` (`student_code`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scores_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
