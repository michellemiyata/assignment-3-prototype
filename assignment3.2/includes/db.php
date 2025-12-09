<?php
$host = 'localhost';
$db = 'assignment3_app';
$user = 'root';
$pass = 'root';
$port = 8889; // Default MAMP MySQL port
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Check if the error is "Unknown database" (Code 1049)
    if ($e->getCode() == 1049) {
        die("
            <div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                <h1>Database Not Found</h1>
                <p>The database <b>assignment3_app</b> does not exist yet.</p>
                <p>Please run the setup script to create it.</p>
                <a href='setup.php' style='background: #6C63FF; color: white; padding: 12px 24px; text-decoration: none; border-radius: 50px; display: inline-block; margin-top: 20px;'>Run Setup Now</a>
            </div>
        ");
    }

    // In a real app, log this error instead of showing it
    // For this assignment, we might want to see if connection fails
    die("Database connection failed: " . $e->getMessage() . " <br>Please ensure you have created the database using the setup.sql file.");
}
?>