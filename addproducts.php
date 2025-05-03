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
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $productId = (int) $_POST['product_id'];
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $productCategory = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $productPrice = (float) $_POST['productPrice'];
    $productWeight = mysqli_real_escape_string($conn, $_POST['productWeight']);
    $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription'] ?? '');
    $currentImageUrl = $_POST['currentImageUrl'];
    $productType = $_POST['productType'];
    $isOffer = ($productType == 'offer') ? 1 : 0;
    $discountPercentage = $isOffer ? (int) $_POST['discountPercentage'] : 0;
    
    // Handle image upload
    $imageUrl = $currentImageUrl; // Use existing image by default
    $uploadOk = 1;
    
    if (isset($_FILES["productImage"]) && $_FILES["productImage"]["error"] == 0) {
        $targetDir = "../uploads/products/";
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = basename($_FILES["productImage"]["name"]);
        $uniqueName = time() . '_' . $fileName;
        $targetFile = $targetDir . $uniqueName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["productImage"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['message'] = "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES["productImage"]["size"] > 5000000) {
            $_SESSION['message'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // If everything is ok, try to upload file
        if ($uploadOk) {
            if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
                $imageUrl = "uploads/products/" . $uniqueName;
                
                // Delete old image if it exists and is not the default
                if (!empty($currentImageUrl) && file_exists("../" . $currentImageUrl)) {
                    unlink("../" . $currentImageUrl);
                }
            } else {
                $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                // But we'll continue with the update using the existing image
            }
        }
    }
    
    // Update product data in database
    $sql = "UPDATE products SET 
            productname = ?, 
            category = ?, 
            price = ?, 
            weight = ?, 
            imageurl = ?, 
            description = ?,
            is_offer = ?,
            discount = ?
            WHERE itemid = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsssiis", $productName, $productCategory, $productPrice, $productWeight, $imageUrl, $productDescription, $isOffer, $discountPercentage, $productId);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Product updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating product: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    
    // Redirect back to seller page
    header("Location: ../seller.php");
    exit();
}

mysqli_close($conn);
?>