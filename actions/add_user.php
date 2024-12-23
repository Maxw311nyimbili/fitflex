<?php
// Include the database connection file
require '../templates/db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_clean();

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);
    $role = htmlspecialchars($_POST['role']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); 
    $password = 'fitflex1234';  // Default password

    // Hash the password before storing it (for security)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email is already in use
    $stmt = $conn->prepare("SELECT COUNT(*) FROM usersflex WHERE email = ?");
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
    $query = $conn->prepare("INSERT INTO usersflex (firstName, lastName, email, password, height, weight, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssdds", $firstName, $lastName, $email, $hashedPassword, $height, $weight, $role);

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
