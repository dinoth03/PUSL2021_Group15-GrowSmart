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

$id = $_POST['product_id'];

// Fetch only if the product belongs to the logged-in user
$result = mysqli_query($conn, "SELECT * FROM products WHERE itemid = $id AND user_id = $userId LIMIT 1");

if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['edit_product'] = $row;
} else {
    $_SESSION['message'] = "⚠️ You can only edit your own products.";
}

mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>
