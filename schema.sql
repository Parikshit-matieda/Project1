-- Create database (run once)
CREATE DATABASE IF NOT EXISTS courses_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE courses_db;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional seed data
INSERT INTO courses (title, description) VALUES
('HTML Basics', 'Learn the building blocks of the web: tags, elements, structure.'),
('CSS Basics', 'Style your pages with selectors, box model, and layout.'),
('JavaScript Basics', 'Add interactivity with variables, functions, and DOM.');

-- OTPs for email verification/login (6-digit codes)
CREATE TABLE IF NOT EXISTS email_otps (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL,
  code CHAR(6) NOT NULL,
  purpose ENUM('signup','login','generic') NOT NULL DEFAULT 'generic',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP NOT NULL,
  used_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX idx_email_created (email, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


