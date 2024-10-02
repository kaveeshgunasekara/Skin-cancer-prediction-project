CREATE DATABASE skin_cancer_prediction;

USE skin_cancer_prediction;

CREATE TABLE predictions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    learning_rate FLOAT DEFAULT NULL,
    num_epochs INT DEFAULT NULL,
    batch_size INT DEFAULT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
