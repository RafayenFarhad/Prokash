-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2025 at 04:01 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prokash`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_id` bigint(20) NOT NULL,
  `actor_user_id` int(11) DEFAULT NULL,
  `entity_type` varchar(40) DEFAULT NULL,
  `entity_id` bigint(20) DEFAULT NULL,
  `action` varchar(60) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `key_name` varchar(64) NOT NULL,
  `display_name` varchar(80) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `communities`
--

CREATE TABLE `communities` (
  `community_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `device_tokens`
--

CREATE TABLE `device_tokens` (
  `token_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fcm_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `flags`
--

CREATE TABLE `flags` (
  `flag_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` varchar(180) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lost_found`
--

CREATE TABLE `lost_found` (
  `lf_id` bigint(20) NOT NULL,
  `community_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_type` enum('lost','found') NOT NULL,
  `title` varchar(160) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `location` varchar(160) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `media_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` enum('image','video') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `moderation_actions`
--

CREATE TABLE `moderation_actions` (
  `mod_action_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `moderator_id` int(11) NOT NULL,
  `action` enum('approve','reject','mark_false','restore') NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` bigint(20) DEFAULT NULL,
  `message` varchar(200) NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` bigint(20) NOT NULL,
  `community_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(160) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `status` enum('pending','verified','rejected','false_info') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('citizen','moderator','admin') DEFAULT 'citizen',
  `language` enum('en') DEFAULT 'en',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `verifications`
--

CREATE TABLE `verifications` (
  `verification_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('verify','false') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Indexes for table `communities`
--
ALTER TABLE `communities`
  ADD PRIMARY KEY (`community_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `uniq_user_token` (`user_id`,`fcm_token`);

--
-- Indexes for table `flags`
--
ALTER TABLE `flags`
  ADD PRIMARY KEY (`flag_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lost_found`
--
ALTER TABLE `lost_found`
  ADD PRIMARY KEY (`lf_id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `moderation_actions`
--
ALTER TABLE `moderation_actions`
  ADD PRIMARY KEY (`mod_action_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `moderator_id` (`moderator_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_category_created` (`category_id`,`created_at`),
  ADD KEY `idx_geo` (`latitude`,`longitude`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `community_id` (`community_id`);

--
-- Indexes for table `verifications`
--
ALTER TABLE `verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD UNIQUE KEY `uniq_vote` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `communities`
--
ALTER TABLE `communities`
  MODIFY `community_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_tokens`
--
ALTER TABLE `device_tokens`
  MODIFY `token_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flags`
--
ALTER TABLE `flags`
  MODIFY `flag_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lost_found`
--
ALTER TABLE `lost_found`
  MODIFY `lf_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `media_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderation_actions`
--
ALTER TABLE `moderation_actions`
  MODIFY `mod_action_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verifications`
--
ALTER TABLE `verifications`
  MODIFY `verification_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD CONSTRAINT `device_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `flags`
--
ALTER TABLE `flags`
  ADD CONSTRAINT `flags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `flags_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `lost_found`
--
ALTER TABLE `lost_found`
  ADD CONSTRAINT `lost_found_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lost_found_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE;

--
-- Constraints for table `moderation_actions`
--
ALTER TABLE `moderation_actions`
  ADD CONSTRAINT `moderation_actions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderation_actions_ibfk_2` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE;

--
-- Constraints for table `verifications`
--
ALTER TABLE `verifications`
  ADD CONSTRAINT `verifications_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `verifications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
