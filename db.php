<?php
// db.php
$host = 'localhost';
$dbname = 'dbmusq7zsuew2r';
$username = 'uar8kmsrlijda';
$password = 'knczabmoocaw';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
