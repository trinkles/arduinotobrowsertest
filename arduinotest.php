<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "your_database_name";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === FALSE) {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($database);

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS arduino_data (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    value1 INT(11) NOT NULL,
    value2 INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Retrieve Arduino values via Wi-Fi
$arduinoValue1 = isset($_GET['value1']) ? $_GET['value1'] : null;
$arduinoValue2 = isset($_GET['value2']) ? $_GET['value2'] : null;

// Validate and sanitize the values
$arduinoValue1 = filter_var($arduinoValue1, FILTER_VALIDATE_INT);
$arduinoValue2 = filter_var($arduinoValue2, FILTER_VALIDATE_INT);

// Ensure the values are not null and valid
if ($arduinoValue1 !== false && $arduinoValue2 !== false) {
    // Store values in the database
    $stmt = $conn->prepare("INSERT INTO arduino_data (value1, value2) VALUES (?, ?)");
    $stmt->bind_param("ii", $arduinoValue1, $arduinoValue2);
    
    if ($stmt->execute()) {
        echo "Values stored in the database successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid values received from Arduino.";
}

// Display values from the database
$sql = "SELECT * FROM arduino_data";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>Value 1</th><th>Value 2</th></tr>";
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["value1"] . "</td><td>" . $row["value2"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No values found in the database.";
}

// Close the database connection
$conn->close();
?>
