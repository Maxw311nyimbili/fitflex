
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
                session_start();

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

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include "db_connect.php";

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
                    <div><input type="text" id="newPassword" name="newPassword" value="kitchen1234" readonly></div>
                    

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
                    <div><input type="text" name="role" id="newRole"></div>
                    <br>
                    

                    <button type="button" onclick="updateUser()">Update</button>
                </form>
          </div>
        </div>

</body>
</html>