<?php
// Enable error reporting (optional for development)
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

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
    header("Location: ../cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $items = json_decode($_POST['checkout_items'], true);
    $total = $_POST['checkout_total'];

    if (!$userId || empty($items)) {
        $_SESSION['message'] = "❌ Invalid checkout request!";
        header("Location: ../cart.php");
        exit();
    }

    $success = true;

    foreach ($items as $item) {
        $name = $item['name'];
        $price = floatval(str_replace('Rs.', '', $item['price']));
        $image = $item['image'];

        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $userId, $name, $price, $image);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }

    if ($success) {
        $stmt = $conn->prepare("INSERT INTO order_summary (user_id, total_amount) VALUES (?, ?)");
        $stmt->bind_param("id", $userId, $total);
        if ($stmt->execute()) {
            $_SESSION['message'] = "✅ Order placed successfully!";
            mysqli_close($conn);
            header("Location: ../delivery.php?total=Rs.$total");
            exit();
        } else {
            $_SESSION['message'] = "❌ Failed to save order summary: " . $stmt->error;
        }
    } else {
        $_SESSION['message'] = "❌ Failed to process order items.";
    }
}

mysqli_close($conn);
header("Location: ../cart.php");
exit();
?>