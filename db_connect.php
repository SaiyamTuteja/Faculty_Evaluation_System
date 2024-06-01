<!-- $conn= new mysqli('localhost','root','','evaluation_db')or die("Could not connect to mysql".mysqli_error($con));  -->


<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "evaluation_db";
 
try{
    $conn = new MySQLi($host, $username, $password, $dbname);
} catch (Exception $e){
    die($e->getMessage());
}
