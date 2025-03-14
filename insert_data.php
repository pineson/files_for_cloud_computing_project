<?php 
$servername = "mysql-container"; // Use the MySQL container name 
$username = "webuser"; 
$password = "webpassword"; 
$dbname = "iot_data"; 
// Create connection 
$conn = new mysqli($servername, $username, $password, $dbname); 
// Check connection 
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
} 
$temperature = $_POST['temperature']; 
$humidity = $_POST['humidity']; 
$user_id = $_POST['user_id']; 
$sql = "INSERT INTO sensor_data (temperature, humidity, user_id) VALUES ('$temperature', '$humidity', '$user_id')"; 
if ($conn->query($sql)) { 
    echo "Data inserted successfully"; 
} else { 
    echo "Error: " . $sql . "<br>" . $conn->error; 
} 
$conn->close(); 
?> 
