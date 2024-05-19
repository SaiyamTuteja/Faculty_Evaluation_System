<?php
session_start();
ini_set('display_errors', 1);

Class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php';

        $this->db = $conn;
    }

    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

function login(){
    extract($_POST);
    $type = array("","users","faculty_list","student_list");
    $type2 = array("","admin","faculty","student");

    // Prepare the query
    $stmt = $this->db->prepare("SELECT id, firstname, lastname FROM {$type[$login]} WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, md5($password));
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        foreach ($row as $key => $value) {
            if($key != 'password' && !is_numeric($key))
                $_SESSION['login_'.$key] = $value;
        }
        $_SESSION['login_type'] = $login;
        $_SESSION['login_view_folder'] = $type2[$login].'/';
        
        // Fetch academic data
        $stmt_academic = $this->db->prepare("SELECT * FROM academic_list WHERE is_default = 1");
        $stmt_academic->execute();
        $result_academic = $stmt_academic->get_result();

        if($result_academic->num_rows > 0){
            $academic_data = $result_academic->fetch_assoc();
            foreach($academic_data as $k => $v){
                if(!is_numeric($k))
                    $_SESSION['academic'][$k] = $v;
            }
        }
        $stmt_academic->close();

        $stmt->close();
        return 1; // Login successful
    }else{
        $stmt->close();
        return 2; // Login failed
    }
}

function login2(){
    extract($_POST);
    
    // Prepare the query
    $stmt = $this->db->prepare("SELECT id, firstname, lastname, middlename FROM students WHERE student_code = ?");
    $stmt->bind_param("s", $student_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        foreach ($row as $key => $value) {
            if(!is_numeric($key))
                $_SESSION['rs_'.$key] = $value;
        }
        $stmt->close();
        return 1; // Login successful
    }else{
        $stmt->close();
        return 3; // Login failed
    }
}

function save_user(){
    extract($_POST);

    // Prepare the query
    if(!empty($password)){
        $password_hash = md5($password);
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $stmt->close();
            return 2; // Email already exists
        }
    }

    $data = "";
    $types = "";

    // Construct the update query
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ?";
            }else{
                $data .= ", $k = ?";
            }
            $types .= "s"; // Assuming all parameters are strings
        }
    }

    if(!empty($password)){
        $data .= ", password = ?";
        $types .= "s";
    }

    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
        $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
        $data .= ", avatar = ?";
        $types .= "s";
    }

    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO users SET $data");
    }else{
        $data .= " WHERE id = ?";
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE users SET $data");
    }

    // Bind parameters
    $params = array(&$email);
    if(!empty($password)){
        $params[] = &$password_hash;
    }
    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $params[] = &$fname;
    }
    if(!empty($id)){
        $params[] = &$id;
    }
    $stmt->bind_param($types, ...$params);

    // Execute query
    $save = $stmt->execute();
    $stmt->close();

    if($save){
        return 1; // Save successful
    }
}

function signup(){
    extract($_POST);

    // Prepare the query
    $stmt_check = $this->db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if($result_check->num_rows > 0){
        $stmt_check->close();
        return 2; // Email already exists
    }

    $data = "";
    $types = "";

    // Construct the update query
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
            if($k =='password'){
                if(empty($v))
                    continue;
                $v = md5($v);
            }
            if(empty($data)){
                $data .= " $k = ?";
            }else{
                $data .= ", $k = ?";
            }
            $types .= "s"; // Assuming all parameters are strings
        }
    }

    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
        $move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
        $data .= ", avatar = ?";
        $types .= "s";
    }

    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO users SET $data");
    }else{
        $data .= " WHERE id = ?";
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE users SET $data");
    }

    // Bind parameters
    $params = array();
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
            if($k =='password'){
                if(empty($v))
                    continue;
                $v = md5($v);
            }
            $params[] = &$$_POST[$k];
        }
    }
    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $params[] = &$fname;
    }
    if(!empty($id)){
        $params[] = &$id;
    }
    $stmt->bind_param($types, ...$params);

    // Execute query
    $save = $stmt->execute();
    $stmt->close();

    if($save){
        if(empty($id))
            $id = $this->db->insert_id;
        foreach ($_POST as $key => $value) {
            if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
                $_SESSION['login_'.$key] = $value;
        }
        $_SESSION['login_id'] = $id;
        if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
            $_SESSION['login_avatar'] = $fname;
        return 1; // Save successful
    }
}
function update_user(){
    extract($_POST);
    $data = "";
    $type = array("","users","faculty_list","student_list");

    // Construct the update query
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            }else{
                $data .= ", $k = ? ";
            }
        }
    }

    // Prepare the query
    $stmt_check = $this->db->prepare("SELECT * FROM {$type[$_SESSION['login_type']]} WHERE email = ? AND id != ?");
    $stmt_check->bind_param("si", $email, $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if($result_check->num_rows > 0){
        $stmt_check->close();
        return 2; // Email already exists
    }

    $types = str_repeat("s", count($_POST) - 4); // Assuming all parameters are strings except id, cpass, table, and password

    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
        $move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
        $data .= ", avatar = ? ";
        $types .= "s";
    }

    if(!empty($password))
        $data .= " ,password = md5(?) ";

    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO {$type[$_SESSION['login_type']]} SET $data");
    }else{
        $data .= " WHERE id = ? ";
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE {$type[$_SESSION['login_type']]} SET $data");
    }

    // Bind parameters
    $params = array_values($_POST);
    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $params[] = $fname;
    }
    if(!empty($id)){
        $params[] = $id;
    }
    $stmt->bind_param($types, ...$params);

    // Execute query
    $save = $stmt->execute();
    $stmt->close();

    if($save){
        foreach ($_POST as $key => $value) {
            if($key != 'password' && !is_numeric($key))
                $_SESSION['login_'.$key] = $value;
        }
        if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
                $_SESSION['login_avatar'] = $fname;
        return 1;
    }
}

