<?php
$conn = new mysqli("localhost:3306", "root", "", "se07101");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

else{
    // echo"Connect Success";
}


$admin_username = "admin";
$admin_email = "admin@gmail.com";

$sql = "INSERT INTO users $admin_username, $admin_password,$full_name, $admin_email, $admin_phone, $admin_address, 'admin'";
$conn ->query($sql);
if ($cmd->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $cmd->error;
}

$cmd->close();
$conn->close();
?>