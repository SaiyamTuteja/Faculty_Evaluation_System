

<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('./db_connect.php');
ob_start();

$system = $conn->query("SELECT * FROM system_settings")->fetch_array();
foreach($system as $k => $v){
  $_SESSION['system'][$k] = $v;
}

ob_end_flush();
?>
<?php
if(isset($_SESSION['login_id']))
  header("location:index.php?page=home");
?>
<?php include 'header.php' ?>

<style>
  .signup-logo {
    text-align: center;
    margin-bottom: 20px; /* Adjust as needed */
  }

  .signup-logo img {
    width: 200px; /* Set the width as per your requirements */
    height: auto; /* This maintains the image's aspect ratio */
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
    max-width: 400px; /* Adjust the width as needed */
    text-align: center; /* Center align text inside the box */
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
              <input type="text" class="form-control" id="CUID" name="CUID" required placeholder="CUID">
              <div class="input-group-append">
                <div class="input-group-text">
                  <i class="fa-regular fa-id-card"></i>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="name" class="form-control" name="firstname" id="firstname" required placeholder="First Name">
              <div class="input-group-append">
                <div class="input-group-text">
                  <i class="fa-regular fa-user"></i>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="name" class="form-control" name="lastname" id="lastname" required placeholder="Last Name">
              <div class="input-group-append">
                <div class="input-group-text">
                  <i class="fa-regular fa-user"></i>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="email" class="form-control" name="email" id="email" required placeholder="Email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <i class="fa-regular fa-envelope"></i>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" name="password" required placeholder="Password">
              <div class="input-group-append">
                <div class ="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" name="Conform_password" required placeholder="Conform_Password">
              <div class="input-group-append">
                <div class ="input-group-text">
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
  <div id="successPopup" class="popup-message">

  </div>

  <script>


    function validateForm(event) {
      // Getting refrences of the input fields
      let firstname = document.getElementById('firstname').value;
      let lastname = document.getElementById('lastname').value;
      let CUID = document.getElementById('CUID').value;
      let password = document.getElementById('password').value;
      let Confirm_Password = document.getElementById('Conform_password').value;
      
      // Validating CUID
      if (CUID.length > 100) {
        showMessage("CUID must be of less than 100 characters", "error");
        return false;
      }

      // Validating firstname
      if (firstname.length > 200 || !/^[A-Za-z]+$/.test(firstname)) {
        alert("Invalid!");
        showMessage("Firstname must be of less than 200 characters and must contain only alphabets", "error");
        return false;
      }

      // Validating lastname
      if (lastname.length > 200 || !/^[A-Za-z]+$/.test(lastname)) {
        showMessage("Lastname must be of less than 200 characters and must contain only alphabets", "error");
        return false;
      }

      // Validating password
      if (password == Confirm_Password) {
        if (password.length < 8 || !/[A-Z]/.test(password) || !/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(password)) {
          showMessage("Enter Strong Paasword", 'error');
          return false;
        }
      }else{
        showMessage("Password did not match", 'error');
        return false;
      }
      return true; // Everything goes right, submit the form!
    }

  </script>

  <script>
    $(document).ready(function () {
      $('#signup-form').submit(function (e) {
        e.preventDefault();
        if (validateForm(event)) {
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
                console.log(verificationData);
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
        }else
          return;
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

  </script>
  
  <?php include 'footer.php' ?>
</body>
</html>