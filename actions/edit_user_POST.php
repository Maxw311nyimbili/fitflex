<?php
require '../templates/db_connect.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_clean();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['id']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $role = strtolower(trim($_POST['role']));

    $splitName = explode(" ", $name, 2);
    $fname = $splitName[0];
    $lname = isset($splitName[1]) ? $splitName[1] : '';

    // Add debugging logs for inputs
    error_log("UserID: $userId, Email: $email, Name: $name, Role: $role, Fname: $fname, Lname: $lname");

    // Validate role against allowed values
    $allowedRoles = ['super_admin', 'trainer', 'trainee'];
    if (!in_array($role, $allowedRoles)) {
        echo json_encode(['success' => false, 'message' => 'Invalid role value']);
        exit;
    }

    $query = "UPDATE usersflex SET role = ?, email = ?, firstName = ?, lastName = ? WHERE user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssi", $role, $email, $fname, $lname, $userId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes made or user not found']);
            }
        } else {
            // Log SQL execution errors
            error_log("Query Execution Error: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Query execution failed', 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        error_log("Statement Preparation Error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
