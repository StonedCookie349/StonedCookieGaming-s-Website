<?php
// config/database.php

$dbHost = 'db5015911272.hosting-data.io';
$dbUser = 'dbu2997959';
$dbPassword = 'F3ngK0h101$';
$dbName = 'dbs12968178';

// Create a database connection
$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

// Check the database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>