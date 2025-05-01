<?php
// Format date to a readable format
function formatDate($date) {
    return date("F j, Y, g:i a", strtotime($date));
}

// Get user data by ID
function getUserById($conn, $user_id) {
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Count followers
function countFollowers($conn, $user_id) {
    $query = "SELECT COUNT(*) as count FROM follows WHERE followed_id = $user_id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['count'];
}

// Check if user is following another user
function isFollowing($conn, $follower_id, $followed_id) {
    $query = "SELECT * FROM follows WHERE follower_id = $follower_id AND followed_id = $followed_id";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// Count likes on a post
function countLikes($conn, $post_id) {
    $query = "SELECT COUNT(*) as count FROM likes WHERE post_id = $post_id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['count'];
}
?>