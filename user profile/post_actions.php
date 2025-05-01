<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle like
if (isset($_POST['like'])) {
    $post_id = $_POST['post_id'];
    $query = "INSERT IGNORE INTO likes (user_id, post_id, created_at) VALUES ('$user_id', '$post_id', NOW())";
    mysqli_query($conn, $query);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Handle unlike
if (isset($_POST['unlike'])) {
    $post_id = $_POST['post_id'];
    $query = "DELETE FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
    mysqli_query($conn, $query);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Handle comment
if (isset($_POST['add_comment'])) {
    $post_id = $_POST['post_id'];
    $content = mysqli_real_escape_string($conn, $_POST['comment_content']);
    $created_at = date("Y-m-d H:i:s");
    
    $query = "INSERT INTO comments (user_id, post_id, content, created_at) VALUES ('$user_id', '$post_id', '$content', '$created_at')";
    mysqli_query($conn, $query);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>