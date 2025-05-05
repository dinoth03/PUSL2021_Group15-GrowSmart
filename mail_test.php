<?php
$to = "malindakawshalya@gmail.com"; // Replace with your email
$subject = "Test Email from GrowSmart";
$message = "This is a test email to check if PHP mail is working correctly.";
$headers = "From: noreply@growsmart.com";

$result = mail($to, $subject, $message, $headers);

if ($result) {
    echo "Test email sent successfully!";
} else {
    echo "Failed to send test email.";
    
    // Check if we can get error information
    if (function_exists('error_get_last')) {
        print_r(error_get_last());
    }
}
?>