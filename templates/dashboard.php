<?php
session_start();
require 'db_connect.php'; 
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user data (first name, last name, and user role)
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; 
$first_name = $_SESSION['firstName'];
$last_name = $_SESSION['lastName'];

// Fetch user-specific data (weights) from Progress or another relevant table
$sql = "SELECT weight FROM progress WHERE user_id = ? ORDER BY date DESC LIMIT 1"; // Getting most recent weight
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind the user_id parameter
$stmt->bind_param("i", $user_id); // "i" means integer for user_id

// Execute the statement
$stmt->execute();

// Bind result variables
$stmt->bind_result($current_weight);

// Fetch the result
if ($stmt->fetch()) {
    // echo "Current Weight: " . $current_weight . "<br>";  // important for debugging not needed for final result
} else {
    // Default weights if not found
    $current_weight = 'N/A';
}

// Close the statement
$stmt->close();

$gym_name = '';
if ($user_role == 'trainee') {
    $query = "SELECT gym.gym_name 
              FROM gym 
              JOIN usersflex ON usersflex.gym_id = gym.gym_id 
              WHERE usersflex.user_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($gym_name);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
}

if (empty($gym_name)) {
    $gym_name = 'No gym assigned'; // Default message
}

// Handle image upload logic and database insert
$imagePath = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $imageDir = 'uploads/';
    $imageName = uniqid() . '_' . basename($_FILES['file']['name']);
    $imagePath = $imageDir . $imageName;

    $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($imageFileType, $allowedTypes)) {
        die("Sorry, only JPG, JPEG, and PNG files are allowed.");
    }

    if ($_FILES['file']['size'] > 5000000) {
        die("Sorry, your file is too large.");
    }

    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $imagePath)) {
        // Save image path to the database

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare('INSERT INTO user_images (user_id, image_url) VALUES (?, ?)');
        $stmt->bind_param('is', $user_id, $imagePath);

        if (!$stmt->execute()) {
            die("Database error: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } else {
        die("Failed to upload the image.");
    }
}

// Fetch images for a user (for modal display)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']); // Ensure user_id is an integer

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare('SELECT image_url FROM user_images WHERE user_id = ?');
    $stmt->bind_param('i', $userId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $images = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success' => true, 'pictures' => $images]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch images.']);
    }

    // Close the statement
    $stmt->close();
}

