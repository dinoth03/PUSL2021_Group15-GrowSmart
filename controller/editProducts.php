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
$result = mysqli_query($conn, "SELECT * FROM products WHERE itemid=$id LIMIT 1");
if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['edit_product'] = $row;
}
mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>