<?php 
include('db_connect.php');
session_start();

if(isset($_GET['id'])){
    $type = array("","users","faculty_list","student_list");
    $user = $conn->query("SELECT * FROM {$type[$_SESSION['login_type']]} where id =".$_GET['id']);
    foreach($user->fetch_array() as $k => $v){
        $meta[$k] = $v;
    }
}

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

    // Check if email already exists for another user
    $check_email = $conn->query("SELECT * FROM users WHERE email = '$email' AND id != $id");
    if($check_email->num_rows > 0){
        echo 2; // Email already exists
        exit;
    }

    // Update user details
    $update_query = "UPDATE users SET firstname='$firstname', lastname='$lastname', email='$email'";
    if(!empty($password)){
        $update_query .= ", password='$password'";
    }
    $update_query .= " WHERE id=$id";

    if($conn->query($update_query)){
        echo 1; // Success
    }else{
        echo 0; // Error
    }
}
?>
