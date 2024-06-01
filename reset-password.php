<?php 
require_once('auth.php');
require_once('db-connect.php');
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    extract($_POST);
    if($new_password !== $confirm_password){
        $error = "Password does not match.";
    }else{
        $uid = $_GET['uid'] ?? "";
        $stmt = $conn->prepare("SELECT * FROM `users` where md5(`id`) = ?");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $data = $result->fetch_assoc();
            $password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->query("UPDATE `users` set `password` = '{$password}'");
            if($update){
                $_SESSION['msg']['success'] = "New Password has been saved successfully.";
                header('location:login.php');
                exit;
            }else{
                $error = 'Password has failed to update.';
            }
        }else{
            $error = "User is registered on this website.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once('header.php') ?>
<body>
    <h1 id="page-title" class="text-center">Reset Password</h1>
    <hr id="title_hr" class="mx-auto">
    <div id="login-wrapper">
        <div class="text-muted"><small><em>Please Fill all the required fields</em></small></div>
        <?php if(isset($error) && !empty($error)): ?>
            <div class="message-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['msg']['success']) && !empty($_SESSION['msg']['success'])): ?>
        <div class="message-success">
            <?php 
            echo $_SESSION['msg']['success'];
            unset($_SESSION['msg']);
            ?>
        </div>  
        <?php endif; ?>
        <form action="" method="POST">
            <div class="input-field">
                <label for="new_password" class="input-label">New Password</label>
                <input type="password" id="new_password" name="new_password" value="<?= $_POST['new_password'] ?? "" ?>" autofocus required="required">
            </div>
            <div class="input-field">
                <label for="confirm_password" class="input-label">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" value="<?= $_POST['confirm_password'] ?? "" ?>" required="required">
            </div>
            <button class="reset-btn">Reset Password</button>
        </form>
    </div>
</body>
</html>
