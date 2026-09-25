<?php

// Include your database configuration file
require_once '../config/database.php';

// Retrieve user input from POST request
$username = $_POST['username'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];

// Validate input
if (empty($username) || empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
    // Return JSON response indicating failure
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Sanitize user input
$username = $conn->real_escape_string($username);
$firstName = $conn->real_escape_string($firstName);
$lastName = $conn->real_escape_string($lastName);
$email = $conn->real_escape_string($email);

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Check if the username or email is already registered
$checkQuery = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
$checkResult = $conn->query($checkQuery);

// Check if the query was successful
if ($checkResult) {
    // Check if a user with the provided username or email already exists
    if ($checkResult->num_rows > 0) {
        // Return JSON response indicating failure
        echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
    } else {
        // Insert the new user into the database
        $insertQuery = "INSERT INTO users (username, first_name, last_name, email, password) VALUES ('$username', '$firstName', '$lastName', '$email', '$hashedPassword')";
        $insertResult = $conn->query($insertQuery);

        // Check if the insertion was successful
        if ($insertResult) {
            // Simulate generating a session token (replace this with your actual session management logic)
            $sessionToken = bin2hex(random_bytes(16));

            // Store the session token in the session
            session_start();
            $_SESSION['sessionToken'] = $sessionToken;
            $_SESSION['username'] = $username;

            // Return JSON response indicating success
            echo json_encode(['success' => true, 'username' => $username]);
        } else {
            // Return JSON response indicating failure
            echo json_encode(['success' => false, 'error' => 'User registration failed']);
        }
    }
} else {
    // Return JSON response indicating failure
    echo json_encode(['success' => false, 'error' => 'Database query failed']);
}

// Close the database connection
$conn->close();

?>