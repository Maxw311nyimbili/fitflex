<?php
// Include the database connection file
require '../db/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the user ID from the query string
    if (isset($_GET['id'])) {
        $userId = intval($_GET['id']);  // Sanitize the ID

        // Prepare the query to fetch the user data
        $query = "SELECT user_id, CONCAT(fname, ' ', lname) AS name, email FROM users WHERE user_id = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($query)) {
            // Bind the parameter
            $stmt->bind_param("i", $userId);  // 'i' means integer

            // Execute the statement
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            // Check if user data was found
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }

            // Close the statement
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>


