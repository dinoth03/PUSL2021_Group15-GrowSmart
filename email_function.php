<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendReceiptEmail($order, $items, $formattedTotal) {
    // Make sure we have an email to send to
    if (empty($order['email'])) {
        error_log("Cannot send email: no recipient email address");
        return false;
    }
    
    // Check if PHPMailer is installed
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        error_log("PHPMailer not installed. Cannot send email.");
        return false;
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 0;                      // Enable verbose debug output (set to 2 for debugging)
        $mail->isSMTP();                           // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';      // SMTP server
        $mail->SMTPAuth   = true;                  // Enable SMTP authentication
        $mail->Username   = 'dinoth08@gmail.com'; // SMTP username
        $mail->Password   = 'zpzp thom tyqi oyem'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS
        $mail->Port       = 587;                   // TCP port to connect to
        
        // Recipients
        $mail->setFrom('dinoth08@gmail.com', 'GrowSmart');
        $mail->addAddress($order['email'], $order['first_name'] . ' ' . $order['last_name']);
        $mail->addReplyTo('dinoth08@gmail.com', 'GrowSmart Support');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your GrowSmart Order Receipt #' . $order['order_number'];
        $mail->Body    = generateHtmlReceipt($order, $items, $formattedTotal);
        $mail->AltBody = strip_tags(generateHtmlReceipt($order, $items, $formattedTotal));
        
        $mail->send();
        error_log("Email sent successfully to " . $order['email']);
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Generates HTML content for the receipt email
 */
function generateHtmlReceipt($order, $items, $formattedTotal) {
    // Start capturing output
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
                line-height: 1.6;
            }
            .receipt-container {
                max-width: 800px;
                margin: 0 auto;
                border: 1px solid #ddd;
                padding: 20px;
                background-color: #fff;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #2e7d32;
            }
            .receipt-number {
                font-size: 1.2em;
                color: #666;
                margin: 10px 0;
            }
            h1, h2, h3 {
                color: #2e7d32;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border: 1px solid #ddd;
            }
            th {
                background-color: #f5f5f5;
            }
            .total {
                font-weight: bold;
                text-align: right;
                font-size: 1.2em;
                margin-top: 20px;
                color: #2e7d32;
            }
            .info-section {
                margin-bottom: 20px;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                padding-top: 10px;
                border-top: 1px solid #ddd;
                font-size: 0.9em;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="receipt-container">
            <div class="header">
                <h1>GrowSmart</h1>
                <div class="receipt-number">Receipt #<?php echo htmlspecialchars($order['order_number']); ?></div>
                <div>Order Date: <?php echo date('F j, Y \a\t g:i a', strtotime($order['order_date'])); ?></div>
            </div>
            
            <div class="info-section">
                <h2>Customer Information</h2>
                <p>
                    <strong>Name:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br>
                    <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?><br>
                    <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?>
                </p>
            </div>
            
            <div class="info-section">
                <h2>Shipping Information</h2>
                <p>
                    <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br>
                    <?php echo htmlspecialchars($order['address']); ?><br>
                    <?php echo htmlspecialchars($order['city'] . ', ' . $order['zip_code']); ?>
                </p>
            </div>
            
            <div class="info-section">
                <h2>Payment Information</h2>
                <p>
                    <strong>Method:</strong> <?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?>
                </p>
            </div>
            
            <div class="info-section">
                <h2>Order Summary</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <?php 
                                    $name = $item['name'] ?? 'Unknown Item';
                                    $quantity = $item['quantity'] ?? 1;
                                    $price = $item['price'] ?? 'N/A';
                                    
                                    // Remove currency symbol for calculation
                                    $numericPrice = str_replace(['Rs.', 'Rs', 'rs.', 'rs', ','], '', $price);
                                    $numericPrice = trim($numericPrice);
                                    
                                    if (is_numeric($numericPrice) && is_numeric($quantity)) {
                                        $subtotal = $numericPrice * $quantity;
                                        $subtotalFormatted = 'Rs. ' . number_format($subtotal, 2);
                                    } else {
                                        $subtotalFormatted = 'N/A';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($name); ?></td>
                                    <td><?php echo htmlspecialchars($quantity); ?></td>
                                    <td><?php echo htmlspecialchars($price); ?></td>
                                    <td><?php echo htmlspecialchars($subtotalFormatted); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Order items information not available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="total">
                    Total: <?php echo htmlspecialchars($formattedTotal); ?>
                </div>
            </div>
            
            <div class="footer">
                <p>Thank you for shopping with GrowSmart!</p>
                <p>If you have any questions about your order, please contact us at malindakawshalya@gmail.com</p>
                <p>&copy; <?php echo date('Y'); ?> GrowSmart. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    <?php
    // Get the captured output and return it
    return ob_get_clean();
}
?>