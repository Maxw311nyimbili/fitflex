<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture and sanitize input data
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirmPassword']));
    $role = htmlspecialchars(trim($_POST['role'])); // Ensure role is dynamic (trainee/trainer)
    $gender = htmlspecialchars(trim($_POST['gender']));
    $gymIdToInsert = ($role === 'trainee') ? htmlspecialchars(trim($_POST['gymPreferred'])) : null;

    // For trainers, capture gym details
    $gymName = isset($_POST['gymName']) ? htmlspecialchars(trim($_POST['gymName'])) : null;
    $gymContact = isset($_POST['gymContact']) ? htmlspecialchars(trim($_POST['gymContact'])) : null;
    $gymLocation = isset($_POST['gymLocation']) ? htmlspecialchars(trim($_POST['gymLocation'])) : null;
    $servicesOffered = isset($_POST['servicesOffered']) ? htmlspecialchars(trim($_POST['servicesOffered'])) : null;

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

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
            // Insert user into usersflex table
            $query = "INSERT INTO usersflex (firstName, lastName, email, password, role, gender, gym_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $conn->prepare($query);

            if (!$stmt2) {
                die("Preparation failed: " . $conn->error);
            }

            $stmt2->bind_param(
                "ssssssi",
                $firstName,
                $lastName,
                $email,
                $hashedPassword,
                $role,
                $gender,
                $gymIdToInsert
            );

            if ($stmt2->execute()) {
                // Set session variables for the registered user
                $_SESSION['email'] = $email;
                $_SESSION['firstName'] = $firstName;
                $_SESSION['lastName'] = $lastName;
                $_SESSION['role'] = $role;

                // If trainer, insert gym details
                if ($role === 'trainer' && $gymName && $gymContact && $gymLocation && $servicesOffered) {
                    $gymQuery = "INSERT INTO gym (gym_name, gym_location, services_offered, gym_contact) VALUES (?, ?, ?, ?)";
                    $stmt3 = $conn->prepare($gymQuery);

                    if (!$stmt3) {
                        die("Preparation failed: " . $conn->error);
                    }

                    $stmt3->bind_param('ssss', $gymName, $gymLocation, $servicesOffered, $gymContact);
                    if (!$stmt3->execute()) {
                        die("Gym insertion failed: " . $conn->error);
                    }
                }

                // Redirect on successful registration
                header('Location: intermediate.php');
                exit();
            } else {
                $error = "Failed to register user. Please try again.";
            }
        }
    }
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
            <div class="heading" style="color: #122331;"><h1>Create an Account</h1></div>
        </div>

        <div class="inner-container">
            <form id="registerForm" method="POST" action="sign-up.php">
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div><input type="text" name="firstName" placeholder="First Name" required class="input_area"></div>
                <div><input type="text" name="lastName" placeholder="Last Name" required class="input_area"></div>
                <div><input type="email" name="email" placeholder="Email" required class="input_area"></div>
                <div><input type="password" name="password" placeholder="Password" required class="input_area"></div>
                <div><input type="password" name="confirmPassword" placeholder="Confirm Password" required class="input_area"></div>
                
                <!-- Gender Selection -->
                <div>
                    <select name="gender" required class='input_area'>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="select-role">
                        <!-- <input type="radio" id="gymOwner" name="role" value="gym_owner" required>
                        <label for="gymOwner" style="margin-right: 20px;">Gym Owner</label> -->
                        <input type="radio" id="trainee" name="role" value="trainee" required>
                        <label for="trainee">Select preferred gym</label><br><br>
                    </div>


                <!-- Hidden fields to capture trainer details -->
                <input type='hidden' name='gymName' id='gymNameHidden'>
                <input type='hidden' name='gymContact' id='gymContactHidden'>
                <input type='hidden' name='gymLocation' id='gymLocationHidden'>
                <input type='hidden' name='servicesOffered' id='servicesOfferedHidden'>

                <!-- Submit Button -->
                 <div class="btn-wrapper">
                    <div><input type='submit' value='Register' class='submit_btn'></div>
                    <div class="create"><p>Do not have an account?</p><a href="./login.php">Login</a></div>
                 </div>
                
            </form>
        </div>


    </div>
    
  <!-- Trainer Modal -->
  <div id='trainerModal' class='modal'>
      <div class='modal-content'>
          <span class='close'>&times;</span>
          <h2>Trainer Details</h2> <!-- Updated modal title -->
          <form id='trainerForm'> <!-- Updated form ID -->
              <input type='text' id='gymName' placeholder='Gym Name' required class='input_area-1'>
              <input type='text' id='gymContact' placeholder='Contact Details' required class='input_area-1'>
              <input type='text' id='gymLocation' placeholder='Gym Location' required class='input_area-1'>
              <input type='text' id='servicesOffered' placeholder='Services Offered' required class='input_area-1'>
              <div><button type='button' id='trainerDone'>Done</button></div> <!-- Updated button ID -->
          </form>
      </div>
  </div>

  <!-- Trainee Modal -->
  <div id='traineeModal' class='modal'>
      <div class='modal-content'>
          <span class='close'>&times;</span>
          <h2>Trainee Gym Details</h2>
          <form id='traineeForm'>
              <label for='gymPreferred'>Select Your Preferred Gym:</label>
              <select id='gymPreferred' name='gymPreferred' required class='input_area-1'>
                  <!-- Populate gyms from the database -->
                  <?php 
                  include 'db_connect.php'; 
                  $sql = "SELECT gym_id, gym_name FROM gym";
                  if ($result = mysqli_query($conn, $sql)) {
                      while ($row = mysqli_fetch_assoc($result)) {
                          echo '<option value="' . htmlspecialchars($row['gym_id']) . '">' . htmlspecialchars($row['gym_name']) . '</option>';
                      }
                  } else {
                      echo '<option value="">Error fetching gyms</option>';
                  }
                  ?>
              </select><br><br>
              <button type='button' id='traineeDone'>Done</button>
          </form>
      </div>
  </div>

</div>

<script src="../static/scripts/sign-up.js"></script> <!-- Ensure this script is linked -->
</body>
</html>
