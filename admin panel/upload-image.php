<?php
// Include database connection
require_once 'db_connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Response array
$response = [
    'status' => 'error',
    'message' => 'No file uploaded'
];

// Check if file was uploaded
if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
    // Create directory if it doesn't exist
    $targetDir = 'images/products/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Generate a unique filename to prevent overwriting
    $fileName = uniqid() . '_' . basename($_FILES['productImage']['name']);
    $targetFile = $targetDir . $fileName;
    
    // Check file type
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        $response['message'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
    } else {
        // Try to upload file
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFile)) {
            $response['status'] = 'success';
            $response['message'] = 'File uploaded successfully';
            $response['file_path'] = $targetFile;
            
            // Update image in database if it's an edit operation and productId is provided
            if (isset($_POST['productId']) && !empty($_POST['productId'])) {
                $productId = intval($_POST['productId']);
                
                if ($productId > 0) {
                    // Get just the filename part
                    $fileNameOnly = $fileName;
                    
                    // Update the product's image URL
                    $sql = "UPDATE products SET imageurl = ? WHERE itemid = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('si', $fileNameOnly, $productId);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        } else {
            $response['message'] = 'Sorry, there was an error uploading your file.';
        }
    }
} else {
    $response['message'] = 'File upload error: ' . $_FILES['productImage']['error'];
}

echo json_encode($response);
?>