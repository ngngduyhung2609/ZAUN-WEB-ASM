<?php
session_start();

try{
    include '../db.php';
}
catch(Exception $e){
    echo ''.$e->getMessage().'';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($user['role'] == 'admin'){
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                header("Location: ../home_store/home.php");
                echo "Login success!";
            }
            else{
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];

                header("Location: ../home_store/home.php");
                echo "Login success!";
        } 
    }

        else {
            echo "Sai mật khẩu!";
        }

    } else {
        echo "Tài khoản không tồn tại!";
    }
}
?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Whale Cum to My World</title>
    <link rel="stylesheet" href="../fe/login.css" />
  </head>
  <body>
  <div class="wrapper fadeInDown">
        <div id="formContent">
<!-- 
            <a href="login.php">
                <h2 class="inactive underlineHover">Sign In </h2>
            </a>
            <h2 class="active"> Sign Up </h2> -->
        
            <!-- <div class="fadeIn first">
                <img src="/img/logo.png" alt="User Icon" style="width: 80px;"/>
            </div> -->
            <div class="title">Login</div>
            <form method="POST" action="" class="flip-card__form">
                <input type="text" id="username" class="input-card fadeIn second" name="username" placeholder="User name" required>
                <input type="password" id="password" class="input-card fadeIn third" name="password" placeholder="Password" required>
                <button type="submit" class="resgister-btn btn btn-primary ">Let's Go!</button>
                <!-- <button type="submit" class="resgister-btn btn btn-primary ">New Accout</button> -->
            </form>
      
            <div id="formFooter">
                <a class="underlineHover" href="#">Forgot Password?</a>
                <a href="register.php" class="create-accout">New Accout</a>
            </div>
        </div>
    </div>
  </body>
</html>
