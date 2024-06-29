-- Active: 1719393315760@@127.0.0.1@3306
CREATE Table users(
    id INT PRIMARY KEY AUTO_INCREMENT,
    fristName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    phone VARCHAR(100) NOT NULL UNIQUE,
    address VARCHAR(100) NOT NULL,
    DOB DATE NOT NULL,
    nagritaNo VARCHAR(100) NOT NULL UNIQUE,
    licenseNo VARCHAR(100) NOT NULL UNIQUE,
    isRider ENUM('yes', 'no') DEFAULT 'no'
)

CREATE Table poolalerts(
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    source_address VARCHAR(100) NOT NULL,
    source_latitude DOUBLE 
)