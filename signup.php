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
?>
<?php
if (isset($_SESSION['login_id']))
  header("location:index.php?page=home");
?>
<?php include 'header.php' ?>

<style>
  .signup-logo {
    text-align: center;
    margin-bottom: 20px;
    /* Adjust as needed */
  }

  .signup-logo img {
    width: 200px;
    /* Set the width as per your requirements */
    height: auto;
    /* This maintains the image's aspect ratio */
  }

  .signup-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    /* height: 100vh; 100% of viewport height */
    max-height: fit-content;
  }

  .signup-box {
    max-width: 400px;
    /* Adjust the width as needed */
    text-align: center;
    /* Center align text inside the box */
  }

  /* Add styles for the success pop-up */
  .popup-message {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
  }

  .popup-message.success {
    background-color: #d4edda;
    color: #155724;
  }

  .popup-message.error {
    background-color: #f8d7da;
    color: #721c24;
  }
</style>

<body class="hold-transition login-page bg-white">
  <div class="signup-container">
    <div class="signup-box">

      <h2><b>AQMS - Sign Up</b></h2>
      <div class="card">
        <div class="card-body signup-card-body">
          <form action="process_signup.php" method="POST" id="signup-form">
            <div class="signup-logo">
              <img src="profpraisal.png" alt="Your Image Alt Text">
            </div>
            <div class="input-group mb-3">
              <input type="text" class="form-control" name="CUID" required placeholder="CUID">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa fa-id-card"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="name" class="form-control" name="firstname" required placeholder="First Name">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="name" class="form-control" name="lastname" required placeholder="Last Name">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="email" class="form-control" name="email" required placeholder="Email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" id="password" name="password" required placeholder="Password">
              <div class="input-group-append">
                <div class="input-group-text" onclick="togglePasswordVisibility('password')">
                  <span id="passwordIcon" class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" id="Conform_password" name="Conform_password" required
                placeholder="Confirm_Password">
              <div class="input-group-append">
                <div class="input-group-text" onclick="togglePasswordVisibility('Conform_password')">
                  <span id="Conform_passwordIcon" class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="form-group mb-3">
              <label for="user_type">Select User Type</label>
              <select name="user_type" id="user_type" class="custom-select custom-select-sm">
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
              </select>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
              </div>
            </div>
          </form>
        </div>
        <!-- /.signup-card-body -->
      </div>
    </div>
  </div>
  <!-- /.signup-container -->

  <!-- Success pop-up message -->
  <div id="successPopup" class="popup-message">

  </div>


  <script>
    $(document).ready(function () {
      $('#signup-form').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serializeArray();
        var email = formData.find(input => input.name === 'email').value;

        // Verify the email address with Disify
        $.ajax({
          type: 'POST',
          url: 'verify_email.php', // This should be the path to your PHP script that calls the Disify API
          data: { email: email },
          success: function (verificationResult) {
            try {
              const verificationData = JSON.parse(verificationResult);
              // Check if the 'disposable' property is true
              if (verificationData.format && !verificationData.disposable) {
                // If the email is not disposable, proceed with the signup process
                $.ajax({
                  type: 'POST',
                  url: 'process_signup.php',
                  data: formData,
                  success: function (response) {
                    var responseData = JSON.parse(response);
                    if (responseData.status === 'success') {
                      showMessage('Signup successful! You can now <a href="login.php">log in</a>.', 'success');
                    } else {
                      showMessage('Error: ' + responseData.message, 'error');
                    }
                  },
                  error: function (xhr, status, error) {
                    showMessage('Error during signup: ' + xhr.responseText, 'error');
                  }
                });
              } else {
                // If the email is disposable, show a not deliverable message
                showMessage('Email is disposable and cannot be used for signup.', 'error');
              }
            } catch (error) {
              console.error("Error parsing verification result:", error);
              showMessage('Email verification failed: Invalid response format.', 'error');
            }
          },
          error: function (xhr, status, error) {
            // Show Disify email verification failed message
            showMessage('Email verification failed: ' + xhr.responseText, 'error');
          }
        });
      });

      function showMessage(message, type) {
        var $popup = $('#successPopup');
        $popup.html(message);
        $popup.removeClass('error success').addClass(type);
        $popup.show();
        setTimeout(function () {
          $popup.hide();
        }, 5000);
      }
    });

  </script>

  <script>
    function togglePasswordVisibility(passwordId) {
      var passwordInput = document.getElementById(passwordId);
      var passwordIcon = document.getElementById(passwordId + 'Icon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-lock');
        passwordIcon.classList.add('fa-eye');
      } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-lock');
      }
    }
  </script>
  <?php include 'footer.php' ?>
</body>

</html>
