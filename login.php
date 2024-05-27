<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include ('./db_connect.php');
ob_start();

$system = $conn->query("SELECT * FROM system_settings")->fetch_array();
foreach ($system as $k => $v) {
  $_SESSION['system'][$k] = $v;
}

ob_end_flush();

if (isset($_SESSION['login_id'])) {
    header("location:index.php?page=home");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (password_verify($password, $data['password'])) {
            foreach ($data as $k => $v) {
                if ($k != 'password') {
                    $_SESSION[$k] = $v;
                }
            }
            $_SESSION['msg']['success'] = "You have logged in successfully.";
            header('location: ./');
            exit;
        } else {
            $error = "Incorrect Email or Password";
        }
    } else {
        $error = "Incorrect Email or Password";
    }
}
?>
<?php include 'header.php' ?>
<body>
    <h1 id="page-title" class="text-center">Login Page</h1>
    <hr id="title_hr" class="mx-auto">
    <div id="login-wrapper">
        <div class="text-muted"><small><em>Please fill in all the required fields</em></small></div>
        <?php if (isset($error) && !empty($error)): ?>
            <div class="message-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['msg']['success']) && !empty($_SESSION['msg']['success'])): ?>
        <div class="message-success">
            <?php 
            echo $_SESSION['msg']['success'];
            unset($_SESSION['msg']);
            ?>
        </div>  
        <?php endif; ?>
        <form action="" method="POST">
            <div class="input-field">
                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? "", ENT_QUOTES) ?>" required="required">
            </div>
            <div class="input-field">
                <label for="password" class="input-label">Password</label>
                <input type="password" id="password" name="password" value="<?= htmlspecialchars($_POST['password'] ?? "", ENT_QUOTES) ?>" required="required">
            </div>
            <div class="input-field">
                <a href="forgot-password.php" tabindex="-1"><small><strong>Forgot Password?</strong></small></a>
            </div>
            <button class="login-btn" type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php include 'footer.php' ?>
