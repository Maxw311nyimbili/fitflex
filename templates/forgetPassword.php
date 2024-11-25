<?php
// Include the Composer autoloader and database connection
require '../vendor/autoload.php'; // Adjust path if necessary
include 'db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the email from the form input
    $emailAddress = trim($_POST['email'] ?? '');

    if (empty($emailAddress)) {
        $errorMessage = "Please enter your email.";
    } else {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT user_id FROM usersflex WHERE email = ?");
        $stmt->bind_param("s", $emailAddress);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email found, create token and retrieve user ID
            $row = $result->fetch_assoc();
            $userId = (int)$row['user_id'];
            $token = bin2hex(random_bytes(32));

            // Store token in the database
            $stmt = $conn->prepare("INSERT INTO token (userID, token) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $token);
            $stmt->execute();

            // Send the password reset email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'pswdreset123@gmail.com';
                $mail->Password = 'rukgiuaxczcdcmje';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Email content
                $mail->setFrom('pswdreset123@gmail.com', 'Fitflex Gyms Password Recovery');
                $mail->addAddress($emailAddress);
                $mail->isHTML(true);
                $mail->Subject = 'Account Password Recovery';
                $mail->Body = 'Here is your recovery token: <b>' . $token . '</b>';
                $mail->AltBody = 'Here is your recovery token: ' . $token;

                $mail->send();

                // Redirect to the token page
                header("Location: token.php");
                exit;
            } catch (Exception $e) {
                $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $errorMessage = "Email not found.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link rel="stylesheet" href="../static/css/password.css">
    <style>
        body {
            text-align: center;
            font-family: 'Arial', sans-serif;
            background: #ffffff
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        h1 {
            color: #2d3436;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        form {
            background-color: #ffffff;
            padding: 30px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        form label {
            font-weight: bold;
            font-size: 1.1em;
            display: block;
            margin-bottom: 10px;
            color: #2d3436;
        }
        input[type="email"] {
            padding: 12px;
            width: calc(100% - 24px);
            margin: 10px 0;
            border: 1px solid #dcdde1;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }
        input[type="email"]:focus {
            border-color: #74b9ff;
            box-shadow: 0 0 8px rgba(116, 185, 255, 0.6);
        }
        button {
            padding: 12px 20px;
            background-color: #122331dc;
            color: #ffffff;
            font-weight: bold;
            font-size: 1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s, transform 0.2s;
        }
        button:hover {
            background-color: #2575fc;
            transform: scale(1.05);
        }
        button:active {
            transform: scale(1);
        }
        .message {
            margin-top: 20px;
            font-size: 1em;
        }
        .error {
            color: #e74c3c;
        }
        .success {
            color: #27ae60;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div>
        <h1>Forgot Password</h1>

        <?php if (isset($errorMessage)): ?>
            <p class="message error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="POST" action="forgetPassword.php">
            <div>
                <label for="email">Enter your email:</label>
                <input type="email" id="email" name="email" placeholder="example@example.com" required>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
