-- CREATE DATABASE candidate_management;

-- USE candidate_management;

-- CREATE TABLE candidates (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     name VARCHAR(255) NOT NULL,
--     email VARCHAR(255) NOT NULL,
--     position VARCHAR(255) NOT NULL,
--     status ENUM('selected', 'rejected') NOT NULL,
--     email_sent BOOLEAN DEFAULT FALSE,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- );

-- CREATE TABLE email_logs (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     candidate_id INT,
--     recipient_email VARCHAR(255) NOT NULL,
--     subject VARCHAR(500) NOT NULL,
--     body TEXT NOT NULL,
--     status ENUM('sent', 'failed') NOT NULL,
--     sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (candidate_id) REFERENCES candidates(id)
-- );
