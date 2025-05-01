<?php
session_start();
require_once 'db.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$follower_id = $_SESSION['user_id'];

// Handle follow
if (isset($_POST['follow']) && isset($_POST['followed_id'])) {
    $followed_id = $_POST['followed_id'];
    $created_at = date("Y-m-d H:i:s");
    
    $query = "INSERT IGNORE INTO follows (follower_id, followed_id, created_at) VALUES ('$follower_id', '$followed_id', '$created_at')";
    mysqli_query($conn, $query);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Handle unfollow
if (isset($_POST['unfollow']) && isset($_POST['followed_id'])) {
    $followed_id = $_POST['followed_id'];
    
    $query = "DELETE FROM follows WHERE follower_id = '$follower_id' AND followed_id = '$followed_id'";
    mysqli_query($conn, $query);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>