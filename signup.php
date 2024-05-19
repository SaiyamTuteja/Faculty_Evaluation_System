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
  .success-popup {
    display: none;
    background-color: #4CAF50;
    color: white;
    text-align: center;
    padding: 10px;
    position: fixed;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 999;
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
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="name" class="form-control" name="lastname" required placeholder="Last Name">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="email" class="form-control" name="email" required placeholder="Email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" name="password" required placeholder="Password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" name="Conform_password" required
                placeholder="Conform_Password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
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
  <div class="success-popup" id="successPopup">
    Signup successful! You can now <a href="login.php">log in</a>.
  </div>

  <script>
    $('#signup-form').submit(function (e) {
      e.preventDefault();
      var formData = $(this).serializeArray();
      var email = formData.find(input => input.name === 'email').value;

      // Verify the email address with Verifalia
      $.ajax({
        type: 'POST',
        url: 'verify_email.php', // This is a new PHP file you'll create for verification
        data: { email: email },
        success: function (verificationResult) {
          if (verificationResult.isDeliverable) {
            // If the email is verified, proceed with the signup process
            $.ajax({
              type: 'POST',
              url: 'process_signup.php',
              data: formData,
              success: function (response) {
                // Handle the rest of the signup process
              },
              error: function (xhr, status, error) {
                console.error(xhr.responseText);
              }
            });
          } else {
            // Handle the case where the email is not deliverable
            console.log("Email is not deliverable: " + verificationResult.status);
          }
        },
        error: function (xhr, status, error) {
          console.error("Verifalia email verification failed: " + xhr.responseText);
        }
      });
    });

    function showSuccessPopup() {
      var successPopup = document.getElementById("successPopup");
      successPopup.style.display = "block";
    }
});

  </script>

  <?php include 'footer.php' ?>
</body>

</html>