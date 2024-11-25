<?php
session_start(); // Start the session

if (isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
} else {
    // Redirect back if no email is found
    header("Location: forgetPassword.php");
    exit;
}
// Include the database connection file
include 'db_connect.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = trim($_POST['new-password'] ?? '');
    $confirmPassword = trim($_POST['confirm-password'] ?? '');

    if (empty($newPassword) || empty($confirmPassword)) {
        $errorMessage = "All fields are required.";
    } elseif (strlen($newPassword) < 8) {
        $errorMessage = "Password must be at least 8 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } elseif (empty($email)) {
        $errorMessage = "Invalid email. Please try again.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE usersflex SET password = ? WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $hashedPassword, $email);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $successMessage = "Password changed successfully! Redirecting to login page...";
                session_destroy(); // Clear session data
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000);
                      </script>";
            } else {
                $errorMessage = "Failed to update password. Please try again.";
            }
            $stmt->close();
        } else {
            $errorMessage = "Database error: Failed to prepare the statement.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../static/css/reset.css">
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>

        <?php if (isset($errorMessage)): ?>
            <p class="error-message" style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if (isset($successMessage)): ?>
            <p class="success-message" style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <form id="reset-form" method="POST">
            <div class="form-group">
                <label for="new-password">New Password:</label>
                <input type="password" id="new-password" name="new-password" placeholder="New password" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm password" required>
            </div>
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>
