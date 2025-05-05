<?php
session_start();

// Get order ID from URL parameter
$orderId = isset($_GET['id']) ? $_GET['id'] : '';

// If no ID was provided in URL, redirect to home
if (empty($orderId)) {
    header("Location: index.php");
    exit();
}

// Get order details from database
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

// Set filename for browser download
$fileName = 'GrowSmart_Receipt_' . $order['order_number'] . '.html';
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart Receipt #<?php echo $order['order_number']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2e7d32;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
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
        @media print {
            body {
                padding: 0;
            }
            .receipt-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>GrowSmart</h1>
            <div class="receipt-number">Receipt #<?php echo $order['order_number']; ?></div>
            <div>Order Date: <?php echo date('F j, Y \a\t g:i a', strtotime($order['order_date'])); ?></div>
        </div>
        
        <div class="info-section">
            <h2>Customer Information</h2>
            <p>
                <strong>Name:</strong> <?php echo $order['first_name'] . ' ' . $order['last_name']; ?><br>
                <strong>Email:</strong> <?php echo $order['email']; ?><br>
                <strong>Phone:</strong> <?php echo $order['phone']; ?>
            </p>
        </div>
        
        <div class="info-section">
            <h2>Shipping Information</h2>
            <p>
                <?php echo $order['first_name'] . ' ' . $order['last_name']; ?><br>
                <?php echo $order['address']; ?><br>
                <?php echo $order['city'] . ', ' . $order['zip_code']; ?>
            </p>
        </div>
        
        <div class="info-section">
            <h2>Payment Information</h2>
            <p>
                <strong>Method:</strong> <?php echo ucfirst($order['payment_method']); ?>
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
                Total: <?php echo $formattedTotal; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for shopping with GrowSmart!</p>
            <p>If you have any questions about your order, please contact us at support@growsmart.com</p>
            <p>&copy; <?php echo date('Y'); ?> GrowSmart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>