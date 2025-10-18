-- SQL Command to create user_accesses table
CREATE TABLE user_accesses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    classmate TINYINT(1) NOT NULL DEFAULT 0,
    leaderboard TINYINT(1) NOT NULL DEFAULT 0,
    post TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    -- Foreign key constraint
    CONSTRAINT fk_user_accesses_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE CASCADE,
    
    -- Index for better performance
    INDEX idx_user_accesses_user_id (user_id)
);

-- Alternative command if you want to add the table to existing database
-- Make sure to replace 'your_database_name' with your actual database name
-- USE your_database_name;

-- Example insert statements (optional)
-- INSERT INTO user_accesses (user_id, classmate, leaderboard, post) VALUES (1, 1, 1, 0);
-- INSERT INTO user_accesses (user_id, classmate, leaderboard, post) VALUES (2, 0, 1, 1);
