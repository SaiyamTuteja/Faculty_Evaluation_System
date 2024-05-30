<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('./db_connect.php');
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

<style>
  .login-logo {
    text-align: center;
    margin-bottom: 20px;
  }
  .login-logo img {
    width: 200px;
    height: auto;
  }
  .message-error {
    color: red;
  }
  .message-success {
    color: green;
  }
</style>

<body class="hold-transition login-page bg-white">
  <h2><b>COER AQMS</b></h2>
  <div class="login-box">
    <div class="login-logo">
      <img src="profpraisal.png" alt="Your Image Alt Text">
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <div class="login-logo">
          <img src="profpraisal.png" alt="Your Image Alt Text">
        </div>
        <?php if (isset($error) && !empty($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['msg']['success']) && !empty($_SESSION['msg']['success'])): ?>
          <div class="alert alert-success">
            <?php 
            echo $_SESSION['msg']['success'];
            unset($_SESSION['msg']);
            ?>
          </div>
        <?php endif; ?>
        <form action="" method="POST" id="login-form">
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" required placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" required placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock" onclick="togglePasswordVisibility()"></span>
                <span class="fas fa-eye" style="display:none;" onclick="togglePasswordVisibility()"></span>
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="">Login As</label>
            <select name="login" class="custom-select custom-select-sm">
              <option value="3">Student</option>
              <option value="2">Faculty</option>
              <option value="1">Admin</option>
            </select>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">Remember Me</label>
              </div>
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
          </div>
          <p class="mb-1">
            Don't have an account? <a href="signup.php" class="text-center">Sign up</a>
          </p>
        </form>
      </div>
    </div>
  </div>

  <script>
    function togglePasswordVisibility() {
      var passwordInput = document.querySelector('[name="password"]');
      var lockIcon = document.querySelector('.fa-lock');
      var eyeIcon = document.querySelector('.fa-eye');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        lockIcon.style.display = 'none';
        eyeIcon.style.display = 'block';
      } else {
        passwordInput.type = 'password';
        lockIcon.style.display = 'block';
        eyeIcon.style.display = 'none';
      }
    }
  </script>

  <?php include 'footer.php' ?>
</body>
</html>
