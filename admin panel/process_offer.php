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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $action = $_POST['action'];
    $productName = mysqli_real_escape_string($conn, $_POST['offerProductName']);
    $category = mysqli_real_escape_string($conn, $_POST['offerCategory']);
    $originalPrice = (float)$_POST['originalPrice'];
    $discountPercentage = (int)$_POST['offerDiscountPercentage'];
    $offerPrice = (float)$_POST['offerPrice'];
    $status = (int)$_POST['offerStatus'];
    
    // Validate discount calculation
    $calculatedPrice = round($originalPrice - ($originalPrice * $discountPercentage / 100), 2);
    if (abs($calculatedPrice - $offerPrice) > 0.01) {
        // If user manually set a different price, recalculate the discount
        $actualDiscount = round(100 - (($offerPrice / $originalPrice) * 100), 0);
        $discountPercentage = $actualDiscount;
    }
    
    // Handle image upload
    $targetDir = "../uploads/offers/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageUrl = "";
    $uploadOk = 1;
    
    if (isset($_FILES["offerImage"]) && $_FILES["offerImage"]["error"] == 0) {
        $fileName = basename($_FILES["offerImage"]["name"]);
        $uniqueName = time() . '_' . $fileName;
        $targetFile = $targetDir . $uniqueName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["offerImage"]["tmp_name"]);
        if ($check === false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'File is not an image']);
            exit;
        }
        
        // Check file size (5MB max)
        if ($_FILES["offerImage"]["size"] > 5000000) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'File is too large (max 5MB)']);
            exit;
        }
        
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed']);
            exit;
        }
        
        // Upload file
        if (move_uploaded_file($_FILES["offerImage"]["tmp_name"], $targetFile)) {
            $imageUrl = "uploads/offers/" . $uniqueName;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit;
        }
    } elseif ($action === 'update' && isset($_POST['currentOfferImageUrl']) && !empty($_POST['currentOfferImageUrl'])) {
        // Keep existing image for updates
        $imageUrl = $_POST['currentOfferImageUrl'];
    } elseif ($action === 'add') {
        // New offers require an image
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please select an image for the offer']);
        exit;
    }
    
    if ($action === 'add') {
        // Insert new offer
        $sql = "INSERT INTO offers (product_name, category, original_price, discount_percentage, offer_price, image_url, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdiisi", $productName, $category, $originalPrice, $discountPercentage, $offerPrice, $imageUrl, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Offer added successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error adding offer: ' . mysqli_error($conn)]);
        }
    } elseif ($action === 'update') {
        // Update existing offer
        $offerId = (int)$_POST['offerId'];
        
        $sql = "UPDATE offers SET 
                product_name = ?, 
                category = ?, 
                original_price = ?, 
                discount_percentage = ?, 
                offer_price = ?,
                image_url = ?,
                is_active = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdiisii", $productName, $category, $originalPrice, $discountPercentage, $offerPrice, $imageUrl, $status, $offerId);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Offer updated successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error updating offer: ' . mysqli_error($conn)]);
        }
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>