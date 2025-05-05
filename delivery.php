<?php
session_start();
$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // clear the message after displaying
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart - Delivery Details</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/delivery.css" rel="stylesheet" />

</head>

<body>
    <br>
    <br>
    <div class="container">
        <h2 class="mt-4">Delivery Details</h2>
        <form id="deliveryForm" action="controller/deliveryDetails.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" class="form-control" id="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" class="form-control" id="address" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" id="phone" required>
            </div>

            <input type="hidden" id="totalInput" name="total">
            <h3>Total: <span id="totalPrice"></span></h3>


            <button type="submit" class="btn btn-submit mt-3">Confirm Order</button>
            <button type="back" class="btn btn-back mt-3" type="button"
                onclick="window.location.href='cart.html';">Back</button>
            <br>
            <br>
        </form>

    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const totalElement = document.getElementById("totalPrice");
            const totalInput = document.getElementById("totalInput");
            const deliveryForm = document.getElementById("deliveryForm");

            // Get parameters from URL
            const urlParams = new URLSearchParams(window.location.search);
            const price = urlParams.get("price") || "0.00";
            const action = urlParams.get("action");

            // Format the price
            let total = "0.00";
            if (action === "buynow") {
                // For Buy Now, use the single product price
                total = parseFloat(price).toFixed(2);
            } else {
                // For cart checkout, use the passed total
                total = urlParams.get("total") || "0.00";
                // Remove "Rs." if present
                total = total.toString().replace(/rs\.?/i, '').trim();
                total = parseFloat(total).toFixed(2);
            }

            // Format as currency
            const formattedTotal = "Rs. " + total;

            // Display and store in hidden input
            totalElement.textContent = formattedTotal;
            totalInput.value = formattedTotal;

            // Form validation
            deliveryForm.addEventListener("submit", function (e) {
                if (!document.getElementById("name").value.trim() ||
                    !document.getElementById("address").value.trim() ||
                    !document.getElementById("phone").value.trim()) {
                    e.preventDefault();
                    alert("Please fill in all fields.");
                }
            });
        });

        // Display PHP messages if any
        const messageFromPHP = <?= json_encode($message) ?>;
        if (messageFromPHP) {
            alert(messageFromPHP);
        }
    </script>

   

</body>

</html>