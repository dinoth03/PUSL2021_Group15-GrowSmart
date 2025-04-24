<?php
session_start();

$server = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

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

// Insert query
$sql = "INSERT INTO delivery (Dname, Daddress, Dphone, Dtotal) VALUES ('$name', '$address', '$phone', '$total')";

// Run query
if (mysqli_query($conn, $sql)) {
    echo "<script>
        alert('✅ Delivery details added successfully!');
        window.location.href = '../payment.html';
    </script>";
} else {
    echo "<script>
        alert('❌ Error: " . mysqli_error($conn) . "');
        window.history.back();
    </script>";
}

mysqli_close($conn);
?>