function delete_user(){
    extract($_POST);
    
    // Prepare the query
    $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $delete = $stmt->execute();
    $stmt->close();

    if($delete)
        return 1;
}

function save_system_settings(){
    extract($_POST);
    $data = '';
    
    // Construct the update query
    foreach($_POST as $k => $v){
        if(!is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            }else{
                $data .= ", $k = ? ";
            }
        }
    }

    // Check if system_settings table has data
    $chk = $this->db->query("SELECT * FROM system_settings");
    if($chk->num_rows > 0){
        $data_types = str_repeat("s", count($_POST)); // Assuming all parameters are strings
        $data_values = array_values($_POST);
        
        // Prepare the update query
        $stmt = $this->db->prepare("UPDATE system_settings SET $data WHERE id = ?");
        $stmt->bind_param($data_types."i", ...$data_values, $chk->fetch_array()['id']);
        $save = $stmt->execute();
        $stmt->close();
    }else{
        $data_types = str_repeat("s", count($_POST)); // Assuming all parameters are strings
        $data_values = array_values($_POST);
        
        // Prepare the insert query
        $stmt = $this->db->prepare("INSERT INTO system_settings SET $data");
        $stmt->bind_param($data_types, ...$data_values);
        $save = $stmt->execute();
        $stmt->close();
    }

    if($save){
        foreach($_POST as $k => $v){
            if(!is_numeric($k)){
                $_SESSION['system'][$k] = $v;
            }
        }
        if($_FILES['cover']['tmp_name'] != ''){
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
            $move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
            $_SESSION['system']['cover_img'] = $fname;
        }
        return 1;
    }
}
function save_image(){
    extract($_FILES['file']);
    if(!empty($tmp_name)){
        $fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
        $move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
        if($move){
            return 'assets/uploads/'.$fname; // Return the relative path of the uploaded image
        }
    }
}

