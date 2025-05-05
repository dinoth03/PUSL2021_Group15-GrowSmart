<?php
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    $_SESSION['message'] = "❌ Unauthorized: Please log in first.";
    header("Location: ../seller.php");
    exit();
}

// Database connection
// $server = "localhost";
// $username = "root";
// $password = "Himasha@1218";
// $db = "growsmart";

$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if (!$conn) {
    $_SESSION['message'] = "❌ Connection failed: " . mysqli_connect_error();
    header("Location: ../seller.php");
    exit();
}

// Get form data
$name = $_POST["productName"];
$category = $_POST["productCategory"];
$price = $_POST["productPrice"];
$weight = $_POST["productWeight"];
$url = $_POST["imageUrl"];

// Prepare and execute SQL with user_id
$sql = "INSERT INTO products (productname, category, price, weight, imageurl, user_id)
        VALUES ('$name', '$category', '$price', '$weight', '$url', '$userId')";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "✅ Product added successfully!";
} else {
    $_SESSION['message'] = "❌ Error: " . mysqli_error($conn);
}

mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>
