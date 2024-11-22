<?php
// Include the database connection file
require '../db/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $userId = intval($_POST['id']);  // Ensure ID is an integer
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  // Sanitize the email
    $name = $_POST['name'];

    // Split the name into first and last names
    $splitName = explode(" ", $name, 2); // Assumes "fname lname"
    $fname = $splitName[0];
    $lname = isset($splitName[1]) ? $splitName[1] : '';  // If there's no last name, assign an empty string

    // Prepare the SQL query to update the user data
    $query = "UPDATE users SET email = ?, fname = ?, lname = ? WHERE user_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the parameters
        $stmt->bind_param("sssi", $email, $fname, $lname, $userId);  // 'sssi' means string, string, string, integer

        // Execute the statement
        if ($stmt->execute()) {
            // Check if any rows were updated
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes made or user not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing query']);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>