function save_subject(){
    extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            }else{
                $data .= ", $k = ? ";
            }
        }
    }

    // Prepare the query
    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO subject_list SET $data");
    }else{
        $data .= " WHERE id = ? ";
        $stmt = $this->db->prepare("UPDATE subject_list SET $data");
    }

    // Bind parameters
    $types = str_repeat("s", count($_POST) - 2); // Assuming all parameters are strings except id and user_ids
    $params = array_values($_POST);
    if(!empty($id)){
        $params[] = $id; // Add id as a parameter for update
        $types .= "i"; // Assuming id is an integer
    }
    $stmt->bind_param($types, ...$params);

    // Execute query
    $save = $stmt->execute();
    $stmt->close();

    if($save){
        return 1;
    }else{
        return 0;
    }
}
function delete_subject(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM subject_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();
    
    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function save_class(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            } else {
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }
    
    if(isset($user_ids)){
        $data .= ", user_ids = ?";
        $params[] = implode(',', $user_ids);
        $types .= "s";
    }
    
    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO class_list SET $data");
    } else {
        $data .= " WHERE id = ?";
        $params[] = &$id;
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE class_list SET $data");
    }

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function delete_class(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM class_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function save_academic(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            } else {
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }

    $chk = $this->db->prepare("SELECT * FROM academic_list WHERE $data AND id != ?");
    $chk->bind_param($types . "i", ...$params, $id); // Bind parameters
    $chk->execute();
    $chk->store_result();
    $chk->fetch();

    if($chk->num_rows > 0){
        $chk->close();
        return 2;
    }
    $chk->close();

    $hasDefault = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1")->num_rows;
    if($hasDefault == 0){
        $data .= " , is_default = 1 ";
    }

    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO academic_list SET $data");
    } else {
        $data .= " WHERE id = ?";
        $params[] = &$id;
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE academic_list SET $data");
    }

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function delete_academic(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM academic_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}
function make_default(){
    extract($_POST);
    $update = $this->db->prepare("UPDATE academic_list SET is_default = 0");
    $update->execute();

    $update1 = $this->db->prepare("UPDATE academic_list SET is_default = 1 WHERE id = ?");
    $update1->bind_param("i", $id); // Bind id parameter
    $update1->execute();

    $qry = $this->db->prepare("SELECT * FROM academic_list WHERE id = ?");
    $qry->bind_param("i", $id); // Bind id parameter
    $qry->execute();
    $result = $qry->get_result();
    $row = $result->fetch_assoc();
    $qry->close();

    if($update && $update1){
        foreach($row as $k => $v){
            if(!is_numeric($k))
                $_SESSION['academic'][$k] = $v;
        }
        return 1;
    }
}

function save_criteria(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            } else {
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }

    $chk = $this->db->prepare("SELECT * FROM criteria_list WHERE $data AND id != ?");
    $chk->bind_param($types . "i", ...$params, $id); // Bind parameters
    $chk->execute();
    $chk->store_result();

    if($chk->num_rows > 0){
        $chk->close();
        return 2;
    }
    $chk->close();

    if(empty($id)){
        $lastOrder = $this->db->query("SELECT COALESCE(MAX(ABS(order_by)), 0) AS max_order FROM criteria_list")->fetch_assoc()['max_order'] + 1;
        $data .= ", order_by = ?";
        $params[] = &$lastOrder;
        $types .= "i"; // Assuming order_by is an integer
        $stmt = $this->db->prepare("INSERT INTO criteria_list SET $data");
    } else {
        $stmt = $this->db->prepare("UPDATE criteria_list SET $data WHERE id = ?");
        $params[] = &$id;
        $types .= "i"; // Assuming id is an integer
    }

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function delete_criteria(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM criteria_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function save_criteria_order(){
    extract($_POST);
    $update = $this->db->prepare("UPDATE criteria_list SET order_by = ? WHERE id = ?");
    foreach($criteria_id as $k => $v){
        $update->bind_param("ii", $k, $v); // Bind parameters
        $update->execute();
    }

    if($update->affected_rows > 0){
        $update->close();
        return 1;
    } else {
        $update->close();
        return 0;
    }
}

function save_question(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            } else {
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }

    if(empty($id)){
        $lastOrder = $this->db->prepare("SELECT COALESCE(MAX(ABS(order_by)), 0) AS max_order FROM question_list WHERE academic_id = ?");
        $lastOrder->bind_param("i", $academic_id); // Bind academic_id parameter
        $lastOrder->execute();
        $result = $lastOrder->get_result();
        $lastOrder = $result->fetch_assoc()['max_order'] + 1;
        $data .= ", order_by = ?";
        $params[] = &$lastOrder;
        $types .= "i"; // Assuming order_by is an integer
        $stmt = $this->db->prepare("INSERT INTO question_list SET $data");
    } else {
        $stmt = $this->db->prepare("UPDATE question_list SET $data WHERE id = ?");
        $params[] = &$id;
        $types .= "i"; // Assuming id is an integer
    }

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}
function delete_question(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM question_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function save_question_order(){
    extract($_POST);
    $update = $this->db->prepare("UPDATE question_list SET order_by = ? WHERE id = ?");
    foreach($qid as $k => $v){
        $update->bind_param("ii", $k, $v); // Bind parameters
        $update->execute();
    }

    if($update->affected_rows > 0){
        $update->close();
        return 1;
    } else {
        $update->close();
        return 0;
    }
}

function save_faculty(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            } else {
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }

    if(!empty($password)){
        $data .= ", password = ?";
        $params[] = &$password; // Reference to the value of $password
        $types .= "s"; // Assuming password is a string
    }

    $check_email = $this->db->prepare("SELECT * FROM faculty_list WHERE email = ?" . (!empty($id) ? " AND id != ?" : ""));
    $check_email->bind_param("s", $email); // Bind email parameter
    if(!empty($id)) $check_email->bind_param("i", $id); // Bind id parameter if it exists
    $check_email->execute();
    $check_email->store_result();
    $num_rows_email = $check_email->num_rows;
    $check_email->close();

    $check_school = $this->db->prepare("SELECT * FROM faculty_list WHERE school_id = ?" . (!empty($id) ? " AND id != ?" : ""));
    $check_school->bind_param("i", $school_id); // Bind school_id parameter
    if(!empty($id)) $check_school->bind_param("i", $id); // Bind id parameter if it exists
    $check_school->execute();
    $check_school->store_result();
    $num_rows_school = $check_school->num_rows;
    $check_school->close();

    if($num_rows_email > 0){
        return 2; // Email already exists
    } elseif($num_rows_school > 0){
        return 3; // School ID already exists
    }

    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
        $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
        $data .= ", avatar = ?";
        $params[] = &$fname; // Reference to the value of $fname
        $types .= "s"; // Assuming avatar is a string
    }

    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO faculty_list SET $data");
    } else {
        $data .= " WHERE id = ?";
        $params[] = &$id; // Reference to the value of $id
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE faculty_list SET $data");
    }

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}

