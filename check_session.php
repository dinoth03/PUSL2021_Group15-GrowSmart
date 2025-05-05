<?php
session_start();
header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'username' => '',
    'role' => ''
];

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
    
    // Get username from session or database
    if (isset($_SESSION['username'])) {
        $response['username'] = $_SESSION['username'];
    } else {
        // Fetch username from database if not in session
        $server = "localhost";
        $username = "root";
        $password = "";
        $db = "growsmartDB";
        
        $conn = mysqli_connect($server, $username, $password, $db);
        if ($conn) {
            $userId = $_SESSION['user_id'];
            $sql = "SELECT username FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $response['username'] = $row['username'];
                // Save to session for future use
                $_SESSION['username'] = $row['username'];
            }
            
            $stmt->close();
            $conn->close();
        }
    }
    
    // Determine user role
    if (isset($_SESSION['user_role'])) {
        $response['role'] = $_SESSION['user_role'];
    } else if (isset($_SESSION['admin_id'])) {
        $response['role'] = 'admin';
    } else if (isset($_SESSION['seller_id'])) {
        $response['role'] = 'seller';
    } else {
        $response['role'] = 'user';
    }
}

echo json_encode($response);
?>