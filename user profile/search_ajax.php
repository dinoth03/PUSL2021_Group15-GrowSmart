<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$current_user_id = $_SESSION['user_id'];
$search_results = [];

if (isset($_GET['q'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['q']);
    
    // Search for users by name or email with their average rating
    $query = "SELECT u.id, u.first_name, u.last_name, u.profile_pic, u.account_type,
              COALESCE(AVG(r.rating), 0) as avg_rating,
              COUNT(r.id) as rating_count
              FROM users u
              LEFT JOIN ratings r ON u.id = r.rated_id
              WHERE (u.first_name LIKE '%$search_term%' 
              OR u.last_name LIKE '%$search_term%' 
              OR u.email LIKE '%$search_term%')
              AND u.id != $current_user_id 
              GROUP BY u.id
              LIMIT 10";
              
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if current user has already rated this user
        $has_rated_query = "SELECT id FROM ratings WHERE rater_id = $current_user_id AND rated_id = " . $row['id'];
        $rating_result = mysqli_query($conn, $has_rated_query);
        $has_rated = mysqli_num_rows($rating_result) > 0;
        
        $search_results[] = [
            'id' => $row['id'],
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'profile_pic' => $row['profile_pic'],
            'account_type' => $row['account_type'],
            'avg_rating' => round($row['avg_rating'], 1),
            'rating_count' => $row['rating_count'],
            'has_rated' => $has_rated
        ];
    }
}

// Return results as JSON
header('Content-Type: application/json');
echo json_encode($search_results);
?>