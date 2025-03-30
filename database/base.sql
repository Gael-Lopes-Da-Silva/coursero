CREATE DATABASE coursero;
USE coursero;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    reference_file VARCHAR(255) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    exercise_id INT NOT NULL,
    language VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM('pending', 'running', 'done') NOT NULL,
    score INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (exercise_id) REFERENCES exercises(id)
);
