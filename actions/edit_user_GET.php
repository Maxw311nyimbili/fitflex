<?php
// Include the database connection file
require '../templates/db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_clean();

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate and sanitize the user ID
    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        $userId = intval($_GET['id']); // Sanitize the ID

        // Debugging log
        error_log("Fetching data for user ID: $userId");

        // Prepare the query to fetch the user data
        $query = "SELECT user_id, CONCAT(firstName, ' ', lastName) AS name, email, role FROM usersflex WHERE user_id = ?";

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

                // Secure output to prevent XSS
                $user = [
                    'user_id' => $user['user_id'],
                    'name' => htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'),
                    'email' => htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'),
                    'role' => htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8')
                ];

                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }

            // Close the statement
            $stmt->close();
        } else {
            // Debugging log for statement preparation errors
            error_log("Statement Preparation Error: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing User ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
