<?php
require '../templates/db_connect.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['id']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $name = $_POST['name'];
    $role = $_POST['role'];

    $splitName = explode(" ", $name, 2);
    $fname = $splitName[0];
    $lname = isset($splitName[1]) ? $splitName[1] : '';

    // Add debugging to check input values
    error_log("UserID: $userId, Email: $email, Name: $name, Role: $role, Fname: $fname, Lname: $lname");

    $query = "UPDATE usersflex SET role = ?, email = ?, firstName = ?, lastName = ? WHERE user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssi", $role, $email, $fname, $lname, $userId);  

        if ($stmt->execute()) {
            // Get more detailed error information
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully', 'affected_rows' => $stmt->affected_rows]);
            } else {
                // Check for potential reasons no rows were updated
                $error_info = $stmt->error;
                echo json_encode([
                    'success' => false, 
                    'message' => 'No changes made or user not found', 
                    'error_info' => $error_info,
                    'user_id' => $userId
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing query', 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>