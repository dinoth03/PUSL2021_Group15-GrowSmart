<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

// Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if (!$conn) {
    echo "<script>alert('❌ Connection failed: " . mysqli_connect_error() . "'); window.history.back();</script>";
    exit();
}

// Get data
$name = $_POST["name"];
$address = $_POST["address"];
$phone = $_POST["phone"];
$total = $_POST["total"];
$products = isset($_POST["products"]) ? $_POST["products"] : "GrowSmart Pro Package"; // Get products if available

// Store in session for later use
$_SESSION['delivery_name'] = $name;
$_SESSION['delivery_address'] = $address;
$_SESSION['delivery_phone'] = $phone;
$_SESSION['delivery_total'] = $total;
$_SESSION['delivery_products'] = $products;

// Insert query
$sql = "INSERT INTO delivery (Dname, Daddress, Dphone, Dtotal) VALUES ('$name', '$address', '$phone', '$total')";

// Run query
if (mysqli_query($conn, $sql)) {
    // Redirect to payment page with parameters
    header("Location: ../Payment.php?total=$total&products=" . urlencode($products));
    exit();
} else {
    echo "<script>
        alert('❌ Error: " . mysqli_error($conn) . "');
        window.history.back();
    </script>";
}

mysqli_close($conn);
?>