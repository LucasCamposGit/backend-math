CREATE DATABASE mathdb;
use mathdb;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` varchar(255) NOT NULL,
    `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `subject`;
CREATE TABLE IF NOT EXISTS `subject` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` varchar(255),
    `summary` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `topics`;
CREATE TABLE IF NOT EXISTS `topics` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `subject_id` int NOT NULL,
    `title` varchar(255),
    `content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `topics_progress`;
CREATE TABLE IF NOT EXISTS `topics_progress` (
    `user_id` int NOT NULL,
    `topic_id` int NOT NULL,
    `done` boolean NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `topic_id` int NOT NULL,
    `question` text NOT NULL,
    `solution` text NOT NULL,
    `difficulty` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `question_progress`;
CREATE TABLE IF NOT EXISTS `question_progress` (
    `question_id` int NOT NULL,
    `user_id` int NOT NULL,
    `done` boolean NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `revision`;
CREATE TABLE IF NOT EXISTS `revision` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `question_id` int NOT NULL,
    `user_id` int NOT NULL,
    `ef` float NOT NULL,
    `rev_interval` int NOT NULL,
    `repetitions` int NOT NULL,
    `next_date` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
