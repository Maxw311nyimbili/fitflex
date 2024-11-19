<?php
// view_progress_pictures.php
require 'db_connect.php';

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Example query to retrieve progress pictures for the given user_id
    $sql = "SELECT image_url FROM user_images WHERE user_id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter (i for integer, since user_id is an integer)
    $stmt->bind_param("i", $user_id);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch all pictures
    $pictures = $result->fetch_all(MYSQLI_ASSOC);

    // Check if pictures were found and return the response
    if ($pictures) {
        echo json_encode(['success' => true, 'pictures' => $pictures]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No pictures found']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing user_id']);
}

?>
