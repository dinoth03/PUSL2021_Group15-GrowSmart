<?php
session_start();

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
    header("Location: ../cart.html"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $items = json_decode($_POST['checkout_items'], true);
    $total = $_POST['checkout_total'];

    if (!$userId || empty($items)) {
        $_SESSION['message'] = "❌ Invalid checkout request!";
        header("Location: ../cart.html"); 
        exit();
    }

    $success = true;

    // Start a transaction to ensure all operations succeed together
    mysqli_begin_transaction($conn);

    try {
        foreach ($items as $item) {
            $name = $item['name'];
            $price = floatval(str_replace('Rs.', '', $item['price']));
            $image = $item['image'];

            // Insert the order item into the orders table
            $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, price, image) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("❌ Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("isds", $userId, $name, $price, $image);
            if (!$stmt->execute()) {
                throw new Exception("❌ Failed to insert order item: " . $stmt->error);
            }
            $stmt->close(); // Good practice to close statement after each use
        }

        // Insert the total amount into the order_summary table
        $stmt = $conn->prepare("INSERT INTO order_summary (user_id, total_amount) VALUES (?, ?)");
        if ($stmt === false) {
            throw new Exception("❌ Prepare failed (summary): " . $conn->error);
        }

        $stmt->bind_param("id", $userId, $total);
        if (!$stmt->execute()) {
            throw new Exception("❌ Failed to save order summary: " . $stmt->error);
        }
        $stmt->close();

        // Commit the transaction if all inserts are successful
        mysqli_commit($conn);

        $_SESSION['message'] = "✅ Order placed successfully!";
        header("Location: ../delivery.php?total=Rs.$total");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($conn);
        $_SESSION['message'] = $e->getMessage();
        header("Location: ../cart.html"); 
        exit();
    }
}

mysqli_close($conn);
header("Location: ../cart.html"); 
exit();
?>
