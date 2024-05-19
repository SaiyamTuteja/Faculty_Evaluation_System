<?php
require 'vendor/autoload.php';
use Verifalia\VerifaliaRestClient;

// Initialize the Verifalia client
$verifaliaClient = new VerifaliaRestClient('210196b9cbb94f48a9a4e9d8eed01b3b', 'q6G9-V.9kaVDPYs');

// Get the email from the AJAX request
$email = $_POST['email'];

// Verify the email address
$verification = $verifaliaClient->emailValidations->submit($email);

// Wait for the completion of the verification job
$verifaliaClient->emailValidations->waitForCompletion($verification->id);

// Fetch the completed verification job
$verification = $verifaliaClient->emailValidations->get($verification->id);

// Return the verification result
echo json_encode($verification->entries[0]);
?>
