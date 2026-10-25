CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(80) NOT NULL,
  username VARCHAR(24) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  mobile CHAR(10) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('student','admin') NOT NULL DEFAULT 'student',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_created (created_at), INDEX idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL UNIQUE,
  full_name VARCHAR(80) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  mobile CHAR(10) NOT NULL,
  course VARCHAR(100) NOT NULL,
  year_of_study TINYINT UNSIGNED NOT NULL,
  gender VARCHAR(30) NOT NULL DEFAULT 'Prefer not to say',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT chk_student_year CHECK (year_of_study BETWEEN 1 AND 4),
  INDEX idx_students_course_year (course,year_of_study)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description VARCHAR(2000) NOT NULL,
  category ENUM('Technical','Career','Cultural') NOT NULL,
  event_date DATE NOT NULL,
  poster_path VARCHAR(255) NULL,
  created_by INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_events_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_events_date (event_date), INDEX idx_events_category (category)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registrations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_event (student_id,event_id),
  CONSTRAINT fk_reg_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  INDEX idx_reg_registered (registered_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS remember_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  selector CHAR(18) NOT NULL UNIQUE,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_remember_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_remember_expiry (expires_at)
) ENGINE=InnoDB;

-- Public event JSON is in ../data/events.json; the API uses it as a first-run fallback until events are added to MySQL.


-- Sample records for the Events page and event-registration dropdown.
INSERT INTO events (title, description, category, event_date)
SELECT 'Tech Fest 2026', 'A two-day event featuring coding competitions, robotics exhibitions, gaming tournaments, and project showcases.', 'Technical', '2026-11-02'
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title = 'Tech Fest 2026');
INSERT INTO events (title, description, category, event_date)
SELECT 'Career Guidance Seminar', 'Industry professionals share career advice, interview tips, and information about the latest technology trends.', 'Career', '2026-11-07'
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title = 'Career Guidance Seminar');
INSERT INTO events (title, description, category, event_date)
SELECT 'Annual Cultural Fest', 'Enjoy music, dance, drama, and various cultural performances by students.', 'Cultural', '2026-11-15'
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title = 'Annual Cultural Fest');
INSERT INTO events (title, description, category, event_date)
SELECT 'Coding Workshop', 'A practical workshop where students learn programming concepts and solve coding problems.', 'Technical', '2026-11-20'
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title = 'Coding Workshop');
INSERT INTO events (title, description, category, event_date)
SELECT 'Resume Building Workshop', 'Learn how to create an effective resume and present your skills to recruiters.', 'Career', '2026-11-26'
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title = 'Resume Building Workshop');
INSERT INTO events (title, description, category, event_date)
SELECT 'Photography Competition', 'Students can participate in a photography competition and showcase their creativity.', 'Cultural', '2026-12-03'
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title = 'Photography Competition');
