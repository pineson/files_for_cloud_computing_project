   CREATE DATABASE iot_data; 
   USE iot_data; 
   CREATE TABLE users ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     username VARCHAR(50) NOT NULL UNIQUE, 
     password VARCHAR(255) NOT NULL 
   ); 
   CREATE TABLE sensor_data ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     temperature FLOAT NOT NULL, 
     humidity FLOAT NOT NULL, 
     user_id INT, 
     timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
     FOREIGN KEY (user_id) REFERENCES users(id) 
   ); 