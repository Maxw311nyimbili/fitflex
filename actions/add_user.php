<?php
// Include the database connection file
require '../templates/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $gender = htmlspecialchars($_POST['gender']);
    $weight = htmlspecialchars($_POST['weight']);
    $age = htmlspecialchars($_POST['age']);
    $role = htmlspecialchars($_POST['role']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); 
    $password = 'fitflex1234';  // Default password
    $role = intval($_POST['role']); 

    // Hash the password before storing it (for security)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email is already in use
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        exit;
    }

    // Prepare the SQL query to insert the new user
    $query = $conn->prepare("INSERT INTO usersflex (firstName, lastName, email, password, gender, height, weight, age, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("sssssiiis", $firstName, $lastName, $email, $hashedPassword, $gendar, $height, $weight, $age, $role);

    // Execute the query and check if it was successful
    if ($query->execute()) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user']);
    }

    // Close the statement
    $query->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>