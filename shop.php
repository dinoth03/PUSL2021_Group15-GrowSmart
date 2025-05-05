<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart - Shop for you</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Your custom styles -->
    <link rel="stylesheet" href="css/shopStyle.css">
    <link rel="stylesheet" href="css/shop.css">
    <style>
        .body {
            background: linear-gradient(to right, #e2e2e2, #d5ffdd);
        }
    </style>
</head>

<body>

<?php
$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

$conn = mysqli_connect($server, $username, $password, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$products = [];
$result = mysqli_query($conn, "SELECT * FROM products");
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a href="home new.html" class="btn search-button" id="nav-button">Home</a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <form class="d-flex" id="searchForm">
                        <input id="searchInput" class="form-control me-2" type="search"
                               placeholder="Search products..." aria-label="Search" style="max-width:1000px;">
                        <button class="btn search-button" type="submit" id="nav-button">Search</button>
                    </form>
                </li>
                <li class="nav-item dropdown">
                    <button class="btn search-button dropdown-toggle" type="button" id="categoryDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Sort
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li><button class="dropdown-item filter-btn" data-category="all">All</button></li>
                        <li><button class="dropdown-item filter-btn" data-category="plants">Plants</button></li>
                        <li><button class="dropdown-item filter-btn" data-category="vegetables">Vegetables</button></li>
                        <li><button class="dropdown-item filter-btn" data-category="fruits">Fruits</button></li>
                        <li><button class="dropdown-item filter-btn" data-category="fertilizers">Fertilizers</button></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="cart.html" class="btn btn-outline-primary" id="nav-button">
                        <i class="fa fa-shopping-cart" style="font-size: 24px;"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Change this line -->
<a href="seller.php" id="addProductBtn" class="btn search-button">Add Products</a>

    </div>
</nav>

<header class="hero-section"></header>

<div class="body">
    <br>
    <div class="container">
        <?php
        $categories = ['plants', 'vegetables', 'fruits', 'fertilizers'];
        foreach ($categories as $category) {
            echo '<div class="section-header" data-category="' . $category . '">';
            echo '<h2 style="text-align: center;">' . ucfirst($category) . '</h2>';
            echo '</div><br>';
            echo '<div class="row" id="productList' . ucfirst($category) . '">';

            foreach ($products as $row) {
                if (strtolower($row['category']) === $category) {
                    $modalId = 'modal_' . $row['itemid']; // Assuming 'id' is a unique field in your DB
                    
                            // Hardcoded descriptions by category
        $categoryDescriptions = [
            'plants' => 'Fresh and healthy plants to brighten your space.',
            'fruits' => 'Sweet and juicy fruits grown with care.',
            'vegetables' => 'Fresh vegetables harvested from local farms.',
            'fertilizers' => 'High-quality fertilizers for healthy plant growth.'
        ];

        // Fallback description
        $descriptionText = $categoryDescriptions[strtolower($row['category'])] ?? 'Product description not available.';

                    ?>
                    <div class="col-md-3 product-card" data-category="<?= $row['category'] ?>" data-name="<?= $row['productname'] ?>">
                        <div class="card">
                            <div class="image-card">
                                <img src="<?= $row['imageurl'] ?>" class="card-img-top" alt="<?= $row['productname'] ?>"
                                     data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['productname'] ?></h5>
                                <p><strong>Pieces:</strong> <?= $row['weight'] ?></p>
                                <p class="card-text"><strong>Price:</strong> Rs.<?= $row['price'] ?></p>

                                <!-- Add to Cart and Buy Now buttons -->
                                <button type="button" class="btn add-to-cart cart-button" data-product-id="<?= $modalId ?>" 
                                        data-loggedin="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">Add to Cart</button>
                                <a href="delivery.php?price=<?= $row['price'] ?>&name=<?= urlencode($row['productname']) ?>&action=buynow" 
                                   class="btn buy-now" data-loggedin="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">Buy Now</a>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="<?= $modalId ?>Label"><?= $row['productname'] ?> Details</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <img src="<?= $row['imageurl'] ?>" class="img-fluid" alt="<?= $row['productname'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            
                                            <p><strong>Pieces:</strong> <?= $row['weight'] ?></p>
                                            <p><strong>Price:</strong> Rs.<?= $row['price'] ?></p>
                                            <p><strong>Description:</strong> <?= $descriptionText ?></p>
                                            <p><strong>Shipping:</strong> Free</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <!-- Add to Cart and Buy Now buttons in modal -->
                                    <button type="button" class="btn add-to-cart" data-bs-dismiss="modal"
                                            data-name="<?= htmlspecialchars($row['productname']) ?>" 
                                            data-price="<?= $row['price'] ?>" data-image="<?= $row['imageurl'] ?>"
                                            data-loggedin="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
                                        Add to Cart
                                    </button>
                                    <a href="delivery.php?price=<?= $row['price'] ?>&name=<?= urlencode($row['productname']) ?>&action=buynow" 
                                       class="btn buy-now cart-button" 
                                       data-loggedin="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            echo '</div><br>';
        }
        ?>
    </div>
</div>

<section class="footer" style="padding: 10px;">
    <div class="credit" style="text-align: center;">
        created by <span>© 2025 GrowSmart.</span> | all rights reserved
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/shopScript.js"></script>

</body>
</html>
