<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./static/css/index.css">
    <script src="./static/scripts/nav.js" defer></script>
    <link rel="stylesheet" href="./static/css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>FitFlex | Home</title>
</head>
<body>
    
    <!-- Navigation Section -->
    <header class="header">
        <!-- logo -->
        <div> <a href="./index.php"><img class="logo" src="./static/images/FitFlex.png" alt="website logo" width="70px"></a></div>
        <!-- End of logo -->

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
                    <li><a href="./index.php" class="menu-item" style="border-bottom: #122331 solid 2px;">Home</a></li>
                    <li><a href="./templates/about.php" class="menu-item">About Us</a></li>

                    <!-- Conditional Login/Logout & Sign-up logic based on user authentication -->
                    <?php
                    session_start();
                    if (isset($_SESSION['user_id'])) {
                        // If the user is logged in, show 'Dashboard' and 'Logout' instead of 'Login' and 'Sign-up'
                        echo '<li><a href="./templates/admin.php" class="menu-item">Admin Dashboard</a></li>';
                        echo '<li><a href="./templates/logout.php" class="menu-item">Logout</a></li>';
                    } else {
                        // If the user is not logged in, show 'Login' and 'Sign-up'
                        echo '<li><button class="sign-in-btn"><a href="./templates/login.php" class="menu-item">Login</a></button></li>';
                        echo '<li><button class="sign-in-btn"><a href="./templates/sign-up.php" class="menu-item">Sign-up</a></button></li>';
                    }
                    ?>
                </ul>
            </div>
        </nav>
    </header>
    <!-- End of Navigation Section -->

    <!-- Body Section -->
     <div class="container">
        <section class="section-1">
            <div class="text-1">
                <h1>YOUR PATH TO STRENGTH & WELLNESS</h1>
            </div>
            <div class="button">
                
                <a href="./templates/login.php" styles="text-decoration: none;"><input type="button" value="Be a member" class="btn-1"></a>
                <a href="./templates/about.php"><input type="button" value="Learn More" class="btn-2"></a>
                
            </div>
        </section>

        <section class="section-2">
            <div class="left-side">
                <div class="image-1"></div>
                <div>
                    <p>Start your fitness journey with FitFlex. Join our community to access exclusive features and resources designed to help you achieve your health and wellness goals.</p>
                </div>
            </div>
            <div class="right-side">
                <div class="image-2"></div>
            </div>
        </section>
     </div>
    
    <!-- End of Body Section -->

    <!-- Arrow Section -->
    <div class="wrapper">
        <a href="#service"> 
            <div class="arrow">
            <div class="line line1"></div>
            <div class="line line2"></div>
            </div>
        </a>
    </div>



    <div class="container-1">
        <div class="why"><h1>Why Join Us</h1></div>
        
        <div class="join-us">

            <div class="card">
                <!-- first one -->
                <i class="fa-regular fa-calendar-check"></i>
                <h3>Efficient Class and Program Management</h3>
                <p>Our platform streamlines scheduling, reducing errors and saving time with seamless booking management for gym owners</p>
            </div>
    
    
            <div class="card">
                <!-- customizable -->
                <i class="fa-solid fa-gear"></i>
                <h3>Customizable Experience for Your Gym</h3>
                <p>Our platform lets gyms personalize experiences, customize programs, and maintain their unique identity to keep members engaged</p>
            </div>
    
    
            <div class="card">
                <!-- Boost Engagemnet -->
                <i class="fa-solid fa-users"></i>
                <h3>Boost Member Engagement</h3>
                <p>Members can easily book classes and stay updated on programs, leading to higher engagement, better retention, and a stronger gym community.</p>
            </div>
    
    
            <div class="card">
                <i class="fa-solid fa-chart-line"></i>
                <h3>Data-Driven Insights</h3>
                <p>Gain real-time insights into gym performance with data tracking, enabling informed decisions to optimize operations and drive business growth.</p>
            </div>
        </div>
    
    </div>

</body>
</html>
