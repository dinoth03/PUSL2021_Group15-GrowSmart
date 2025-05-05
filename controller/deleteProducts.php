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

// Delete only if product belongs to the current user
$sql = "DELETE FROM products WHERE itemid = $id AND user_id = $userId";

if (mysqli_query($conn, $sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['message'] = "🗑️ Product deleted successfully!";
    } else {
        $_SESSION['message'] = "⚠️ You can only delete your own products.";
    }
} else {
    $_SESSION['message'] = "❌ Delete failed: " . mysqli_error($conn);
}

mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>
