<?php
    $host = 'localhost:3306';
    $dbname = 'se07101';
    $username = 'root';
    $password = '';

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    else {  
        // echo "Connected successfully";
    }
?>
