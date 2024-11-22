<?php
require '../db/db_connection.php';

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    parse_str(file_get_contents("php://input"), $_DELETE);
    
    if (isset($_DELETE['id'])) {
        $userId = intval($_DELETE['id']);

        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        
        if ($stmt) {
            $stmt->bind_param("i", $userId); // Bind the user ID parameter

            // Execute the query and check if it was successful
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare the query']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
    }
} else {
    // Invalid request method, return JSON error
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

