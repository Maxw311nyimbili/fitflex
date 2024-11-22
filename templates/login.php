<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php'; // Include your database connection file

$error = ""; // Initialize an error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM usersflex WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true); // Secure session handling
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role']; // Automatically use role from DB
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['lastName'] = $user['lastName'];
                $_SESSION['email'] = $user['email'];

                header('Location: admin.php');
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid email.";
        }

        $stmt->close(); // Close statement
    }

    $conn->close(); // Close connection
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../static/css/nav.css">
    <link rel="stylesheet" href="../static/css/login.css">
    <script src="../static/scripts/login.js" defer></script>
    <script src="../static/scripts/nav.js" defer></script>
    <title>FitFlex | Login Page</title>
</head>
<body>
    <!-- Navigation Section -->
    <header class="header">
        <div><a href="./index.php"><img class="logo" src="../static/images/FitFlex.png" alt="website logo" width="70px"></a></div>
        <div class="hamburger">
            <div class="menu-btn">
                <div class="menu-btn_lines"></div>
            </div>
        </div>
        <nav>
            <div class="nav_links">
                <ul class="menu-items">
                    <li><a href="../index.php" class="menu-item">Home</a></li>
                    <li><a href="./about.php" class="menu-item">About Us</a></li>
                    <li><button class="sign-in-btn"><a href="./login.php" class="menu-item">Login</a></button></li>
                    <li><button class="sign-in-btn"><a href="./sign-up.php" class="menu-item">Sign-up</a></button></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Body section -->
    <div class="container">
        <div class="banner-login"></div>

        <div class="right-content">
            <div class="header-wrapper">
                <div class="heading" style="color: #122331;"><h1>WELCOME BACK!</h1></div>
            </div>

            <div class="inner-container">
                <!-- Form for login -->
                <form id="loginForm" method="POST" action="login.php">
                    <?php if (!empty($error)): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <div><input id="email" class="input_area" name="email" type="email" placeholder="Enter your email" required></div>
                    <div id="emailError" class="error-message"></div> <!-- Added error message div -->
                    <div><input id="password" class="input_area" name="password" type="password" placeholder="Enter your password" required></div>
                    <div id="passwordError" class="error-message"></div> <!-- Added error message div -->

                    <div class="btn-wrapper">
                        <input class="submit_btn" type="submit" value="Log in">
                        <div class="create"><p>Do not have an account?</p><a href="./sign-up.php">Sign-up</a></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
