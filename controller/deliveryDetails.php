<?php
$server = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

// Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
}

$name = $_POST["name"];
$address = $_POST["address"];
$phone = $_POST["phone"];
$total = $_POST["total"];

$sql = "INSERT INTO delivery (Dname, Daddress, Dphone, Dtotal) VALUES ('$name', '$address', '$phone', '$total')";

if (mysqli_query($conn, $sql)) {
    echo "✅ Delivery details added successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

// 6. Close connection
mysqli_close($conn);



?>

<!--
 $sql = "INSERT INTO delivery (Dname, Daddress, Dphone, Dtotal) VALUES ('$name', '$address', '$phone', '$total')";

    if ($con->query($sql)) {
        echo "<script>alert('Delivery details saved successfully!'); window.location.href='payment.html';</script>";
    } else {
        echo "<script>alert('Failed to save details'); window.history.back();</script>";
    }

    $con->close();
-->
