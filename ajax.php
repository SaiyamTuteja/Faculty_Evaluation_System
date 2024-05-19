<?php
ob_start();
date_default_timezone_set("Asia/Manila");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();
if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}

if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'update_user'){
	$save = $crud->update_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'save_subject'){
	$save = $crud->save_subject();
	if($save)
		echo $save;
}
if($action == 'delete_subject'){
	$save = $crud->delete_subject();
	if($save)
		echo $save;
}
if($action == 'save_class'){
	$save = $crud->save_class();
	if($save)
		echo $save;
}
if($action == 'delete_class'){
	$save = $crud->delete_class();
	if($save)
		echo $save;
}
if($action == 'save_academic'){
	$save = $crud->save_academic();
	if($save)
		echo $save;
}
if($action == 'delete_academic'){
	$save = $crud->delete_academic();
	if($save)
		echo $save;
}
if($action == 'make_default'){
	$save = $crud->make_default();
	if($save)
		echo $save;
}
if($action == 'save_criteria'){
	$save = $crud->save_criteria();
	if($save)
		echo $save;
}
if($action == 'delete_criteria'){
	$save = $crud->delete_criteria();
	if($save)
		echo $save;
}
if($action == 'save_question'){
	$save = $crud->save_question();
	if($save)
		echo $save;
}
if($action == 'delete_question'){
	$save = $crud->delete_question();
	if($save)
		echo $save;
}

if($action == 'save_criteria_question'){
	$save = $crud->save_criteria_question();
	if($save)
		echo $save;
}
if($action == 'save_criteria_order'){
	$save = $crud->save_criteria_order();
	if($save)
		echo $save;
}

if($action == 'save_question_order'){
	$save = $crud->save_question_order();
	if($save)
		echo $save;
}
if($action == 'save_faculty'){
	$save = $crud->save_faculty();
	if($save)
		echo $save;
}
if($action == 'delete_faculty'){
	$save = $crud->delete_faculty();
	if($save)
		echo $save;
}
if($action == 'save_student'){
	$save = $crud->save_student();
	if($save)
		echo $save;
}
if($action == 'delete_student'){
	$save = $crud->delete_student();
	if($save)
		echo $save;
}
if($action == 'save_restriction'){
	$save = $crud->save_restriction();
	if($save)
		echo $save;
}
if($action == 'save_evaluation'){
	$save = $crud->save_evaluation();
	if($save)
		echo $save;
}

if($action == 'get_class'){
	$get = $crud->get_class();
	if($get)
		echo $get;
}
if($action == 'get_report'){
	$get = $crud->get_report();
	if($get)
		echo $get;
}
ob_end_flush();
?>
