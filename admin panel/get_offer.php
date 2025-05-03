<?php
session_start();

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

// Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get offer data
    $query = "SELECT * FROM offers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'offer' => $row]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Offer not found']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
}

mysqli_close($conn);
?>