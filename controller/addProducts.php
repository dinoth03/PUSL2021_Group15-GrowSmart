<?php

$server = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

// 1. Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// 2. Check connection
if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
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
    echo "✅ Product added successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

// 6. Close connection
mysqli_close($conn);

?>



