<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

// 1. Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// 2. Check connection
if (!$conn) {
    $_SESSION['message'] = "❌ Connection failed: " . mysqli_connect_error();
    header("Location: ../seller.php");
    exit();
}

// 3. Get data from request
$name = $_POST["productName"];
$category = $_POST["productCategory"];
$price = $_POST["productPrice"];
$weight = $_POST["productWeight"];
$url = $_POST["imageUrl"];

// 4. Create SQL query
$sql = "INSERT INTO products(productname, category, price, weight, imageurl) VALUES ('$name', '$category', '$price', '$weight', '$url');";

// 5. Execute query
if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "✅ Product added successfully!";
} else {
    $_SESSION['message'] = "❌ Error: " . mysqli_error($conn);
}

// 6. Close connection and redirect
mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>