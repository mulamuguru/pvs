<?php
$host = 'localhost'; // Or your database server
$dbname = 'planet_victoria'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    // Create the PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>