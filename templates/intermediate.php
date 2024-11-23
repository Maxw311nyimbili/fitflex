<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   // Retrieve session data
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : null;


    $height = isset($_POST['height']) ? htmlspecialchars(trim($_POST['height'])) : null;
    $weight = isset($_POST['weight']) ? htmlspecialchars(trim($_POST['weight'])) : null;
    $age = isset($_POST['age']) ? htmlspecialchars(trim($_POST['age'])) : null;
    $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : null;


        // Check if the email already exists
        $emailQuery = "SELECT email FROM usersflex WHERE email = ?";
        $stmt = $conn->prepare($emailQuery);
        if (!$stmt) {
            die("Preparation failed: " . $conn->error);
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered.";
        } else {
          // Update the user's record in the database
                $query = "UPDATE usersflex 
                SET height = ?, weight = ?, age = ?
                WHERE email = ?";

                $stmt3 = $conn->prepare($query);

                if (!$stmt3) {
                die("Preparation failed: " . $conn->error);
                }

                // Bind the parameters (data types: i for integers, s for strings)
                $stmt3->bind_param("iiis", 
                $height, 
                $weight, 
                $age, 
                $email
                );


            if($stmt3->execute()){ 
                // Redirect on successful registration
                header('Location: login.php');
                exit();
            } else {
                $error = "Failed to register user. Please try again.";
            }
        }
        $stmt->close();
        $stmt3->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../static/css/nav.css">
  <link rel="stylesheet" href="../static/css/sign-up.css">
  <script src="../static/scripts/sign-up.js" defer></script>
  <script src="../static/scripts/nav.js" defer></script>
  <title>FitFlex | Sign-up Page</title>
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

<div class="container">
    <div class="banner-login"></div>
    <div class="right-content">
        <div class="header-wrapper">
            <div class="heading" style="color: #122331;"><h1>Additional Information</h1></div>
        </div>

        <div class="inner-container">
            <form id="registerForm" method="POST" action="intermediate.php">
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                

                <!-- New Fields for Height, Weight and Age -->
                <div><input type="number" name="height" placeholder="Height (cm)" required min="0" class="input_area"></div>
                <div><input type="number" name="weight" placeholder="Weight (kg)" required min="0" class="input_area"></div>
                <div><input type="number" name="age" placeholder="Age (years)" required min="0" class="input_area"></div>
            

                <!-- Submit Button -->
                 <div class="btn-wrapper">
                    <div><input type='submit' value='Done' class='submit_btn'></div>
                 </div>
                
            </form>
        </div>


    </div>


</div>

<script src="../static/scripts/sign-up.js"></script> <!-- Ensure this script is linked -->
</body>
</html>
