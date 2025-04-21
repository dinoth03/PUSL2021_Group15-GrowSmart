<?php
$hostname = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

// Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if ($con->connect_error) {
    die("Database Connection failed: " . $con->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $con->real_escape_string($_POST["name"]);
    $address = $con->real_escape_string($_POST["address"]);
    $phone   = $con->real_escape_string($_POST["phone"]);
    $total   = $con->real_escape_string($_POST["total"]); // We'll pass it via hidden field

    // Insert into database
    $sql = "INSERT INTO Delivery (name, address, phone, total) VALUES ('$name', '$address', '$phone', '$total')";

    if ($con->query($sql)) {
        echo "<script>alert('Delivery details saved successfully!'); window.location.href='payment.html';</script>";
    } else {
        echo "<script>alert('Failed to save details'); window.history.back();</script>";
    }

    $con->close();
}
?>
