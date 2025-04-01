<?php
session_start();

session_unset(); 
session_destroy(); 

header("Location: ../home_store/home.php");
exit();
?>