<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart - Shop for you</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/shopStyle.css">
    <link rel="stylesheet" href="css/shop.css">
    <link rel="stylesheet" href="footer.css">
</head>

<style>
    .body{
    background: linear-gradient(to right, #e2e2e2, #d5ffdd);
  }
</style>
<body>

<?php
$server = "localhost";
$username = "root";
$password = "Himasha@1218";
$db = "growsmart";

$conn = mysqli_connect($server, $username, $password, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

// Fetch all results into an array
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Shop</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a href="home new.html" class="btn search-button" id="nav-button">Home</a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <form class="d-flex" id="searchForm">
                        <input id="searchInput" class="form-control me-2" type="search" placeholder="Search products..."
                            aria-label="Search" style="max-width:1000px;">
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

        <a href="seller.php" class="btn search-button" id="nav-button">Add Products</a>
    </div>
</nav>

<header class="hero-section"></header>

<div class="body">

<br>

<div class="container">
    <?php
    $categories = ['plants', 'vegetables', 'fruits', 'fertilizers'];
    foreach ($categories as $category) {
        echo '<div class="section-header" data-category="'.$category.'">';
        echo '<h2 style="text-align: center;">'.ucfirst($category).'</h2>';
        echo '</div><br>';
        echo '<div class="row" id="productList'.ucfirst($category).'">';

        foreach ($products as $row) {
            if (strtolower($row['category']) === $category) {
                $modalId = strtolower($row['productname']) . "Modal";
                ?>
                <div class="col-md-3 product-card" data-category="<?= $row['category'] ?>" data-name="<?= strtolower($row['productname']) ?>">
                    <div class="card">
                        <img src="<?= $row['imageurl'] ?>" class="card-img-top" alt="<?= $row['productname'] ?>" data-bs-toggle="modal"
                             data-bs-target="#<?= $modalId ?>">
                        <div class="card-body">
                            <div class="card-body-user">
                                <img src="Images/u1.jpeg" alt="User Image" class="user-img">
                                <button class="user-name">John Smith</button>
                            </div>
                            <h5 class="card-title"><?= $row['productname'] ?></h5>
                            <p><?= $row['weight'] ?></p>
                            <p class="card-text">Price: Rs.<?= $row['price'] ?></p>
                            <button type="button" class="btn add-to-cart cart-button" data-product-id="<?= $modalId ?>">Add to Cart</button>
                            <button type="button" class="btn buy-now cart-button">Buy Now</button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="<?= $modalId ?>Label"><?= $row['productname'] ?> Details</h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <img src="<?= $row['imageurl'] ?>" class="img-fluid" alt="<?= $row['productname'] ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Seller:</strong> John Smith</p>
                                        <p><strong>Seller Address:</strong> No.205, Galle Rd, Galle.</p>
                                        <p><strong>Rating:</strong> ⭐⭐⭐⭐ (4.5)</p>
                                        <p><strong>Pieces:</strong> <?= $row['weight'] ?></p>
                                        <p><strong>Price:</strong> Rs.<?= $row['price'] ?></p>
                                        <p><strong>Shipping:</strong> Free</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn add-to-cart" data-product-id="<?= $modalId ?>" data-bs-dismiss="modal">Add to Cart</button>
                                <button type="button" class="btn btn-success">Buy Now</button>
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

<!-- Footer -->
    <!--footer section starts-->
    <section class="footer">
        
    
    </section>
    <!--footer section ends-->

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->

    <script src="js/shopScript.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
