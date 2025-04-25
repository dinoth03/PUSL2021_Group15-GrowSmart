<?php
// Save as: payment.php
require 'paydb_config.php';
require 'vendor/autoload.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get form data
$product     = $_POST['product'];
$amount      = $_POST['amount'];
$card_number = $_POST['card_number'];
$expiry      = $_POST['expiry'];
$cvv         = $_POST['cvv'];
$first_name  = $_POST['first_name'];
$last_name   = $_POST['last_name'];
$email       = $_POST['email'];

try {
    // Insert into DB
    $stmt = $pdo->prepare("INSERT INTO payments (product, amount, card_number, expiry, cvv, first_name, last_name, email) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$product, $amount, $card_number, $expiry, $cvv, $first_name, $last_name, $email]);

    // Send confirmation email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'dinoth08@gmail.com'; // ✅ Your Gmail
    $mail->Password   = 'zpzp thom tyqi oyem';   // ✅ Gmail App Password (not your Gmail password!)
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('yourgmail@gmail.com', 'GrowSmart');
    $mail->addAddress($email, "$first_name $last_name");
    $mail->Subject = 'Payment Successful - GrowSmart';
    $mail->Body    = "Dear $first_name,\n\nThank you for your purchase of $product for $$amount.\nYour order will be delivered soon.\n\n- GrowSmart Team";

    $mail->send();

    // Display receipt
    echo "
    <html><head><link rel='stylesheet' href='css/Payment.css'><title>Receipt</title></head><body>
    <div class='container'>
      <div class='section'>
        <h3>Receipt</h3><hr>
        <div class='receipt'>
          <p><b>Name:</b> $first_name $last_name</p>
          <p><b>Email:</b> $email</p>
          <p><b>Product:</b> $product</p>
          <p><b>Amount:</b> $$amount</p>
          <p><b>Status:</b> Payment Successful ✅</p>
        </div>
        <br><a href='home new.html' class='back-btn'>Back to Home</a>
      </div>
    </div></body></html>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
