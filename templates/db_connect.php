<!-- <?php

// Database credentials
// $host = 'localhost';
// $user = 'root'; 
// $password = ''; 
// $db_name = 'fitflex'; 

// // Create a connection
// $conn = new mysqli($host, $user, $password, $db_name);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }


// //return $conn;
?> -->



<?php
$servername = "localhost";
$username = "maxwell.nyimbili"; 
$password = "L3gendary1864"; 
$dbname = "webtech_fall2024_maxwell_nyimbili";
$port = "3341";

// Attempt to connect to the database
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn -> connect_error) 
{
    echo''. $conn -> connect_error;
    die("Connection failed: " . $conn->connect_error);
}

?>