<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rater_id = $_SESSION['user_id'];
    $rated_id = isset($_POST['rated_id']) ? (int)$_POST['rated_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? mysqli_real_escape_string($conn, $_POST['comment']) : '';
    $created_at = date("Y-m-d H:i:s");
    
    // Validate input
    if ($rated_id <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid rating data']);
        exit();
    }
    
    // Check if this is a new rating or an update
    $check_query = "SELECT id FROM ratings WHERE rater_id = $rater_id AND rated_id = $rated_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing rating
        $query = "UPDATE ratings 
                 SET rating = $rating, comment = '$comment', created_at = '$created_at' 
                 WHERE rater_id = $rater_id AND rated_id = $rated_id";
    } else {
        // Insert new rating
        $query = "INSERT INTO ratings (rater_id, rated_id, rating, comment, created_at) 
                 VALUES ($rater_id, $rated_id, $rating, '$comment', '$created_at')";
    }
    
    if (mysqli_query($conn, $query)) {
        // Get the updated average rating
        $avg_query = "SELECT AVG(rating) as avg_rating, COUNT(id) as count 
                     FROM ratings WHERE rated_id = $rated_id";
        $avg_result = mysqli_query($conn, $avg_query);
        $avg_data = mysqli_fetch_assoc($avg_result);
        
        $response = [
            'success' => true,
            'avg_rating' => round($avg_data['avg_rating'], 1),
            'count' => $avg_data['count']
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Database error: ' . mysqli_error($conn)
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>