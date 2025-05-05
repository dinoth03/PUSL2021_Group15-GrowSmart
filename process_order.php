<?php
session_start();

// Include email functions
require_once 'email_function.php';

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "growsmartDB";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Load Stripe PHP library
require_once('vendor/autoload.php');

// Set your Stripe API key
\Stripe\Stripe::setApiKey('sk_test_51QtROfQj3VpmfbLOLyCrkgcaSMRIPV33eFzbfR16cL9PIm2MNcIDRxxacjBcjOody15lN5s1MYwecvzMIBJjHvY000F6oEPhxL');

// Set charset to utf8
$conn->set_charset("utf8");

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]));
}

// Get form data
$firstName = $conn->real_escape_string($_POST['firstName'] ?? '');
$lastName = $conn->real_escape_string($_POST['lastName'] ?? '');
$email = $conn->real_escape_string($_POST['email'] ?? '');
$phone = $conn->real_escape_string($_POST['phone'] ?? '');
$address = $conn->real_escape_string($_POST['address'] ?? '');
$city = $conn->real_escape_string($_POST['city'] ?? '');
$zipCode = $conn->real_escape_string($_POST['zipCode'] ?? '');
$paymentMethod = $conn->real_escape_string($_POST['paymentMethod'] ?? '');
$items = $_POST['items'] ?? '[]';
$total = $conn->real_escape_string($_POST['total'] ?? '');

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || 
    empty($address) || empty($city) || empty($zipCode) || empty($paymentMethod)) {
    die(json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields'
    ]));
}

// Generate order number
$orderNumber = 'GS' . date('Ymd') . rand(1000, 9999);

// Insert order into database
$sql = "INSERT INTO orders (order_number, first_name, last_name, email, phone, address, 
        city, zip_code, payment_method, items, total, order_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssssssss', $orderNumber, $firstName, $lastName, $email, $phone, 
                  $address, $city, $zipCode, $paymentMethod, $items, $total);

if (!$stmt->execute()) {
    die(json_encode([
        'success' => false,
        'message' => 'Error saving order: ' . $stmt->error
    ]));
}

if (isset($_POST['paymentMethod']) && $_POST['paymentMethod'] === 'paypal') {
    $paypal_order_id = isset($_POST['paypalOrderId']) ? $_POST['paypalOrderId'] : null;
    $paypal_payer_id = isset($_POST['paypalPayerId']) ? $_POST['paypalPayerId'] : null;
    
    if (!$paypal_order_id || !$paypal_payer_id) {
        die(json_encode([
            'success' => false,
            'message' => 'Missing PayPal transaction details'
        ]));
    }
    
    // You'd typically verify the PayPal payment with PayPal's API here
    // For now, we'll just record the payment details
    
    $payment_status = 'Completed'; // For PayPal payments, we assume completed since PayPal handles this
    $payment_id = $paypal_order_id;
    $payment_method = 'PayPal';
}

// Get the inserted order ID
$orderId = $stmt->insert_id;
$stmt->close();

// Get full order data for email
$orderQuery = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();
$orderData = $result->fetch_assoc();
$stmt->close();

// Decode items for the email
$itemsArray = json_decode($orderData['items'], true);

// Format total for email
$formattedTotal = is_numeric($orderData['total']) ? 'Rs. ' . number_format($orderData['total'], 2) : $orderData['total'];

// Send receipt email
$emailSent = sendReceiptEmail($orderData, $itemsArray, $formattedTotal);

// Return success response
echo json_encode([
    'success' => true,
    'orderId' => $orderId,
    'orderNumber' => $orderNumber,
    'emailSent' => $emailSent
]);

$conn->close();
?>