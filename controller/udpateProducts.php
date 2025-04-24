<!--?php
session_start();
$server = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

$conn = mysqli_connect($server, $username, $password, $db);
if (!$conn) {
    $_SESSION['message'] = "❌ Connection failed: " . mysqli_connect_error();
    header("Location: ../seller.php");
    exit();
}

$id = $_POST["product_id"];
$name = $_POST["productName"];
$category = $_POST["productCategory"];
$price = $_POST["productPrice"];
$weight = $_POST["productWeight"];
$url = $_POST["imageUrl"];

$sql = "UPDATE products SET productname='$name', category='$category', price='$price', weight='$weight', imageurl='$url' WHERE itemid=$id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "✅ Product updated successfully!";
} else {
    $_SESSION['message'] = "❌ Update failed: " . mysqli_error($conn);
}
unset($_SESSION['edit_product']);
mysqli_close($conn);
header("Location: ../seller.php");
exit();
?-->



<?php
session_start();

// Database connection
$server = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

// Create connection
$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if (!$conn) {
    $_SESSION['message'] = "❌ Connection failed: " . mysqli_connect_error();
    header("Location: ../seller.php");
    exit();
}

// Check if all required POST data is set
if (isset($_POST["product_id"], $_POST["productName"], $_POST["productCategory"], $_POST["productPrice"], $_POST["productWeight"], $_POST["imageUrl"])) {
    $id = $_POST["product_id"];
    $name = $_POST["productName"];
    $category = $_POST["productCategory"];
    $price = $_POST["productPrice"];
    $weight = $_POST["productWeight"];
    $url = $_POST["imageUrl"];

    // Prepare the update statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE products SET productname=?, category=?, price=?, weight=?, imageurl=? WHERE itemid=?");
    $stmt->bind_param("sssssi", $name, $category, $price, $weight, $url, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Product updated successfully!";
    } else {
        $_SESSION['message'] = "❌ Update failed: " . $stmt->error;
    }

    // Close the prepared statement and connection
    $stmt->close();
    mysqli_close($conn);
} else {
    $_SESSION['message'] = "❌ Missing required product information!";
}

// Redirect to seller.php after handling the request
header("Location: ../seller.php");
exit();
?>
