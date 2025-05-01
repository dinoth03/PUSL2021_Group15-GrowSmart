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
            // Resize image before saving
            $max_width = 1200;  // Maximum width for post images
            $max_height = 1200; // Maximum height for post images
            
            list($width, $height) = $check;
            $image_type = $check[2]; // IMAGETYPE constant
            
            // Only resize if image is larger than our max dimensions
            if ($width > $max_width || $height > $max_height) {
                // Calculate new dimensions while maintaining aspect ratio
                if ($width > $height) {
                    $new_width = $max_width;
                    $new_height = intval($height * $max_width / $width);
                } else {
                    $new_height = $max_height;
                    $new_width = intval($width * $max_height / $height);
                }
                
                // Create a new image with the new dimensions
                $source = null;
                $destination = imagecreatetruecolor($new_width, $new_height);
                
                // Create source image based on file type
                switch ($image_type) {
                    case IMAGETYPE_JPEG:
                        $source = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                        break;
                    case IMAGETYPE_PNG:
                        $source = imagecreatefrompng($_FILES['image']['tmp_name']);
                        // Preserve transparency
                        imagealphablending($destination, false);
                        imagesavealpha($destination, true);
                        break;
                    case IMAGETYPE_GIF:
                        $source = imagecreatefromgif($_FILES['image']['tmp_name']);
                        break;
                }
                
                if ($source) {
                    // Resize
                    imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    
                    // Save the resized image
                    switch ($image_type) {
                        case IMAGETYPE_JPEG:
                            imagejpeg($destination, $target_file, 85); // 85% quality
                            break;
                        case IMAGETYPE_PNG:
                            imagepng($destination, $target_file, 8); // Compression level 8
                            break;
                        case IMAGETYPE_GIF:
                            imagegif($destination, $target_file);
                            break;
                    }
                    
                    // Free memory
                    imagedestroy($source);
                    imagedestroy($destination);
                    $image = $file_name;
                }
            } else {
                // If image is already small enough, just move it
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
                $image = $file_name;
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