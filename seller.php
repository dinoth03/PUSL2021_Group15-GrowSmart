<?php
// Start the session and get session data
session_start();
//$_SESSION['user_id'] = 1;  //checking using this

$message = $_SESSION['message'] ?? '';
$editData = $_SESSION['edit_product'] ?? null;
unset($_SESSION['message']);

// Check if user is logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    die("Unauthorized access. Please <a href='login.html'>log in</a>.");
}
?>

<?php
// Database connection
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
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch products only added by the logged-in user
$result = mysqli_query($conn, "SELECT * FROM products WHERE user_id = '$userId'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart - Seller Panel</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/seller.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="header-4">
            <h2 class="mt-4"><?= $editData ? 'Edit Product' : 'Seller Panel' ?></h2>
        </div>

        <!-- Add/Edit Product Form -->
        <form id="addProductForm"
              action="<?= $editData ? 'controller/updateProducts.php' : 'controller/addProducts.php' ?>"
              method="post">

            <?php if ($editData): ?>
                <input type="hidden" name="product_id" value="<?= $editData['itemid'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <label for="productName">Product Name</label>
                    <input type="text" name="productName" id="productName" class="form-control" required
                           value="<?= $editData['productname'] ?? '' ?>">
                </div>
                <div class="col-md-6">
                    <label for="productCategory">Category</label>
                    <select id="productCategory" name="productCategory" class="form-control" required>
                        <option value="" disabled <?= !$editData ? 'selected' : '' ?>>Choose Category</option>
                        <?php
                        $categories = ['vegetables', 'plants', 'fruits', 'fertilizers'];
                        foreach ($categories as $cat) {
                            $selected = ($editData && $editData['category'] == $cat) ? 'selected' : '';
                            echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="productPrice">Price (Rs.)</label>
                    <input type="number" name="productPrice" id="productPrice" class="form-control" required
                           value="<?= $editData['price'] ?? '' ?>">
                </div>
                <div class="col-md-6">
                    <label for="productWeight">Pieces</label>
                    <input type="text" name="productWeight" id="productWeight" class="form-control" required
                           value="<?= $editData['weight'] ?? '' ?>">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label for="imageUrl">Image URL</label>
                    <input type="text" name="imageUrl" id="imageUrl" class="form-control" required
                           value="<?= $editData['imageurl'] ?? '' ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-submit mt-3">
                <?= $editData ? 'Update Product' : 'Add Product' ?>
            </button>
            <button type="button" class="btn btn-back mt-3" onclick="window.location.href='shop.php';">Back</button>
        </form>

        <div class="manage_table mt-5">
            <h3>Manage My Products</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price (Rs.)</th>
                            <th>Pieces</th>
                            <th>Image URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['productname']) ?></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td><?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['weight']) ?></td>
                                    <td><?= htmlspecialchars($row['imageurl']) ?></td>
                                    <td>
                                        <form method="post" action="controller/editProducts.php" style="display:inline-block;">
                                            <input type="hidden" name="product_id" value="<?= $row['itemid'] ?>">
                                            <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                                        </form>
                                        <form method="post" action="controller/deleteProducts.php" style="display:inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            <input type="hidden" name="product_id" value="<?= $row['itemid'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Alert -->
    <script>
        const messageFromPHP = <?= json_encode($message) ?>;
        if (messageFromPHP) {
            alert(messageFromPHP);
        }
    </script>

</body>
</html>

<?php
unset($_SESSION['edit_product']);
mysqli_close($conn);
?>
