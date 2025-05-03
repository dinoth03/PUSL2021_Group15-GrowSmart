<?php
session_start();
require_once 'db.php'; 

// Function to send email receipt
function sendReceiptEmail($to, $name, $products, $total, $payment_id, $payment_method) {
    // Email subject
    $subject = "GrowSmart - Payment Receipt";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: GrowSmart <noreply@growsmart.com>' . "\r\n";
    
    // Email body
    $message = '
    <html>
    <head>
        <title>Payment Receipt</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2e7d32; color: white; padding: 10px; text-align: center; }
            .receipt { border: 1px solid #ddd; padding: 15px; margin-top: 20px; }
            .receipt-item { margin-bottom: 10px; }
            .total { font-weight: bold; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px; }
            .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>GrowSmart</h2>
                <p>Payment Receipt</p>
            </div>
            <p>Hello ' . $name . ',</p>
            <p>Thank you for your purchase! Your payment has been successfully processed.</p>
            
            <div class="receipt">
                <div class="receipt-item"><strong>Transaction ID:</strong> ' . $payment_id . '</div>
                <div class="receipt-item"><strong>Payment Method:</strong> ' . $payment_method . '</div>
                <div class="receipt-item"><strong>Date:</strong> ' . date("Y-m-d H:i:s") . '</div>
                
                <div class="receipt-item">
                    <strong>Products:</strong>
                    <div>' . $products . '</div>
                </div>
                
                <div class="total">
                    <strong>Total Amount:</strong> $' . number_format($total, 2) . '
                </div>
            </div>
            
            <p>If you have any questions about your purchase, please contact our customer support.</p>
            
            <div class="footer">
                <p>This is an automated email. Please do not reply.</p>
                <p>&copy; ' . date("Y") . ' GrowSmart. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    // Send email
    return mail($to, $subject, $message, $headers);
}

// Handle payment data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON data
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Extract payment information
    $payment_method = isset($data['payment_method']) ? $data['payment_method'] : '';
    $payment_id = isset($data['payment_id']) ? $data['payment_id'] : '';
    $email = isset($data['email']) ? $data['email'] : '';
    $name = isset($data['name']) ? $data['name'] : '';
    $products = isset($data['products']) ? $data['products'] : '';
    $total = isset($data['total']) ? floatval($data['total']) : 0;
    
    // Validate data
    if (empty($payment_method) || empty($email) || empty($products) || $total <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required payment information'
        ]);
        exit;
    }
    
    // Store payment in database if user is logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($user_id) {
        $created_at = date('Y-m-d H:i:s');
        $product_list = mysqli_real_escape_string($conn, $products);
        
        $query = "INSERT INTO payments (user_id, payment_method, payment_id, amount, products, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issdss", $user_id, $payment_method, $payment_id, $total, $product_list, $created_at);
        mysqli_stmt_execute($stmt);
    }
    
    // Send receipt email
    $email_sent = sendReceiptEmail($email, $name, $products, $total, $payment_id, $payment_method);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'email_sent' => $email_sent,
        'message' => 'Payment processed successfully. Receipt has been sent to your email.'
    ]);
    exit;
} else {
    // Handle invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>