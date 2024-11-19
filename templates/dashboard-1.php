<?php
session_start();
include 'db_connect.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Check the user type and get user name from session
$user_type = $_SESSION['user_type'];
$user_name = $_SESSION['username']; // Fetch user name from session
$user_id = $_SESSION['user_id']; // Fetch the user ID from session

$gym_name = '';
if ($user_type == 'trainee') {
    $query = "SELECT gyms.gym_name 
              FROM gyms 
              JOIN users ON users.gym_id = gyms.gym_id 
              WHERE users.user_id = ?";
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



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../static/scripts/nav.js" defer></script>
    <link rel="stylesheet" href="../static/css/nav.css">
    <link rel="stylesheet" href="../static/css/dashboard.css">
    <title>Dashboard</title>

    <script>
        // Function to load content based on button click
        function loadContent(contentType) {
            const contentDiv = document.querySelector('.content');

            // Clear existing content
            contentDiv.innerHTML = '<p>Loading...</p>'; // Show loading text

            // Define content based on the button clicked
            let content;
            if (contentType === 'progress') {
                content = `<h2>Your Progress</h2>
                           <p>Here you can view your fitness progress...</p>`;
            } else if (contentType === 'activities') {
                content = `<h2>Gym Activities</h2>
                           <p>Here are the activities available at your gym...</p>`;
            } else if (contentType === 'book') {
                content = `<h2>Book a Session</h2>
                           <p>Choose a time and date to book your session...</p>`;
            }

            // Insert new content after a brief delay to simulate loading
            setTimeout(() => {
                contentDiv.innerHTML = content;
            }, 500); // Simulate loading time
        }

        // Load default content on page load
        window.onload = function() {
            loadContent('progress'); // Set default view to progress
        }
    </script>

</head>
<body>
    <!-- Navigation Section -->
    <header class="header">
        <div> <a href="./index.php"><img class="logo" src="../static/images/FitFlex.png" alt="website logo" width="70px"></a></div>
        <div class="hamburger">
            <div class="menu-btn">
                <div class="menu-btn_lines"></div>
            </div>
        </div>
        <nav>
            <div class="nav_links">
                <ul class="menu-items">
                    <li><a href="./index.php" class="menu-item" >Home</a></li>
                    <?php
                    // Conditional Login/Logout & Sign-up logic based on user authentication
                    if (isset($_SESSION['user_id'])) {
                        echo '<li><a href="./dashboard.php" class="menu-item" style="border-bottom: #122331 solid 2px;">Dashboard</a></li>';
                        echo '<li><a href="./logout.php" class="menu-item">Logout</a></li>';
                    } else {
                        echo '<li><button class="sign-in-btn"><a href="./login.php" class="menu-item">Login</a></button></li>';
                        echo '<li><button class="sign-in-btn"><a href="./sign-up.php" class="menu-item">Sign-up</a></button></li>';
                    }
                    ?>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <?php if ($user_type == 'gym_owner') : ?>
            <section>
                <h2>Gym Owner Dashboard</h2>
                <p>Manage your gym, create new activities, view your members, and track gym performance.</p>
            </section>
        <?php else : ?>
            <!-- Banner section -->
            <div class="gym-signed-to">
                <h1><?php echo htmlspecialchars($gym_name); ?></h1>
            </div>

            <h1 class="person-name">Hello, <?php echo htmlspecialchars($user_name); ?></h1>

            <section>
                <div class="banner">
                    <div class="banner-words">
                        <div><h1>Hello, <?php echo htmlspecialchars($user_name); ?></h1></div>
                        <div class="parag-container"><p>Track your fitness progress, explore gym activities, and book your sessions.</p></div>
                    </div>
                    
                </div>
            </section>


            <div class="section-2">
                <!-- Buttons -->
                <div class="buttons">
                    <div class="btn-1"><input type="button" value="Progress" onclick="loadContent('progress')"></div>
                    <div class="btn-1"><input type="button" value="Gym Activities" onclick="loadContent('activities')"></div>
                    <div class="btn-1"><input type="button" value="Book Session" onclick="loadContent('book')"></div>
                </div>

                <!-- content -->
                <div class="content">
                    <!-- Default content will be loaded here -->
                </div>
            </div>

        <?php endif; ?>
    </main>

</body>
</html>
