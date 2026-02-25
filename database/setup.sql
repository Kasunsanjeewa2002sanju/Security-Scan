-- ==========================================================
-- Database Setup Script
-- Run this once to create the database and users table.
-- ==========================================================

-- 1. Create the database (skip if it already exists)
CREATE DATABASE IF NOT EXISTS `hacki_app`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- 2. Switch to the database
USE `hacki_app`;

-- 3. Create the users table
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT            NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(255)   NOT NULL,
  `username`   VARCHAR(100)   NOT NULL,
  `password`   VARCHAR(255)   NOT NULL,
  `created_at` TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email`    (`email`),
  UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
