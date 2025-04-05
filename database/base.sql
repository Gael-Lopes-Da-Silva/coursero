CREATE DATABASE coursero;
USE coursero;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') DEFAULT "student"
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    reference_file VARCHAR(255) NOT NULL,
    args JSON NOT NULL DEFAULT '[]',
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

INSERT INTO courses (name, description) VALUES
('Python Basics', 'Introduction to Python programming.'),
('Web Development', 'Learn HTML, CSS, and JavaScript.'),
('Data Structures', 'Understanding algorithms and data structures.'),
('Databases', 'Learn SQL and database design.'),
('Machine Learning', 'Introduction to ML concepts and models.'),
('Cybersecurity', 'Basics of cybersecurity and ethical hacking.'),
('Mobile Development', 'Build apps for Android and iOS.'),
('Game Development', 'Learn game programming with Unity.'),
('Cloud Computing', 'Introduction to cloud services and architectures.'),
('DevOps', 'CI/CD, automation, and infrastructure management.');

INSERT INTO exercises (course_id, name, reference_file) VALUES
(1, 'Python Basics - Variables', 'python_basics_01.py'),
(1, 'Python Basics - Functions', 'python_basics_02.py'),
(2, 'HTML Basics', 'webdev_01.html'),
(2, 'CSS Styling', 'webdev_02.css'),
(3, 'Data Structures - Arrays', 'data_structures_01.c'),
(3, 'Data Structures - Linked Lists', 'data_structures_02.cpp'),
(4, 'SQL Introduction', 'databases_01.sql'),
(4, 'Advanced SQL Queries', 'databases_02.sql'),
(5, 'Intro to Machine Learning', 'machine_learning_01.ipynb'),
(5, 'Neural Networks', 'machine_learning_02.ipynb');
