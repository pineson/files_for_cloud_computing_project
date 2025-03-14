<?php 
session_start(); 
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
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    if (isset($_POST['register'])) { 
        $username = $_POST['username']; 
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')"; 
        if ($conn->query($sql)) { 
            echo "Registration successful!"; 
        } else { 
            echo "Error: " . $sql . "<br>" . $conn->error; 
        } 
    } elseif (isset($_POST['login'])) { 
        $username = $_POST['username']; 
        $password = $_POST['password']; 
        $sql = "SELECT * FROM users WHERE username='$username'"; 
        $result = $conn->query($sql); 
        if ($result->num_rows > 0) { 
            $row = $result->fetch_assoc(); 
            if (password_verify($password, $row['password'])) { 
                $_SESSION['user_id'] = $row['id']; // Store user_id in session 
                $_SESSION['username'] = $username; 
                echo "Login successful!"; 
            } else { 
                echo "Invalid password!"; 
            } 
        } else { 
            echo "User not found!"; 
        } 
    } 
} 

if (isset($_SESSION['user_id'])) { 
    $user_id = $_SESSION['user_id']; 
    // Fetch only the logged-in user's data 
    $sql = "SELECT * FROM sensor_data WHERE user_id='$user_id' ORDER BY timestamp DESC"; 
    $result = $conn->query($sql); 
    // Prepare data for the chart 
    $labels = []; 
    $temperatureData = []; 
    $humidityData = []; 
    if ($result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) { 
            $labels[] = $row['timestamp']; 
            $temperatureData[] = $row['temperature']; 
            $humidityData[] = $row['humidity']; 
        } 
    } else { 
        echo "No data available."; 
    } 
} else { 
    echo ' 
    <h2>Register</h2> 
    <form method="post"> 
        <input type="text" name="username" placeholder="Username" required> 
        <input type="password" name="password" placeholder="Password" required> 
        <button type="submit" name="register">Register</button> 
    </form> 
    <h2>Login</h2> 
    <form method="post"> 
        <input type="text" name="username" placeholder="Username" required> 
        <input type="password" name="password" placeholder="Password" required> 
        <button type="submit" name="login">Login</button> 
    </form> 
    '; 
} 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>IoT Data Visualization</title> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <style> 
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
        } 
        .chart-container { 
            width: 80%; 
            margin: 0 auto; 
        } 
    </style> 
</head> 
<body> 
    <?php if (isset($_SESSION['user_id'])): ?> 
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2> 
        <div class="chart-container"> 
            <canvas id="iotChart"></canvas> 
        </div> 
        <script> 
            const labels = <?php echo json_encode($labels); ?>; 
            const temperatureData = <?php echo json_encode($temperatureData); ?>; 
            const humidityData = <?php echo json_encode($humidityData); ?>; 
            const ctx = document.getElementById('iotChart').getContext('2d'); 
            const iotChart = new Chart(ctx, { 
                type: 'line', 
                data: { 
                    labels: labels, 
                    datasets: [ 
                        { 
                            label: 'Temperature (°C)', 
                            data: temperatureData, 
                            borderColor: 'rgba(255, 99, 132, 1)', 
                            backgroundColor: 'rgba(255, 99, 132, 0.2)', 
                            borderWidth: 2 
                        }, 
                        { 
                            label: 'Humidity (%)', 
                            data: humidityData, 
                            borderColor: 'rgba(54, 162, 235, 1)', 
                            backgroundColor: 'rgba(54, 162, 235, 0.2)', 
                            borderWidth: 2 
                        } 
                    ] 
                }, 
                options: { 
                    scales: { 
                        x: { 
                            title: { 
                                display: true, 
                                text: 'Time' 
                            } 
                        }, 
                        y: { 
                            title: { 
                                display: true, 
                                text: 'Value' 
                            } 
                        } 
                    }, 
                    responsive: true, 
                    maintainAspectRatio: false 
                } 
            }); 
        </script> 
    <?php endif; ?> 
</body> 
</html> 
<?php 
$conn->close(); 
?> 
