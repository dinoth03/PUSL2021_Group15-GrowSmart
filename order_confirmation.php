<?php
session_start();

// Get order ID and email status from URL parameters
$orderId = isset($_GET['id']) ? $_GET['id'] : '';
$orderNumber = isset($_GET['orderNumber']) ? $_GET['orderNumber'] : '';
$emailSent = isset($_GET['emailSent']) ? ($_GET['emailSent'] == '1') : false;
$sendEmail = isset($_GET['sendEmail']) ? ($_GET['sendEmail'] == '1') : false;

// If no ID was provided in URL, redirect to home
if (empty($orderId)) {
    header("Location: index.php");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "growsmartDB";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order data
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Order not found
    header("Location: index.php");
    exit();
}

$order = $result->fetch_assoc();
$items = json_decode($order['items'], true);

// Format total with currency symbol
$formattedTotal = is_numeric($order['total']) ? 'Rs. ' . number_format($order['total'], 2) : $order['total'];

// Only send email if explicitly requested and not already sent
if ($sendEmail && !$emailSent) {
    // Include email function
    require_once 'email_function.php';
    $emailSent = sendReceiptEmail($order, $items, $formattedTotal);
    
    // Redirect with updated email status but don't trigger email sending again
    header("Location: order_confirmation.php?id=" . $orderId . "&orderNumber=" . $order['order_number'] . "&emailSent=" . ($emailSent ? '1' : '0'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - GrowSmart</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
            

        body {
            background: linear-gradient(to right, #e2e2e2, #d5ffdd);
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .confirmation-container {
            max-width: 800px;
            margin: 30px auto;
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #2e7d32;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
        }
        .header .logo {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 50px;
            height: 50px;
        }
        .order-details {
            border: 1px solid #eee;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            background: #fcfcfc;
        }
        .order-items {
            margin-bottom: 20px;
        }
        .item {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .item:last-child {
            border-bottom: none;
        }
        .total {
            font-weight: bold;
            text-align: right;
            font-size: 1.3em;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #2e7d32;
            color: #2e7d32;
        }
        .btn-continue {
            background: #d5f3d5;
            border: 0.1rem solid #00ff00;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-continue:hover {
            background: #00ff00;
            color: white;
            transform: translateY(-2px);
        }
        .btn-download {
            background: #f0f9ff;
            border: 0.1rem solid #0d6efd;
            padding: 10px 20px;
            font-weight: 600;
            color: #0d6efd;
            transition: all 0.3s ease;
        }
        .btn-download:hover {
            background: #0d6efd;
            color: white;
            transform: translateY(-2px);
        }
        .email-status {
            margin-top: 25px;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid;
        }
        .email-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #155724;
        }
        .email-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #721c24;
        }
        .order-section-title {
            color: #2e7d32;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .btn-action-group {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .order-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .order-info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            height: 100%;
        }
        .receipt-number {
            font-size: 1.2em;
            color: #666;
            text-align: center;
            margin: 15px 0;
        }
        @media (max-width: 768px) {
            .order-info-grid {
                grid-template-columns: 1fr;
            }
            .btn-action-group {
                flex-direction: column;
            }
        }
        .badge-payment {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
            background: #eef7ee;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
    </style>
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="header">
            <img src="Img/TitleLogo.png" alt="GrowSmart Logo" class="logo">
            <h2><i class="fas fa-check-circle me-2"></i>Order Confirmation</h2>
            <p class="mb-0">Your order has been placed successfully!</p>
        </div>
        
        <div class="receipt-number">
            <span>Receipt # <?php echo $order['order_number']; ?></span>
        </div>
        
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading"><i class="fas fa-leaf me-2"></i>Thank you for your order!</h4>
            <p>Your order has been received and is being processed. You will receive an update when your order ships.</p>
        </div>
        
        <!-- Rest of your HTML content remains the same -->
        
        <!-- Email Status Indicator -->
        <?php if ($emailSent): ?>
        <div class="email-status email-success">
            <p><strong><i class="fas fa-envelope me-2"></i>Confirmation Email Sent</strong></p>
            <p>A confirmation email has been sent to <?php echo $order['email']; ?>. Please check your inbox.</p>
        </div>
        <?php else: ?>
        <div class="email-status email-error">
            <p><strong><i class="fas fa-exclamation-triangle me-2"></i>Email Delivery Status</strong></p>
            <p>Your order has been processed, but there might be an issue with the confirmation email. Please check your spam folder or contact customer support if you don't receive an email.</p>
        </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between mt-4">
            <div>
                <a href="generate_receipt_pdf.php?id=<?php echo $orderId; ?>" class="btn btn-download" target="_blank">
                    <i class="fas fa-file-download me-2"></i>Download Receipt
                </a>
                <a href="order_confirmation.php?id=<?php echo $orderId; ?>&orderNumber=<?php echo $order['order_number']; ?>&sendEmail=1" class="btn btn-download ms-2">
                    <i class="fas fa-envelope me-2"></i>Email Receipt
                </a>
            </div>
            <a href="shop.php" class="btn btn-continue">
                <i class="fas fa-shopping-basket me-2"></i>Continue Shopping
            </a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>