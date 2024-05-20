<?php
session_start();
include('./db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $CUID = $_POST['CUID'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security

    // Determine the table based on user type
    $table = $_POST['user_type'] === 'student' ? 'student_list' : 'faculty_list';

    // Check if the CUID already exists in the table
    $check_query = "SELECT * FROM $table WHERE school_id = '$CUID'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // CUID already exists
        $response = ['status' => 'error', 'message' => 'CUID already registered'];
    } else {
        // CUID does not exist, proceed with insertion
        $insert_query = "INSERT INTO $table (school_id, firstname, lastname, email, password) VALUES ('$CUID', '$firstName', '$lastName', '$email', '$password')";
        if ($conn->query($insert_query) === TRUE) {
            $response = ['status' => 'success', 'message' => 'Registration successful'];
        } else {
            // Handle any database insert errors
            $response = ['status' => 'error', 'message' => 'Error: ' . $insert_query . '<br>' . $conn->error];
        }
    }

    // Send the response to the AJAX call
    echo json_encode($response);
}
