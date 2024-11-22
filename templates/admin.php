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
            header('Location: admin.php');
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
            header('Location: admin.php');
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitFlex | Dashboard</title>
    <script src="../static/scripts/nav.js" defer></script>
    <script src="../static/scripts/dashboard.js" defer></script>
    <script src="../static/scripts/users.js" defer></script>
    <link rel="stylesheet" href="../static/css/nav.css">
    <link rel="stylesheet" href="../static/css/dash.css">
    <style>
        .user-info{
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .tabs {
            display: flex;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .tab {
            padding: 10px 20px;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-bottom: none;
            margin-right: 5px;
            transition: background-color 0.3s;
        }
        .tab:hover {
            background-color: #ddd;
        }
        .tab.active {
            background-color: white;
            border-bottom: 1px solid white;
        }
        .tab-content {
            display: none;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: white;
        }
        .tab-content.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .error-message {
            color: red;
            text-align: center;
        }
        .login-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
        }
        .user-info {
            margin-bottom: 2rem;
            font-weight: bold;
            margin-top: 2rem;
            width: 100%;
            height: 20%;
            font-size: 2rem;
            background-color:gray;
            color: white;
            display: flex;
            justify-content: center; /* Center text horizontally */
            align-items: center;    /* Center text vertically */
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
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
                    echo '<li><a href="./admin.php" class="menu-item" style="border-bottom: #366d81d5 solid 2px;">Admin Dashboard</a></li>';
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



<!-- START -->
<?php if ($user_role == "trainee"): ?>

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
                                <p class="actual-weight"><?= htmlspecialchars($current_weight); ?><span>Kg</span></p>
                            </div>
                            <div><p>Current Weight</p></div>
                        </div>

                        <div class="starting-weight">
                            <div class="weight-container">
                                <p class="actual-weight"><?= htmlspecialchars($starting_weight); ?>Kg<span>lb</span></p>
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

<?php else: ?>
<?php
require 'db_connect.php'; 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<div class='error-message'><p>Please log in to access the system.</p>";
    echo "<a href='login.php' class='login-button'>Login</a></div>";
    exit;
}
echo "<div class='user-info'>Welcome, " .
    htmlspecialchars($_SESSION['firstName'] . " " . $_SESSION['lastName']) .
    " | Role: " . htmlspecialchars(str_replace("_", " ", ucwords($_SESSION['role']))) .
    "</div>";
?>

    <div class="tabs">
        <div class="tab active" onclick="showTab(0)">Profile</div>
        <div class="tab" onclick="showTab(1)">Gyms</div>
        <div class="tab" onclick="showTab(2)">Messages</div>
        <?php
            if ($_SESSION['role'] == 'super_admin') {
                echo "<div class='tab' onclick='showTab(3)'>All Users</div>";
            }
        ?>
    </div>
    <script>
        function showTab(index) {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            tabs[index].classList.add('active');
            contents[index].classList.add('active');
        }
    </script>

    <div class="tab-content active">
        <h2 style="text-align: center;">User Profile</h2>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        try {
            // Fetch user details based on session
            $userID = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT * FROM usersflex WHERE user_id = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "<table style='width: 70%'>";
                echo "<tr><th>First Name</th><td>" . htmlspecialchars($user['firstName']) . "</td></tr>";
                echo "<tr><th>Last Name</th><td>" . htmlspecialchars($user['lastName']) . "</td></tr>";
                echo "<tr><th>Email</th><td>" . htmlspecialchars($user['email']) . "</td></tr>";
                echo "<tr><th>Role</th><td>" . htmlspecialchars($user['role']) . "</td></tr>";
                echo "</table>";
            } else {
                echo "<p>User details not found.</p>";
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "<p class='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <div class="tab-content">
        <h2>Gyms</h2>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        try {
            if ($_SESSION['role'] == 'super_admin') {
                // Show all gyms for super admin
                $sql = "SELECT * FROM gym";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table style='width: 70%;'>";
                    echo "<tr><th>Gym ID</th><th>Name</th><th>Location</th><th>Services</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['gym_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['gym_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['gym_location']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['services_offered']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No gyms found.</p>";
                }
            } elseif ($_SESSION['role'] == 'trainer') {
                // Show user's gym
                $stmt = $conn->prepare("SELECT g.* FROM gym g 
                                        JOIN usersflex u ON g.gym_id = u.gym_id 
                                        WHERE u.user_id = ?");
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $gym = $result->fetch_assoc();
                    echo "<table style='width: 70%;'>";
                    echo "<tr><th>Gym Name</th><td>" . htmlspecialchars($gym['gym_name']) . "</td></tr>";
                    echo "<tr><th>Location</th><td>" . htmlspecialchars($gym['gym_location']) . "</td></tr>";
                    echo "<tr><th>Services</th><td>" . htmlspecialchars($gym['services_offered']) . "</td></tr>";
                    echo "</table>";
                } else {
                    echo "<p>No gym assigned.</p>";
                }
                $stmt->close();
            } else {
                echo "<p>You do not have permission to view gym details.</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <div class="tab-content">
        <h2>Messages</h2>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        try {
            // Message retrieval based on user role
            if ($_SESSION['role'] == 'super_admin') {
                $sql = "SELECT * FROM messages ORDER BY timestamp DESC LIMIT 50";
                $result = $conn->query($sql);
            } elseif ($_SESSION['role'] == 'trainee') {
                $stmt = $conn->prepare("SELECT * FROM messages 
                                        WHERE sender_id = ? OR receiver_id = ? 
                                        ORDER BY timestamp DESC LIMIT 50");
                $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            else {
                echo "<p>You do not have permission to view messages.</p>";
                $result = false;
            }

            if ($result && $result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Sender</th><th>Receiver</th><th>Message</th><th>Timestamp</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['sender_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['receiver_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['message_text']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No messages found.</p>";
            }

        } catch (Exception $e) {
            echo "<p class='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
    <div class="tab-content">
        <h2>All Users</h2>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        echo "<button id='addUserBtn' class='btn-1' onclick='openUserModal()'>Add User</button>";

        try{

            if ($_SESSION['role'] == 'super_admin'){
                $sql = "SELECT user_id, firstName, lastName, email, height, weight, role from usersflex";
                $result = $conn->query($sql);

                if ($result->num_rows > 0){
                    echo "<table style='width: 70%;'>";
                    echo "<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Height</th><th>Weight</th><th>Role</th><th>Actions</th></tr>";

                    while ($row = $result->fetch_assoc()){
                        echo "<tr>";
                        echo "<td>" . $row['user_id'] . "</td>";
                        echo "<td>"  . $row['firstName'] . "</td>";
                        echo "<td>" . $row['lastName'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['height'] . "</td>";
                        echo "<td>" . $row['weight'] . "</td>";
                        echo "<td>" . $row['role'] . "</td>";
                        echo "<td>
                                <button onclick='editUser(" . htmlspecialchars($row['user_id']) . ")'>Edit</button>
                                <button onclick='deleteUser(" . htmlspecialchars($row['user_id']) . ")'>Delete</button>
                            </td>";
                        echo "</tr>";
                    }
                    echo "</table>";

                    
            

                  
                }
                else{
                    echo "<h3 style='text-align: center;'>You have no access</h3>";
                }
            }
        } catch (Exception $e){
            echo "<p class='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
<?php endif; ?>




<!-- MODALS -->
         <!-- Add User Modal -->
         <div id="addUserModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeUserModal()">&times;</span>
                <h3>Add New User</h3>
                <form id="addUserForm" onsubmit="addUser(event)">
                    <label for="newFirstName">First Name:</label>
                    <div><input type="text" id="newFirstName" name="newFirstName" required></div>
                    

                    <label for="newLastName">Last Name:</label>
                    <div><input type="text" id="newLastName" name="newLastName" required></div>
                    

                    <label for="newEmail">Email:</label>
                    <div><input type="email" id="newEmail" name="newEmail" required></div>
                    

                    <label for="newPassword">Password:</label>
                    <div><input type="text" id="newPassword" name="newPassword" value="fitflex1234" readonly></div>

                    <label for="newHeight">Height (cm):</label>
                    <input type="number" id="newHeight" name="height" required min="0" class="input_area">

                    <label for="newWeight">Weight (kg):</label>
                    <input type="number" id="newWeight" name="weight" required min="0" class="input_area">

                    

                    <label for="newRole">Role:</label>
                    <select id="newRole" name="role" required>
                        <option value="super_admin">Admin</option>
                        <option value="trainer">Trainer</option>
                        <option value="trainee"> Regular User</option>
                    </select>
                    <br>
                    <br>

                    <button type="submit" class="submit-btn">Add User</button>
                </form>
                <p id="addErrorMessage" style="color: red; display: none;">Please fill in all required fields with valid information.</p>
            </div>
        </div>


        <!-- User Modal -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3>User Details</h3>
                <p id="modalUserDetails">Loading...</p>
            </div>
        </div>


        <!-- Edit User Modal -->
        <div id="editUserModal" style="display:none;" class="modal  ">
          <div class="modal-content">
          <span class="close" onclick="closeEditModal()">&times;</span>
            <form id="editUserForm" method="POST">
                    <label for="editUserId">User ID</label>
                    <div><input type="text" id="editUserId" name="id" readonly></div>
                    

                    <label for="editUsername">Name</label>
                    <div><input type="text" id="editUsername" name="name"></div>
                    

                    <label for="editEmail">Email</label>
                    <div><input type="email" id="editEmail" name="email"></div> 

                    <label for="editEmail">role</label>
                    <!-- <div><input type="text" name="role" id="newRole-1"></div> -->
                    <select id="newRole-1" name="role" required>
                        <option value="super_admin">Admin</option>
                        <option value="trainer">Trainer</option>
                        <option value="trainee"> Regular User</option>
                    </select>
                    <br>
                    

                    <button type="button" onclick="updateUser()">Update</button>
                </form>
          </div>
        </div>


    <!-- Regular User Modal -->
      <!-- Modal Structure -->
    <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeEntryModal" onclick="closeEntryModal()">&times;</span>

                <h2 class="modal-title">Add New Entry</h2>
                <form id="create-recipe-form" enctype="multipart/form-data" method="POST" action="admin.php">
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
                        <button type="submit" id="btn-1" class="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Weight Update Modal -->
        <div id="myModal-1" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal"  onclick="closeWeightModal()">&times;</span> <!-- Close button -->

                <h2 class="modal-title">Update Weight</h2>
                <form id="create-recipe-form" enctype="multipart/form-data" method="POST" action="admin.php">
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