function delete_faculty(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM faculty_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}
function save_student(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
            if(empty($data)){
                $data .= " $k = ? ";
            }else{
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }
    if(!empty($password)){
        $data .= ", password = md5(?) ";
        $params[] = &$password; // Reference to the value of $password
        $types .= "s"; // Assuming password is a string
    }
    $check_stmt = $this->db->prepare("SELECT * FROM student_list WHERE email = ?" . (!empty($id) ? " AND id != ?" : ""));
    $check_stmt->bind_param("s", $email); // Bind email parameter
    if(!empty($id)) $check_stmt->bind_param("i", $id); // Bind id parameter if it exists
    $check_stmt->execute();
    $check_stmt->store_result();
    $num_rows = $check_stmt->num_rows;
    $check_stmt->close();

    if($num_rows > 0){
        return 2; // Email already exists
    }

    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
        $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
        $data .= ", avatar = ? ";
        $params[] = &$fname; // Reference to the value of $fname
        $types .= "s"; // Assuming avatar is a string
    }

    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO student_list SET $data");
    }else{
        $data .= " WHERE id = ? ";
        $params[] = &$id; // Reference to the value of $id
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE student_list SET $data");
    }

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    }else{
        $stmt->close();
        return 0;
    }
}

function delete_student(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM student_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    }else{
        $stmt->close();
        return 0;
    }
}

function save_task(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id')) && !is_numeric($k)){
            if($k == 'description'){
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            }
            if(empty($data)){
                $data .= " $k = ? ";
            }else{
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }
    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO task_list SET $data");
    }else{
        $data .= " WHERE id = ? ";
        $params[] = &$id; // Reference to the value of $id
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE task_list SET $data");
    }

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    }else{
        $stmt->close();
        return 0;
    }
}

function delete_task(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM task_list WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $stmt->close();
        return 1;
    }else{
        $stmt->close();
        return 0;
    }
}
<?php
function save_progress(){
    extract($_POST);
    $data = "";
    $params = [];
    $types = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id')) && !is_numeric($k)){
            if($k == 'progress')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k = ? ";
            }else{
                $data .= ", $k = ? ";
            }
            $params[] = &$$_POST[$k]; // Reference to the value of $_POST[$k]
            $types .= "s"; // Assuming all parameters are strings
        }
    }
    if(!isset($is_complete))
        $data .= ", is_complete=0 ";
    if(empty($id)){
        $stmt = $this->db->prepare("INSERT INTO task_progress SET $data");
    }else{
        $data .= " WHERE id = ? ";
        $params[] = &$id; // Reference to the value of $id
        $types .= "i"; // Assuming id is an integer
        $stmt = $this->db->prepare("UPDATE task_progress SET $data");
    }

    // Bind parameters
    $bind_types = $types;
    $stmt->bind_param($bind_types, ...$params);

    // Execute query
    $stmt->execute();

    if($stmt->affected_rows > 0){
        if(!isset($is_complete))
            $this->db->query("UPDATE task_list SET status = 1 WHERE id = $task_id ");
        else
            $this->db->query("UPDATE task_list SET status = 2 WHERE id = $task_id ");
        $stmt->close();
        return 1;
    }else{
        $stmt->close();
        return 0;
    }
}

