 -- Create Database
CREATE DATABASE FitFlex;

-- Use the Database
USE FitFlex;

-- Create Gym Table
CREATE TABLE Gym (
   gym_id INT AUTO_INCREMENT PRIMARY KEY,
   gym_name VARCHAR(100) NOT NULL,
   gym_location VARCHAR(255) NOT NULL,
   services_offered TEXT NOT NULL,
   gym_contact VARCHAR(15),
   email VARCHAR(100),
   opening_time TIME NOT NULL DEFAULT '00:00:00',
   closing_time TIME NOT NULL DEFAULT '23:59:59',
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Users Table
CREATE TABLE Users (
   user_id INT AUTO_INCREMENT PRIMARY KEY,
   firstName VARCHAR(100) NOT NULL,
   lastName VARCHAR(50) NOT NULL,
   email VARCHAR(100) UNIQUE NOT NULL,
   password VARCHAR(255) NOT NULL,
   gender ENUM('male', 'female') DEFAULT NULL,
   height DECIMAL(5,2) DEFAULT NULL,
   weight DECIMAL(5,2) DEFAULT NULL,
   age INT DEFAULT NULL,
   join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   role ENUM('trainer', 'trainee') DEFAULT 'trainee',
   gym_id INT DEFAULT NULL,
   specialization VARCHAR(100), -- Added specialization for trainers
   bio TEXT,

   FOREIGN KEY (gym_id) REFERENCES Gym(gym_id) ON DELETE SET NULL
);

-- Create Progress Table
CREATE TABLE Progress (
   progress_id INT AUTO_INCREMENT PRIMARY KEY,
   user_id INT NOT NULL,
   date DATE NOT NULL,
   weight DECIMAL(5,2),
   height DECIMAL(5,2),
   workout_details TEXT,
   notes TEXT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

   FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Workouts Table
CREATE TABLE Workouts (
   workout_id INT AUTO_INCREMENT PRIMARY KEY,
   user_id INT NOT NULL,
   workout_date DATE NOT NULL,
   exercise_name VARCHAR(100) NOT NULL,
   sets INT NOT NULL,
   reps INT NOT NULL,
   duration INT DEFAULT NULL, -- Duration in minutes
   notes TEXT DEFAULT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

   FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Messages Table
CREATE TABLE Messages (
   message_id INT AUTO_INCREMENT PRIMARY KEY,
   sender_id INT NOT NULL,
   receiver_id INT NOT NULL,
   message_text TEXT NOT NULL,
   timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

   FOREIGN KEY (sender_id) REFERENCES Users(user_id) ON DELETE CASCADE,
   FOREIGN KEY (receiver_id) REFERENCES Users(user_id) ON DELETE CASCADE
);