// Fetch Starting Weight
$starting_weight = 'N/A';
$sql_starting = "SELECT weight FROM usersflex WHERE user_id = ? ORDER BY join_date ASC LIMIT 1";
$stmt_starting = $conn->prepare($sql_starting);
if ($stmt_starting) {
    $stmt_starting->bind_param("i", $user_id);
    $stmt_starting->execute();
    $stmt_starting->bind_result($starting_weight);
    $stmt_starting->fetch();
    $stmt_starting->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

// Fetch Current Weight
$current_weight = 'N/A';
$sql_current = "SELECT weight FROM progress WHERE user_id = ? ORDER BY date DESC LIMIT 1";
$stmt_current = $conn->prepare($sql_current);
if ($stmt_current) {
    $stmt_current->bind_param("i", $user_id);
    $stmt_current->execute();
    $stmt_current->bind_result($current_weight);
    $stmt_current->fetch();
    $stmt_current->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $date = $_POST['date'] ?? date('Y-m-d'); // Default to current date if not provided

    // Validate and sanitize input
    $exerciseName = trim($_POST['exercise-name'] ?? '');
    $sets = (int) ($_POST['sets'] ?? 0);
    $reps = (int) ($_POST['reps'] ?? 0);
    $duration = (int) ($_POST['duration'] ?? 0);
    $notes = trim($_POST['ingredient-list'] ?? '');
    $currentWeight = isset($_POST['current-weight']) ? (float) $_POST['current-weight'] : null;
    $feedback = trim($_POST['feedback'] ?? '');

    if (!empty($exerciseName)) {
        // Insert into `Workouts` table
        $stmt = $conn->prepare("
            INSERT INTO workouts (user_id, workout_date, exercise_name, sets, reps, duration, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        // Check if the statement was prepared successfully
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("issiiis", $userId, $date, $exerciseName, $sets, $reps, $duration, $notes);

        // Execute the statement
        if ($stmt->execute()) {
            header('Location: dashboard.php');
            // echo "Exercise entry added successfully.";
        } else {
            // echo "Failed to add exercise entry.";
        }

        // Close the statement
        $stmt->close();
    } elseif ($currentWeight !== null) {
        // Insert into `Progress` table (for weight logs)
        $stmt = $conn->prepare("
            INSERT INTO progress (user_id, date, weight, created_at)
            VALUES (?, ?, ?, NOW())
        ");

        // Check if the statement was prepared successfully
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("isd", $userId, $date, $currentWeight);

        // Execute the statement
        if ($stmt->execute()) {
            // echo "Weight updated successfully.";
            header('Location: dashboard.php');
        } else {
            echo "Failed to update weight.";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Invalid input provided.";
    }
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../static/scripts/nav.js" defer></script>
    <script src="../static/scripts/dashboard.js" defer></script>
    <link rel="stylesheet" href="../static/css/nav.css">
    <link rel="stylesheet" href="../static/css/dash.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>FitFlex | Dashboard</title>
</head>
<body>
        <!-- Navigation Section -->
        <header class="header">

            <div> <a href="./index.php"><img class="logo" src="../static/images/FitFlex.png" alt="website logo" width="70px"></a></div>
          
            <!-- Hamburger -->
            <div class="hamburger">
                <div class="menu-btn">
                    <div class="menu-btn_lines"></div>
                </div>
            </div>
            <!-- End of Hamburger -->


            <!-- navigation links -->
            <nav>
                <div class="nav_links">
                    <ul class="menu-items">
                        <li><a href="../index.php" class="menu-item" >Home</a></li>
                        <li><a href="./about.php" class="menu-item">About Us</a></li>

                        <!-- Conditional Login/Logout & Sign-up logic based on user authentication -->
                        <?php
                       
                        if (isset($_SESSION['user_id'])) {
                            // If the user is logged in, show 'Dashboard' and 'Logout' instead of 'Login' and 'Sign-up'
                            echo '<li><a href="./dashboard.php" class="menu-item" style="border-bottom: #122331 solid 2px;">Dashboard</a></li>';
                            echo '<li><a href="./logout.php" class="menu-item">Logout</a></li>';
                        } else {
                            // If the user is not logged in, show 'Login' and 'Sign-up'
                            echo '<li><button class="sign-in-btn"><a href="./login.php" class="menu-item">Login</a></button></li>';
                            echo '<li><button class="sign-in-btn"><a href="./sign-up.php" class="menu-item">Sign-up</a></button></li>';
                        }
                        ?>
                    </ul>
                </div>
            </nav>

        </header>
    <!-- End of Navigation Section -->


    <!-- Main Body Section -->
    <main>
    <?php if ($user_role == "trainer"): ?>
        <!-- Admin Mode -->
        <section>
            <h2>Gym Owner Dashboard</h2>
            <p>Manage your gym, create new activities, view your members, and track gym performance.</p>
        </section>
    <?php else: ?>
        <!-- User Mode -->
        <div class="gym-name"><h1><?= htmlspecialchars($gym_name)?></h1></div>
        <section class="first-section">
            <div class="greetings">
                <h1>Welcome, <?= htmlspecialchars($first_name) . ' ' . htmlspecialchars($last_name); ?></h1>
            </div>

            <div class="inner-body-container">
                <div class="weight-loss">
                    <h1 class="weight-heading">Weight Loss</h1>
                    <div class="flex">
                        <div class="current-weight">
                            <div class="weight-container">
                                <p class="actual-weight"><?= htmlspecialchars($current_weight); ?> <span>lb</span></p>
                            </div>
                            <div><p>Current Weight</p></div>
                        </div>

                        <div class="starting-weight">
                            <div class="weight-container">
                                <p class="actual-weight"><?= htmlspecialchars($starting_weight); ?> <span>lb</span></p>
                            </div>
                            <div><p>Starting Weight</p></div>
                        </div>
                    </div>
                </div>

                <div class="another-container">
                    <div class="flex second">
                        <div class="current-weight">
                            <div class="weight-container"><p class="actual-weight flex-label">Session</p></div>
                            <div><p class="flex-label-1">Cardio Blast</p></div>
                        </div>

                        <div class="starting-weight">
                            <div class="weight-container"><p class="actual-weight flex-label">Trainer</p></div>
                            <div><p class="flex-label-1">Jonathan Brown</p></div>
                        </div>
                    </div>

                    <div class="buttons">
                        <div><button onclick="openWeightModal()" class="btn">Update weight</button></div> 
                        <div><button onclick="openEntryModal()" id="enterExercise" class="btn-1"> Enter exercise done</button></div>
                    </div>
                </div>


                <!-- Button to open the modal -->
                <a href="#" onclick="openImageModal(<?= htmlspecialchars($user_id) ?>)">
                    <div class="circle-container">
                        <h1>View Progress Pictures</h1>
                    </div>
                </a>

            </div>
        </section>

        <section class="second-section">
            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Exercise Name</th>
                            <th>Sets</th>
                            <th>Reps</th>
                            <th>Duration</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="recipe-table-body">
                    <?php
                        // Prepare the SQL statement using mysqli
                        $stmt = $conn->prepare("SELECT workout_date, exercise_name, sets, reps, duration, notes FROM workouts WHERE user_id = ?");
                        if ($stmt === false) {
                            die("Error preparing statement: " . $conn->error);
                        }

                        // Bind the user_id parameter
                        $stmt->bind_param("i", $user_id); // "i" indicates an integer parameter

                        // Execute the statement
                        $stmt->execute();

                        // Get the result set
                        $result = $stmt->get_result();

                        // Fetch all rows as an associative array
                        while ($exercise = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($exercise['workout_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($exercise['exercise_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($exercise['sets']) . "</td>";
                            echo "<td>" . htmlspecialchars($exercise['reps']) . "</td>";
                            echo "<td>" . htmlspecialchars($exercise['duration']) . "</td>";
                            echo "<td>" . htmlspecialchars($exercise['notes']) . "</td>";
                            echo "</tr>";
                        }

                        // Close the statement
                        $stmt->close();
                    ?>

                    </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <!-- Modal Structure -->
    <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeEntryModal" onclick="closeEntryModal()">&times;</span>

                <h2 class="modal-title">Add New Entry</h2>
                <form id="create-recipe-form" enctype="multipart/form-data" method="POST" action="dashboard.php">
                    <!-- User Form Fields -->
                    <div class="section-1">
                        <div><input type="text" name="exercise-name" id="recipe-name" placeholder="Enter exercise name" required></div>
                        <div><input type="date" name="date" id="date" required></div>
                        <div><input type="number" name="sets" id="sets" placeholder="Number of Sets" required></div>
                        <div><input type="number" name="reps" id="reps" placeholder="Number of Reps" required></div>
                    </div>

                    <div class="section-2">
                        <div class="text-area-1">
                            <div><textarea id="ingredient-list" name="ingredient-list" style="resize: none;" placeholder="Notes about the exercise" required></textarea></div>
                        </div>

                        <div class="inner-section">
                            <div><input type="number" name="duration" id="duration" placeholder="Duration (minutes)" required></div>
                            <div><input type="file" name="file" id="file" required></div>
                        </div>
                    </div>

                    <br>
                    <br>
                    <br>
                  
                    <div class="form-btns">
                        <button type="submit" id="submit-btn" class="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Weight Update Modal -->
        <div id="myModal-1" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal"  onclick="closeWeightModal()">&times;</span> <!-- Close button -->

                <h2 class="modal-title">Update Weight</h2>
                <form id="create-recipe-form" enctype="multipart/form-data" method="POST" action="dashboard.php">
                    <!-- User Form Fields -->
                    <div class="section-1">
                        <div><input type="number" name="current-weight" id="current-weight" placeholder="Current weight" required></div>
                        <div><input type="date" name="date" id="date" required></div>
                    </div>

                    <div class="section-2">
                        <div class="text-area-1">
                            <div><textarea id="ingredient-list" name="feedback" style="resize: none;" placeholder="Comment on how workouts are going so far? (optional)"></textarea></div>
                        </div>

                    </div>

                 
                    <div class="form-btns">
                        <button type="submit" id="submit-btn" class="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Round Button - Images Modal -->
        <div id="imageModal" class="modal">
            <div class="modal-content">
                <span onclick="closeImageModal()" class="close">&times;</span>
                <h2>Progress Pictures</h2>
                <div id="imageContainer"></div> <!-- This will hold the images dynamically -->
            </div>
        </div>    
</body>
</html>