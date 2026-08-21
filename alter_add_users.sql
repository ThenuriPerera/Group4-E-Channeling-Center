-- ================================================================
-- ONLY RUN THIS IF YOU ALREADY IMPORTED THE OLD database.sql
-- (i.e. your database already has doctors/appointments/admins tables
-- but no "users" table yet). Run this in phpMyAdmin's SQL tab while
-- echanneling_db is selected.
--
-- If you are setting up FRESH, ignore this file - just import the
-- updated database.sql instead, it already includes everything below.
-- ================================================================
USE echanneling_db;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE appointments
    ADD COLUMN user_id INT DEFAULT NULL AFTER doctor_id,
    ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL;
