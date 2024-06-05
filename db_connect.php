
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
