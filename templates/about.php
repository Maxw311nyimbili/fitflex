<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../static/scripts/nav.js" defer></script>
    <link rel="stylesheet" href="../static/css/nav.css">
    <link rel="stylesheet" href="../static/css/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>FitFlex | About Us</title>
</head>
<body>

    <!-- Navigation Section -->
    
    <header class="header">
        <!-- logo -->
        <div> <a href="./index.html"><img class="logo" src="../static/images/FitFlex.png" alt="website logo" width="70px"></a></div>
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
                        <li><a href="../index.php" class="menu-item" >Home</a></li>
                        <li><a href="./about.php" class="menu-item" style="border-bottom: #122331 solid 2px;">About Us</a></li>

                        <!-- Conditional Login/Logout & Sign-up logic based on user authentication -->
                        <?php
                        session_start();
                        if (isset($_SESSION['user_id'])) {
                            // If the user is logged in, show 'Dashboard' and 'Logout' instead of 'Login' and 'Sign-up'
                            echo '<li><a href="./admin.php" class="menu-item">Admin Dashboard</a></li>';
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


    <!-- Banner Section -->
     <div class="container">
        <div class="left-side">
            <div class="image-1"></div>

            <div class="paragrapg-container">
                <div class=""><h2>Our Mission</h2></div>
                <div><p>
                    We empower gym owners with tools to streamline their operations and provide gym-goers with a comprehensive solution to monitor their progress and well-being. </p></div>
            </div>
        </div>


        <div class="right-side">
            <div class="heading"><h1>About us</h1></div>
            <div class="image-2"></div>
        </div>

     </div>
     <!-- End of Banner Section -->

     <!-- Arrow -->
     <div class="wrapper">
        <a href="#service"> 
          <div class="arrow">
            <div class="line line1"></div>
            <div class="line line2"></div>
          </div>
        </a>
      </div>
      

     
    <!-- Body Section -->
     <div class="team-container">
        <div class="meet-heading"><h1>Meet The Team</h1></div>
        <div class="team-members">

            <div class="member-1">
                <p class="member-names"><strong>Kirk Kudoto</strong>  <br> Backend Developer</p>
            </div>

            <div class="member-2">
                <p class="member-names"> <strong>Rahinatu Lawal</strong><br> Operations Manager</p>
            </div>

            <div class="member-3">
                <p class="member-names"> <strong>Nonzuzo Sylvia</strong> <br> Backend Developer</p>
            </div>

            <div class="member-4">
                <p class="member-names"><strong>Maxwell Nyimbili</strong>  <br> Frontend Developer</p>
            </div>

        </div>

     </div>

    <!-- End of Body Section -->
    
</body>
</html>