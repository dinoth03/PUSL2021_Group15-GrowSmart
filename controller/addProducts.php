<!--?php

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

// 3. Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST["productName"]);
    $category = mysqli_real_escape_string($conn, $_POST["productCategory"]);
    $price = mysqli_real_escape_string($conn, $_POST["productPrice"]);
    $weight = mysqli_real_escape_string($conn, $_POST["productWeight"]);
    $url = mysqli_real_escape_string($conn, $_POST["imageUrl"]);

    // 5. Check if fields are not empty
    if (empty($name) || empty($category) || empty($price) || empty($weight) || empty($url)) {
        echo "❌ All fields are required!";
    } else {

        // 6. Condition to handle different categories
        switch ($category) {
            case 'plants':
                // Handle plants category if needed (can include more specific logic here)
                break;
            case 'vegetables':
                // Handle vegetables category if needed (can include more specific logic here)
                break;
            case 'fruits':
                // Handle fruits category if needed (can include more specific logic here)
                break;
            case 'fertilizers':
                // Handle fertilizers category if needed (can include more specific logic here)
                break;
            default:
                echo "❌ Invalid category selected!";
                exit();
        }

        // 7. Prepare SQL query with placeholders
        $sql = "INSERT INTO products (productname, category, price, weight, imageurl) 
                VALUES (?, ?, ?, ?, ?)";

        // 8. Initialize prepared statement
        if ($stmt = mysqli_prepare($conn, $sql)) {

            // 9. Bind parameters to the query
            mysqli_stmt_bind_param($stmt, "ssdss", $name, $category, $price, $weight, $url);

            // 10. Execute the query
            if (mysqli_stmt_execute($stmt)) {
                echo "✅ Product added successfully!";
            } else {
                echo "❌ Error: " . mysqli_stmt_error($stmt);
            }

            // 11. Close statement
            mysqli_stmt_close($stmt);
        } else {
            echo "❌ Error preparing the query: " . mysqli_error($conn);
        }
    }
}

// 12. Close the connection
mysqli_close($conn);
?>

