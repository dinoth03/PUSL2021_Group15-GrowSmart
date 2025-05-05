<?php
session_start();

// Get order ID from URL parameter
$orderId = isset($_GET['id']) ? $_GET['id'] : '';
$orderNumber = isset($_GET['orderNumber']) ? $_GET['orderNumber'] : '';

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

// Include email function
require_once 'email_function.php';

// Try to send email with error handling
try {
    $emailSent = sendReceiptEmail($order, $items, $formattedTotal);
} catch (Exception $e) {
    // Log the error
    error_log("Email sending failed: " . $e->getMessage());
    $emailSent = false;
}

// Close the database connection
$conn->close();

// Redirect back to order confirmation with email status
header("Location: order_confirmation.php?id=" . $orderId . "&orderNumber=" . $orderNumber . "&emailSent=" . ($emailSent ? '1' : '0'));
exit();
?>