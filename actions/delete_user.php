<?php
require '../templates/db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); 

ob_clean();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    parse_str(file_get_contents("php://input"), $_DELETE);
    
    if (isset($_DELETE['id'])) {
        $userId = intval($_DELETE['id']);

        $stmt = $conn->prepare("DELETE FROM usersflex WHERE user_id = ?");
        
        if ($stmt) {
            $stmt->bind_param("i", $userId);

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
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>