<?php

// Include your database configuration file
require_once 'config/database.php';

// Retrieve user input from POST request
$usernameOrEmail = $_POST['usernameOrEmail'];
$password = $_POST['password'];

// Validate credentials
if (empty($usernameOrEmail) || empty($password)) {
    // Return JSON response indicating failure
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Connect to the database
$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

// Check the database connection
if ($conn->connect_error) {
    // Return JSON response indicating failure
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Sanitize user input
$usernameOrEmail = $conn->real_escape_string($usernameOrEmail);

// Query the database to retrieve user information
$query = "SELECT * FROM users WHERE (username = '$usernameOrEmail' OR email = '$usernameOrEmail') AND password = '$password'";
$result = $conn->query($query);

// Check if the query was successful
if ($result) {
    // Check if a user with the provided credentials exists
    if ($result->num_rows > 0) {
        // Fetch user details
        $user = $result->fetch_assoc();

        // Simulate generating a session token (replace this with your actual session management logic)
        $sessionToken = bin2hex(random_bytes(16));

        // Store the session token in the session
        session_start();
        $_SESSION['sessionToken'] = $sessionToken;
        $_SESSION['username'] = $user['username'];

        // Return JSON response indicating success
        echo json_encode(['success' => true, 'username' => $user['username']]);
    } else {
        // Return JSON response indicating failure
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
    }
} else {
    // Return JSON response indicating failure
    echo json_encode(['success' => false, 'error' => 'Database query failed']);
}

// Close the database connection
$conn->close();

?>
