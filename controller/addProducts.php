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
$name = $_REQUEST["productname"];
$category = $_REQUEST["category"];
$price = $_REQUEST["price"];
$weight = $_REQUEST["weight"];
$url = $_REQUEST["url"];

// 4. Create SQL query
$sql = "INSERT INTO product(productname, category, price, weight, imageurl) VALUES ('$name', '$category', '$price', '$weight', '$url');";

// 5. Execute query
if (mysqli_query($conn, $sql)) {
    echo "✅ Product added successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

// 6. Close connection
mysqli_close($conn);

?>
