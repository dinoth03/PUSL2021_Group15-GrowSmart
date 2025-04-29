<?php
// Include database connection
require_once 'db_connection.php';

// Allow Cross-Origin Resource Sharing
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get request action
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Response array
$response = [
    'status' => 'error',
    'message' => 'Invalid action'
];

// Process based on action
switch ($action) {
    case 'get_products':
        getProducts($conn, $response);
        break;
    
    case 'get_product':
        getProduct($conn, $response);
        break;
    
    case 'add_product':
        addProduct($conn, $response);
        break;
    
    case 'update_product':
        updateProduct($conn, $response);
        break;
    
    case 'delete_product':
        deleteProduct($conn, $response);
        break;
    
    case 'get_stats':
        getProductStats($conn, $response);
        break;
    
    default:
        // Invalid action, response already set
        break;
}

// Close connection
$conn->close();

// Return JSON response
echo json_encode($response);
exit;

// Function to get all products
function getProducts($conn, &$response) {
    $products = [];
    
    $sql = "SELECT itemid AS id, productname AS name, category, price, weight AS stock, imageurl AS image
            FROM products ORDER BY itemid DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Process image URL
            if (!empty($row['image'])) {
                $row['image'] = 'images/products/' . $row['image'];
            }
            
            // Set default status (you can add a status column to your database if needed)
            $row['status'] = 'Active';
            
            $products[] = $row;
        }
        
        $response['status'] = 'success';
        $response['message'] = 'Products loaded successfully';
        $response['products'] = $products;
    } else {
        $response['message'] = 'Failed to load products: ' . $conn->error;
    }
}

// Function to get a single product
function getProduct($conn, &$response) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        $response['message'] = 'Invalid product ID';
        return;
    }
    
    $sql = "SELECT itemid AS id, productname AS name, category, price, weight AS stock, imageurl AS image 
            FROM products WHERE itemid = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        // Process image URL
        if (!empty($row['image'])) {
            $row['image'] = 'images/products/' . $row['image'];
        }
        
        // Set default status
        $row['status'] = 'Active';
        
        $response['status'] = 'success';
        $response['message'] = 'Product loaded successfully';
        $response['product'] = $row;
    } else {
        $response['message'] = 'Product not found';
    }
    
    $stmt->close();
}

// Function to add a new product
function addProduct($conn, &$response) {
    // Get raw JSON data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        $response['message'] = 'Invalid data format';
        return;
    }
    
    // Extract data and sanitize
    $name = $conn->real_escape_string($data['name']);
    $category = $conn->real_escape_string($data['category']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    
    // Extract image path and get filename only
    $image = '';
    if (isset($data['image']) && !empty($data['image'])) {
        $imagePath = $data['image'];
        $pathParts = explode('/', $imagePath);
        $image = end($pathParts);
    }
    
    $sql = "INSERT INTO products (productname, category, price, weight, imageurl) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssdis', $name, $category, $price, $stock, $image);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Product added successfully';
        $response['product_id'] = $conn->insert_id;
    } else {
        $response['message'] = 'Failed to add product: ' . $stmt->error;
    }
    
    $stmt->close();
}

// Function to update an existing product
function updateProduct($conn, &$response) {
    // Get raw JSON data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data || !isset($data['id']) || intval($data['id']) <= 0) {
        $response['message'] = 'Invalid data format or product ID';
        return;
    }
    
    // Extract data and sanitize
    $id = intval($data['id']);
    $name = $conn->real_escape_string($data['name']);
    $category = $conn->real_escape_string($data['category']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    
    // Check if image is provided
    if (isset($data['image']) && !empty($data['image'])) {
        // Extract image path and get filename only
        $imagePath = $data['image'];
        $pathParts = explode('/', $imagePath);
        $image = end($pathParts);
        
        $sql = "UPDATE products SET productname = ?, category = ?, price = ?, weight = ?, imageurl = ? WHERE itemid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdisi', $name, $category, $price, $stock, $image, $id);
    } else {
        // Update without changing the image
        $sql = "UPDATE products SET productname = ?, category = ?, price = ?, weight = ? WHERE itemid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdii', $name, $category, $price, $stock, $id);
    }
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Product updated successfully';
    } else {
        $response['message'] = 'Failed to update product: ' . $stmt->error;
    }
    
    $stmt->close();
}

// Function to delete a product
function deleteProduct($conn, &$response) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        $response['message'] = 'Invalid product ID';
        return;
    }
    
    // First retrieve the image to delete
    $sql = "SELECT imageurl FROM products WHERE itemid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        $imageFile = $row['imageurl'];
        
        // Delete from database
        $deleteSQL = "DELETE FROM products WHERE itemid = ?";
        $deleteStmt = $conn->prepare($deleteSQL);
        $deleteStmt->bind_param('i', $id);
        
        if ($deleteStmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Product deleted successfully';
            
            // Attempt to delete the image file if it exists
            if (!empty($imageFile)) {
                $filePath = 'images/products/' . $imageFile;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            $response['message'] = 'Failed to delete product: ' . $deleteStmt->error;
        }
        
        $deleteStmt->close();
    } else {
        $response['message'] = 'Product not found';
    }
    
    $stmt->close();
}

// Function to get product statistics
function getProductStats($conn, &$response) {
    // Get total count
    $totalQuery = "SELECT COUNT(*) as total FROM products";
    $totalResult = $conn->query($totalQuery);
    $totalRow = $totalResult->fetch_assoc();
    $total = $totalRow['total'];
    
    // Get plants count (assuming category contains 'plant' or is 'plants')
    $plantsQuery = "SELECT COUNT(*) as plants FROM products WHERE category LIKE '%plant%' OR category = 'plants'";
    $plantsResult = $conn->query($plantsQuery);
    $plantsRow = $plantsResult->fetch_assoc();
    $plants = $plantsRow['plants'];
    
    // Calculate others
    $others = $total - $plants;
    
    // Calculate percentages
    $plantsPercentage = $total > 0 ? round(($plants / $total) * 100) : 0;
    $othersPercentage = $total > 0 ? round(($others / $total) * 100) : 0;
    
    $response['status'] = 'success';
    $response['message'] = 'Stats loaded successfully';
    $response['stats'] = [
        'total' => $total,
        'plants' => $plants,
        'others' => $others,
        'plantsPercentage' => $plantsPercentage,
        'othersPercentage' => $othersPercentage
    ];
}
?>