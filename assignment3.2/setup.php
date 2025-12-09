<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$port = 8889; // Default MAMP MySQL port
$charset = 'utf8mb4';

try {
    // Connect without DB first to create it
    $pdo = new PDO("mysql:host=$host;port=$port;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents('setup.sql');

    // Execute the SQL commands
    $pdo->exec($sql);

    echo "<div style='font-family: sans-serif; padding: 20px; background: #d4edda; color: #155724; border-radius: 5px;'>";
    echo "<h1>Setup Successful!</h1>";
    echo "<p>Database 'assignment3_app' and tables have been created.</p>";
    echo "<p><a href='index.php'>Go to Login Page</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='font-family: sans-serif; padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px;'>";
    echo "<h1>Setup Failed</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please ensure MAMP is running and the MySQL password is 'root'.</p>";
    echo "</div>";
}
?>