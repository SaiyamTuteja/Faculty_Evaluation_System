<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * FROM student_list where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
include 'new_student.php';
?>