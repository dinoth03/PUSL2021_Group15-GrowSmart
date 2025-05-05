<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$image = '';
$created_at = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get post content and sanitize
    $content = isset($_POST['content']) ? mysqli_real_escape_string($conn, $_POST['content']) : '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = "uploads/post_images/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Get file extension and create unique filename
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        // Check if it's actually an image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Check file size - limit to 5MB
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
                header("Location: user_profile.php");
                exit();
            }
            
            // Check file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $_SESSION['error'] = "Only JPG, PNG and GIF files are allowed.";
                header("Location: user_profile.php");
                exit();
            }
            
            // Just move the uploaded file without resizing
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $file_name;
            } else {
                $_SESSION['error'] = "Error uploading file.";
                header("Location: user_profile.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Uploaded file is not a valid image.";
            header("Location: user_profile.php");
            exit();
        }
    }
    
    // Insert post into database
    $query = "INSERT INTO posts (user_id, content, image, created_at) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $content, $image, $created_at);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: user_profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error posting: " . mysqli_error($conn);
        header("Location: user_profile.php");
        exit();
    }
}
?>