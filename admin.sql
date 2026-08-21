USE echanneling_db;

-- Check if admin exists
SELECT * FROM admins;

-- If not exists, insert admin
INSERT INTO admins (username, password) 
VALUES ('admin', 'admin123');

-- If exists, update password
UPDATE admins 
SET password = '$2y$10$WZUJnUcP3b.qWEGvAap.0OOLvy47bASLSMWcn5Do3Rm5o8hV/Fm9e' 
WHERE username = 'admin';

USE echanneling_db;

-- Delete existing admin
DELETE FROM admins WHERE username = 'admin';

-- Insert new admin with proper hash
INSERT INTO admins (username, password) 
VALUES ('admin', '$2y$10$WZUJnUcP3b.qWEGvAap.0OOLvy47bASLSMWcn5Do3Rm5o8hV/Fm9e');

-- Verify
SELECT * FROM admins;

