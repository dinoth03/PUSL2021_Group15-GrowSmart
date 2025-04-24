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

$server = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

// 1. Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// 2. Check connection
if (!$conn) {
    $_SESSION['message'] = "❌ Connection failed: " . mysqli_connect_error();
    header("Location: ../seller.php");
    exit();
}

// 3. Get data from request
$id = $_POST["product_id"];
$name = $_POST["productName"];
$category = $_POST["productCategory"];
$price = $_POST["productPrice"];
$weight = $_POST["productWeight"];
$url = $_POST["imageUrl"];

// 4. Prepare and execute UPDATE statement
$stmt = $conn->prepare("UPDATE products SET 
                       productname=?, 
                       category=?, 
                       price=?, 
                       weight=?, 
                       imageurl=? 
                       WHERE itemid=?");
$stmt->bind_param("ssdssi", $name, $category, $price, $weight, $url, $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "✅ Product updated successfully!";
} else {
    $_SESSION['message'] = "❌ Error: " . $stmt->error;
}

$stmt->close();
mysqli_close($conn);
header("Location: ../seller.php");
exit();
?>