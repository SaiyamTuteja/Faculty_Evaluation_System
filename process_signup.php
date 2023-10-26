<?php
session_start();
include('./db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user data from the form
    $CUID = $_POST['CUID'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security
    $user_type = $_POST['user_type'];

    // Insert the user data into the student_list table
    $insert_query = "INSERT INTO student_list (school_id, firstname, lastname, email, password, class_id) VALUES ('$CUID', 'First Name', 'Last Name', 'Email', '$password', 'Class ID')";
    if ($conn->query($insert_query) === TRUE) {
        // Fetch the user data from the student_list table
        $select_query = "SELECT * FROM student_list WHERE id = " . $conn->insert_id;
        $result = $conn->query($select_query);
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Insert the user data into the login table
            $insert_login_query = "INSERT INTO login (school_id, firstname, lastname, email, password, class_id, avatar, date_created) VALUES (
                '" . $row['school_id'] . "',
                '" . $row['firstname'] . "',
                '" . $row['lastname'] . "',
                '" . $row['email'] . "',
                '" . $row['password'] . "',
                '" . $row['class_id'] . "',
                '" . $row['avatar'] . "',
                '" . $row['date_created'] . "'
            )";
            if ($conn->query($insert_login_query) === TRUE) {
                // Registration is complete. You can display a success message and redirect the user.
                echo "Registration successful!";
                header("location: login.php"); // Redirect to the login page
            } else {
                // Handle any database insert errors for the login table
                echo "Error: " . $insert_login_query . "<br>" . $conn->error;
            }
        }
    } else {
        // Handle any database insert errors for the student_list table
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }
}

// Close the database connection if needed
$conn->close();
?>
