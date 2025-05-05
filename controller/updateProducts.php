<?php
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    $_SESSION['message'] = "❌ Unauthorized: Please log in first.";
    header("Location: ../seller.php");
    exit();
}

// $server = "localhost";
// $username = "root";
// $password = "Himasha@1218";
// $db = "growsmart";

$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

$conn = mysqli_connect($server, $username, $password, $db);

if (!$conn) {
    $_SESSION['message'] = "❌ Connection failed: " . mysqli_connect_error();
    header("Location: ../seller.php");
    exit();
}

// Get form data
$id = $_POST["product_id"];
$name = $_POST["productName"];
$category = $_POST["productCategory"];
$price = $_POST["productPrice"];
$weight = $_POST["productWeight"];
$url = $_POST["imageUrl"];

// Check if the product belongs to the user
$checkQuery = "SELECT itemid FROM products WHERE itemid = ? AND user_id = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("ii", $id, $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $_SESSION['message'] = "⚠️ You are not authorized to update this product.";
    $checkStmt->close();
    mysqli_close($conn);
    header("Location: ../seller.php");
    exit();
}
$checkStmt->close();

// Perform the update
$updateStmt = $conn->prepare("UPDATE products SET 
                               productname = ?, 
                               category = ?, 
                               price = ?, 
                               weight = ?, 
                               imageurl = ? 
                               WHERE itemid = ? AND user_id = ?");
$updateStmt->bind_param("ssdssii", $name, $category, $price, $weight, $url, $id, $userId);

if ($updateStmt->execute()) {
    $_SESSION['message'] = "✅ Product updated successfully!";
} else {
    $_SESSION['message'] = "❌ Error: " . $updateStmt->error;
}

$updateStmt->close();
mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>