function delete_progress(){
    extract($_POST);
    $stmt = $this->db->prepare("DELETE FROM task_progress WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind id parameter
    $stmt->execute();
    $stmt->close();
    if($this->db->affected_rows > 0){
        return 1;
    }else{
        return 0;
    }
}

function save_restriction(){
    extract($_POST);
    $filtered = implode(",",array_filter($rid));
    if(!empty($filtered))
        $this->db->query("DELETE FROM restriction_list WHERE id NOT IN ($filtered) AND academic_id = $academic_id");
    else
        $this->db->query("DELETE FROM restriction_list WHERE academic_id = $academic_id");

    foreach($rid as $k => $v){
        $data = " academic_id = ? ";
        $data .= ", faculty_id = ? ";
        $data .= ", class_id = ? ";
        $data .= ", subject_id = ? ";
        $params = [$academic_id, $faculty_id[$k], $class_id[$k], $subject_id[$k]];

        if(empty($v)){
            $stmt = $this->db->prepare("INSERT INTO restriction_list SET $data");
        }else{
            $data .= " WHERE id = ? ";
            $params[] = &$v; // Reference to the value of $v
            $stmt = $this->db->prepare("UPDATE restriction_list SET $data");
        }

        // Bind parameters
        $stmt->bind_param("iiii", ...$params);

        // Execute query
        $stmt->execute();
        $stmt->close();
    }
    return 1;
}

function save_evaluation(){
    extract($_POST);
    $data = " student_id = ?";
    $data .= ", academic_id = ?";
    $data .= ", subject_id = ?";
    $data .= ", class_id = ?";
    $data .= ", restriction_id = ?";
    $data .= ", faculty_id = ?";
    $params = [$_SESSION['login_id'], $academic_id, $subject_id, $class_id, $restriction_id, $faculty_id];
    $stmt = $this->db->prepare("INSERT INTO evaluation_list SET $data");
    $stmt->bind_param("iiiiii", ...$params);
    $stmt->execute();
    $stmt->close();
    $eid = $this->db->insert_id;
    
    foreach($qid as $k => $v){
        $data = " evaluation_id = ?";
        $data .= ", question_id = ?";
        $data .= ", rate = ?";
        $params = [$eid, $v, $rate[$v]];
        $stmt = $this->db->prepare("INSERT INTO evaluation_answers SET $data");
        $stmt->bind_param("iii", ...$params);
        $stmt->execute();
        $stmt->close();
    }
    return 1;
}

function get_class(){
    extract($_POST);
    $data = array();
    $stmt = $this->db->prepare("SELECT c.id, CONCAT(c.curriculum, ' ', c.level, ' - ', c.section) AS class, s.id AS sid, CONCAT(s.code, ' - ', s.subject) AS subj FROM restriction_list r INNER JOIN class_list c ON c.id = r.class_id INNER JOIN subject_list s ON s.id = r.subject_id WHERE r.faculty_id = ? AND academic_id = ?");
    $stmt->bind_param("ii", $fid, $_SESSION['academic']['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    $stmt->close();
    return json_encode($data);
}

function get_report(){
    extract($_POST);
    $data = array();
    $stmt = $this->db->prepare("SELECT * FROM evaluation_answers WHERE evaluation_id IN (SELECT evaluation_id FROM evaluation_list WHERE academic_id = ? AND faculty_id = ? AND subject_id = ? AND class_id = ?)");
    $stmt->bind_param("iiii", $_SESSION['academic']['id'], $faculty_id, $subject_id, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $answered_stmt = $this->db->prepare("SELECT * FROM evaluation_list WHERE academic_id = ? AND faculty_id = ? AND subject_id = ? AND class_id = ?");
    $answered_stmt->bind_param("iiii", $_SESSION['academic']['id'], $faculty_id, $subject_id, $class_id);
    $answered_stmt->execute();
    $answered = $answered_stmt->get_result();
    $answered_rows = $answered->num_rows;

    $rate = array();
    while($row = $result->fetch_assoc()){
        if(!isset($rate[$row['question_id']][$row['rate']]))
            $rate[$row['question_id']][$row['rate']] = 0;
        $rate[$row['question_id']][$row['rate']] += 1;
    }
    
    $r = array();
    foreach($rate as $qk => $qv){
        foreach($qv as $rk => $rv){
            $r[$qk][$rk] = ($rate[$qk][$rk] / $answered_rows) * 100;
        }
    }

    $data['tse'] = $answered_rows;
    $data['data'] = $r;
    $stmt->close();
    $answered_stmt->close();
    return json_encode($data);
}
?>
