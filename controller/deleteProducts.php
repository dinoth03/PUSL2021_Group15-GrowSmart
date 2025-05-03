<?php
session_start();
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
$sql = "DELETE FROM products WHERE itemid=$id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "🗑️ Product deleted successfully!";
} else {
    $_SESSION['message'] = "❌ Delete failed: " . mysqli_error($conn);
}
mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>