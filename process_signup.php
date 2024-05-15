<?php
session_start();
include('./db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['user_type']) && $_POST['user_type'] === 'student'){
    $CUID = $_POST['CUID'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security

    // Insert the user data into the student_list table
    $insert_query = "INSERT INTO student_list (school_id, firstname, lastname, email, password) VALUES ('$CUID', '$firstName', '$lastName', '$email', '$password')";
    if ($conn->query($insert_query) === TRUE) {
        echo "successful";
    } else {
        // Handle any database insert errors for the student_list table
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }
}
if(isset($_POST['user_type']) && $_POST['user_type'] === 'faculty'){
    $CUID = $_POST['CUID'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security

    // Insert the user data into the student_list table
    $insert_query = "INSERT INTO faculty_list (school_id, firstname, lastname, email, password) VALUES ('$CUID', '$firstName', '$lastName', '$email', '$password')";
    if ($conn->query($insert_query) === TRUE) {
        echo "successful";
    } else {
        // Handle any database insert errors for the student_list table
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }
}
}

// Close the database connection if needed
$conn->close();
?>
