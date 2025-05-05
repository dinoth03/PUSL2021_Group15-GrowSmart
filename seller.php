<?php
session_start();

// Database connection
$server = "localhost";
$username = "root";
$password = "";
$db = "growsmartDB";

// Connect to MySQL
$conn = mysqli_connect($server, $username, $password, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ===================== PRODUCT MANAGEMENT =====================

// Get products for the table
$sql = "SELECT * FROM products ORDER BY itemid DESC";
$result = mysqli_query($conn, $sql);

// Initialize edit data
$editData = isset($_SESSION['edit_product']) ? $_SESSION['edit_product'] : null;

// Get message if any
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);

// Handle product delete
if (isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    
    // Get the image URL before deleting
    $getImageQuery = "SELECT imageurl FROM products WHERE itemid = ?";
    $stmt = mysqli_prepare($conn, $getImageQuery);
    mysqli_stmt_bind_param($stmt, "i", $deleteId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $imageToDelete);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    // Delete the product
    $deleteQuery = "DELETE FROM products WHERE itemid = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $deleteId);
    
    if (mysqli_stmt_execute($stmt)) {
        // Delete the image file if it exists
        if (!empty($imageToDelete) && file_exists($imageToDelete)) {
            unlink($imageToDelete);
        }
        $_SESSION['message'] = "Product deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting product: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    
    // Redirect to prevent resubmission
    header("Location: seller.php");
    exit();
}

// Handle product edit
if (isset($_POST['edit_id'])) {
    $editId = (int)$_POST['edit_id'];
    
    // Get product data
    $query = "SELECT * FROM products WHERE itemid = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $editData = $row;
        $_SESSION['edit_product'] = $editData;
    }
    
    mysqli_stmt_close($stmt);
    
    // Redirect to prevent resubmission
    header("Location: seller.php");
    exit();
}

// ===================== OFFER MANAGEMENT =====================

// Get offers for the table
$offersQuery = "SELECT * FROM offers ORDER BY id DESC";
$offersResult = mysqli_query($conn, $offersQuery);

// Initialize offer edit data
$editOfferData = isset($_SESSION['edit_offer']) ? $_SESSION['edit_offer'] : null;

// Handle offer delete
if (isset($_POST['delete_offer_id'])) {
    $deleteId = (int)$_POST['delete_offer_id'];
    
    // Get the image URL before deleting
    $getImageQuery = "SELECT image_url FROM offers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $getImageQuery);
    mysqli_stmt_bind_param($stmt, "i", $deleteId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $imageToDelete);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    // Delete the offer
    $deleteQuery = "DELETE FROM offers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $deleteId);
    
    if (mysqli_stmt_execute($stmt)) {
        // Delete the image file if it exists
        if (!empty($imageToDelete) && file_exists($imageToDelete)) {
            unlink($imageToDelete);
        }
        $_SESSION['message'] = "Offer deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting offer: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    
    // Redirect to prevent resubmission
    header("Location: seller.php");
    exit();
}

// Handle offer edit
if (isset($_POST['edit_offer_id'])) {
    $editId = (int)$_POST['edit_offer_id'];
    
    // Get offer data
    $query = "SELECT * FROM offers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $editResult = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($editResult)) {
        $editOfferData = $row;
        $_SESSION['edit_offer'] = $editOfferData;
    }
    
    mysqli_stmt_close($stmt);
    
    // Redirect to prevent resubmission
    header("Location: seller.php");
    exit();
}

// Create offers table if it doesn't exist
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'offers'");
if (mysqli_num_rows($tableCheck) == 0) {
    // Create offers table
    $createTable = "CREATE TABLE offers (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        product_name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        original_price DECIMAL(10,2) NOT NULL,
        discount_percentage INT NOT NULL,
        offer_price DECIMAL(10,2) NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $createTable)) {
        $_SESSION['message'] = "Offers table created successfully!";
    } else {
        $_SESSION['message'] = "Error creating offers table: " . mysqli_error($conn);
    }
}

// ===================== FORM PROCESSING =====================

// Check if product form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitType']) && $_POST['submitType'] == 'product') {
    // Get form data
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $productCategory = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $productPrice = (float) $_POST['productPrice'];
    $productWeight = mysqli_real_escape_string($conn, $_POST['productWeight']);
    $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription'] ?? '');
    $productType = $_POST['productType'];
    $isOffer = ($productType == 'offer') ? 1 : 0;
    $discountPercentage = $isOffer ? (int) $_POST['discountPercentage'] : 0;
    $editMode = isset($_POST['product_id']);
    $productId = $editMode ? (int) $_POST['product_id'] : 0;
    
    // Handle image upload
    $targetDir = "uploads/products/"; // Changed path to be relative to current directory
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageUrl = "";
    $uploadOk = 1;
    
    // Handle image upload if a file was provided
    if (isset($_FILES["productImage"]) && $_FILES["productImage"]["error"] == 0) {
        $fileName = basename($_FILES["productImage"]["name"]);
        $uniqueName = time() . '_' . $fileName;
        $targetFile = $targetDir . $uniqueName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["productImage"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['message'] = "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES["productImage"]["size"] > 5000000) {
            $_SESSION['message'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // If everything is ok, try to upload file
        if ($uploadOk) {
            if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
                $imageUrl = $targetFile; // Store the relative path
            } else {
                $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    } else {
        // If in edit mode and no new image, keep existing image
        if ($editMode && isset($_POST['currentImageUrl']) && !empty($_POST['currentImageUrl'])) {
            $imageUrl = $_POST['currentImageUrl'];
            $uploadOk = 1; // Continue with update even without new image
        } else if (!$editMode) {
            // For new products, image is required
            $_SESSION['message'] = "Please select an image for the product.";
            $uploadOk = 0;
        }
    }
    
    // Insert or update product data if upload was successful
    if ($uploadOk) {
        if ($editMode) {
            // Update existing product
            $sql = "UPDATE products SET 
                    productname = ?, 
                    category = ?, 
                    price = ?, 
                    weight = ?, 
                    imageurl = ?, 
                    description = ?,
                    is_offer = ?,
                    discount = ?
                    WHERE itemid = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdsssiii", $productName, $productCategory, $productPrice, $productWeight, $imageUrl, $productDescription, $isOffer, $discountPercentage, $productId);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['message'] = "Product updated successfully!";
                // Redirect to refresh the page
                header("Location: seller.php");
                exit();
            } else {
                $_SESSION['message'] = "Error updating product: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            // Insert new product
            $sql = "INSERT INTO products (productname, category, price, weight, imageurl, description, is_offer, discount) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdsssii", $productName, $productCategory, $productPrice, $productWeight, $imageUrl, $productDescription, $isOffer, $discountPercentage);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['message'] = "Product added successfully!";
                // Redirect to refresh the page
                header("Location: seller.php");
                exit();
            } else {
                $_SESSION['message'] = "Error adding product: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Check if offer form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitType']) && $_POST['submitType'] == 'offer') {
    // Get form data
    $productName = mysqli_real_escape_string($conn, $_POST['offerProductName']);
    $category = mysqli_real_escape_string($conn, $_POST['offerCategory']);
    $originalPrice = (float) $_POST['originalPrice'];
    $discountPercentage = (int) $_POST['offerDiscountPercentage'];
    $offerPrice = (float) $_POST['offerPrice'];
    $isActive = (int) $_POST['offerStatus'];
    $editMode = isset($_POST['offer_id']) && !empty($_POST['offer_id']);
    $offerId = $editMode ? (int) $_POST['offer_id'] : 0;
    
    // Handle image upload
    $targetDir = "uploads/offers/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageUrl = "";
    $uploadOk = 1;
    
    // Handle image upload if a file was provided
    if (isset($_FILES["offerImage"]) && $_FILES["offerImage"]["error"] == 0) {
        $fileName = basename($_FILES["offerImage"]["name"]);
        $uniqueName = time() . '_' . $fileName;
        $targetFile = $targetDir . $uniqueName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["offerImage"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['message'] = "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES["offerImage"]["size"] > 5000000) {
            $_SESSION['message'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // If everything is ok, try to upload file
        if ($uploadOk) {
            if (move_uploaded_file($_FILES["offerImage"]["tmp_name"], $targetFile)) {
                $imageUrl = $targetFile; // Store the relative path
            } else {
                $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    } else {
        // If in edit mode and no new image, keep existing image
        if ($editMode && isset($_POST['currentOfferImageUrl']) && !empty($_POST['currentOfferImageUrl'])) {
            $imageUrl = $_POST['currentOfferImageUrl'];
            $uploadOk = 1; // Continue with update even without new image
        } else if (!$editMode) {
            // For new offers, image is required
            $_SESSION['message'] = "Please select an image for the offer.";
            $uploadOk = 0;
        }
    }
    
    // Validate discount calculation
    $calculatedPrice = round($originalPrice - ($originalPrice * $discountPercentage / 100), 2);
    if (abs($calculatedPrice - $offerPrice) > 0.01) {
        // If manually set a different price, recalculate the discount
        $actualDiscount = round(100 - (($offerPrice / $originalPrice) * 100), 0);
        $discountPercentage = $actualDiscount;
    }
    
    // Insert or update offer data if upload was successful
    if ($uploadOk) {
        if ($editMode) {
            // Update existing offer
            $sql = "UPDATE offers SET 
                    product_name = ?, 
                    category = ?, 
                    original_price = ?, 
                    discount_percentage = ?, 
                    offer_price = ?, 
                    image_url = ?, 
                    is_active = ?
                    WHERE id = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdiisii", $productName, $category, $originalPrice, $discountPercentage, $offerPrice, $imageUrl, $isActive, $offerId);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['message'] = "Offer updated successfully!";
                // Redirect to refresh the page
                header("Location: seller.php");
                exit();
            } else {
                $_SESSION['message'] = "Error updating offer: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            // Insert new offer
            $sql = "INSERT INTO offers (product_name, category, original_price, discount_percentage, offer_price, image_url, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdissi", $productName, $category, $originalPrice, $discountPercentage, $offerPrice, $imageUrl, $isActive);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['message'] = "Offer added successfully!";
                // Redirect to refresh the page
                header("Location: seller.php");
                exit();
            } else {
                $_SESSION['message'] = "Error adding offer: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart - Seller Panel</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .btn-submit {
            background: #d5f3d5;
            border: 0.1rem solid #00ff00;
        }

        .btn-submit:hover {
            background: #00ff00;
            color: white;
        }

        .btn-back {
            background-color: rgb(220, 233, 247);
            border: 0.1rem solid dodgerblue;
        }

        .btn-back:hover {
            background-color: dodgerblue;
            color: white;
        }

        .container {
            background-color: rgb(252, 252, 250);
            border: 5px solid #00ff00;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        body {
            background: linear-gradient(to right, #e2e2e2, #d5ffdd);
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .btn-warning {
            background-color: rgb(239, 247, 128);
            border: 0.1rem solid #e3eb52;
        }

        .btn-warning:hover {
            background-color: #e3eb52;
            color: white;
        }

        .btn-danger {
            color: black;
            background-color: rgb(241, 137, 119);
            border: 0.1rem solid #f24949;
        }

        .btn-danger:hover {
            background-color: #f24949;
            color: white;
        }
        
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #00ff00;
            padding-bottom: 10px;
        }
        
        .product-type-selector {
            border: 2px solid #00ff00;
            border-radius: 5px;
            padding: 8px 15px;
            background-color: #f8fff8;
            margin-bottom: 15px;
        }
        
        .product-type-selector label {
            margin-right: 15px;
            cursor: pointer;
        }
        
        .product-type-selector input[type="radio"] {
            margin-right: 5px;
        }
        
        .badge-offer {
            background-color: #ff9900;
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
        }
        
        .badge-shop {
            background-color: #00aa00;
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
        }
        
        .image-preview {
            width: 100%;
            height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
        }
        
        .discount-field {
            display: none;
        }
        
        .header-page-title {
            font-size: 28px;
            color: #2c8c2c;
            font-weight: bold;
        }

        .section-divider {
            margin: 3rem 0 1.5rem;
            border-bottom: 2px solid #00ff00;
            padding-bottom: 0.5rem;
        }

        .section-divider h3 {
            color: #2c8c2c;
            font-weight: bold;
        }

        .nav-tabs {
            border-bottom: 2px solid #00ff00;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            color: #2c8c2c;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: #00ff00;
            background-color: transparent;
            border-bottom: 3px solid #00ff00;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent transparent #00ff00 transparent;
        }

        .tab-content {
            padding-top: 20px;
        }

        .status-active {
            background-color: #00aa00;
            color: white;
        }

        .status-inactive {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navigation button to return to shop -->
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col">
                <a href="shop.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Shop
                </a>
                <a href="home new.html" class="btn btn-back ms-2">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="header-bar">
            <h2 class="header-page-title">Seller Dashboard</h2>
            <div>
                <span class="text-muted">Welcome, <?= $_SESSION['username'] ?? 'Seller' ?></span>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="sellerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                    <i class="fas fa-box"></i> Products
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="offers-tab" data-bs-toggle="tab" data-bs-target="#offers" type="button" role="tab">
                    <i class="fas fa-tags"></i> Special Offers
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="sellerTabsContent">
            <!-- Products Tab -->
            <div class="tab-pane fade show active" id="products" role="tabpanel">
                <!-- Add/Edit Product Form -->
                <form id="addProductForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="submitType" value="product">
                    <?php if ($editData): ?>
                        <input type="hidden" name="product_id" value="<?= $editData['itemid'] ?>">
                    <?php endif; ?>

                    <!-- Product Type Selection -->
                    <div class="product-type-selector">
                        <label>
                            <input type="radio" name="productType" value="shop" <?= ($editData && isset($editData['is_offer']) && $editData['is_offer'] == 0) || !$editData ? 'checked' : '' ?>>
                            Regular Product (Shop)
                        </label>
                        <label>
                            <input type="radio" name="productType" value="offer" <?= ($editData && isset($editData['is_offer']) && $editData['is_offer'] == 1) ? 'checked' : '' ?>>
                            Special Offer
                        </label>
                    </div>

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
                                $categories = ['Vegetables', 'Plants', 'Fruits', 'Fertilizers', 'Indoor Plants', 'Outdoor Plants', 'Seeds', 'Tools'];
                                foreach ($categories as $cat) {
                                    $selected = ($editData && isset($editData['category']) && strtolower($editData['category']) == strtolower($cat)) ? 'selected' : '';
                                    echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="productPrice">Price (Rs.)</label>
                            <input type="number" name="productPrice" id="productPrice" class="form-control" required
                                value="<?= $editData['price'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="productWeight">Pieces/Weight</label>
                            <input type="text" name="productWeight" id="productWeight" class="form-control" required
                                value="<?= $editData['weight'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 discount-field">
                            <label for="discountPercentage">Discount Percentage (%)</label>
                            <input type="number" name="discountPercentage" id="discountPercentage" class="form-control" min="1" max="99"
                                value="<?= $editData['discount'] ?? '10' ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="productImage">Product Image</label>
                            <input type="file" name="productImage" id="productImage" class="form-control" <?= $editData ? '' : 'required' ?>>
                            <div class="image-preview" id="imagePreview">
                                <?php if ($editData && !empty($editData['imageurl'])): ?>
                                    <img src="<?= $editData['imageurl'] ?>" alt="Product Image" id="currentImage">
                                <?php else: ?>
                                    <span>Image Preview</span>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="currentImageUrl" value="<?= $editData['imageurl'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="productDescription">Product Description</label>
                            <textarea name="productDescription" id="productDescription" class="form-control" rows="3"><?= $editData['description'] ?? '' ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit mt-3"><?= $editData ? 'Update Product' : 'Add Product' ?></button>
                    <button type="reset" class="btn btn-danger mt-3">Reset Form</button>
                </form>

                <!-- Product Table -->
                <h3 class="mt-5">Manage Products</h3>
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price (Rs.)</th>
                            <th>Pieces/Weight</th>
                            <th>Type</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['productname']) ?></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td><?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['weight']) ?></td>
                                    <td>
                                        <?php if (isset($row['is_offer']) && $row['is_offer'] == 1): ?>
                                            <span class="badge-offer">Offer</span>
                                            <?php if (isset($row['discount'])): ?>
                                                <small>(<?= $row['discount'] ?>% off)</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge-shop">Shop</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['imageurl'])): ?>
                                            <img src="<?= htmlspecialchars($row['imageurl']) ?>" alt="Product Image" width="50">
                                        <?php else: ?>
                                            <span>No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Edit button -->
                                        <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="editProduct(<?= $row['itemid'] ?>)">Edit</button>
                                        <!-- Delete button -->
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteProduct(<?= $row['itemid'] ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Offers Tab -->
            <div class="tab-pane fade" id="offers" role="tabpanel">
                <!-- Add/Edit Offer Form -->
                <form id="addOfferForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="submitType" value="offer">
                    <?php if ($editOfferData): ?>
                        <input type="hidden" name="offer_id" value="<?= $editOfferData['id'] ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="offerProductName">Product Name</label>
                            <input type="text" name="offerProductName" id="offerProductName" class="form-control" required
                                value="<?= $editOfferData['product_name'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="offerCategory">Category</label>
                            <select id="offerCategory" name="offerCategory" class="form-control" required>
                                <option value="" disabled <?= !$editOfferData ? 'selected' : '' ?>>Choose Category</option>
                                <?php
                                $categories = ['Vegetables', 'Plants', 'Fruits', 'Fertilizers', 'Indoor Plants', 'Outdoor Plants', 'Seeds', 'Tools'];
                                foreach ($categories as $cat) {
                                    $selected = ($editOfferData && isset($editOfferData['category']) && strtolower($editOfferData['category']) == strtolower($cat)) ? 'selected' : '';
                                    echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="originalPrice">Original Price (Rs.)</label>
                            <input type="number" name="originalPrice" id="originalPrice" class="form-control" step="0.01" min="1" required
                                value="<?= $editOfferData['original_price'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="offerDiscountPercentage">Discount Percentage (%)</label>
                            <input type="number" name="offerDiscountPercentage" id="offerDiscountPercentage" class="form-control" min="1" max="99" required
                                value="<?= $editOfferData['discount_percentage'] ?? '10' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="offerPrice">Offer Price (Rs.)</label>
                            <input type="number" name="offerPrice" id="offerPrice" class="form-control" step="0.01" min="0" required
                                value="<?= $editOfferData['offer_price'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="offerStatus">Status</label>
                            <select id="offerStatus" name="offerStatus" class="form-control" required>
                                <option value="1" <?= ($editOfferData && isset($editOfferData['is_active']) && $editOfferData['is_active'] == 1) || !$editOfferData ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= ($editOfferData && isset($editOfferData['is_active']) && $editOfferData['is_active'] == 0) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="offerImage">Offer Image</label>
                            <input type="file" name="offerImage" id="offerImage" class="form-control" <?= $editOfferData ? '' : 'required' ?>>
                            <div class="image-preview" id="offerImagePreview">
                                <?php if ($editOfferData && !empty($editOfferData['image_url'])): ?>
                                    <img src="<?= $editOfferData['image_url'] ?>" alt="Offer Image" id="currentOfferImage">
                                <?php else: ?>
                                    <span>Image Preview</span>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="currentOfferImageUrl" value="<?= $editOfferData['image_url'] ?? '' ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit mt-3"><?= $editOfferData ? 'Update Offer' : 'Add Offer' ?></button>
                    <button type="reset" class="btn btn-danger mt-3">Reset Form</button>
                </form>

                <!-- Offers Table -->
                <h3 class="mt-5">Manage Offers</h3>
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Original Price</th>
                            <th>Discount</th>
                            <th>Offer Price</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($offersResult && mysqli_num_rows($offersResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($offersResult)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td>Rs. <?= htmlspecialchars($row['original_price']) ?></td>
                                    <td><?= htmlspecialchars($row['discount_percentage']) ?>%</td>
                                    <td>Rs. <?= htmlspecialchars($row['offer_price']) ?></td>
                                    <td>
                                        <span class="badge <?= $row['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                            <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Offer Image" width="50">
                                        <?php else: ?>
                                            <span>No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Edit button -->
                                        <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="editOffer(<?= $row['id'] ?>)">Edit</button>
                                        <!-- Delete button -->
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteOffer(<?= $row['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No offers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Display message if any
            const messageFromPHP = <?= json_encode($message) ?>;
            if (messageFromPHP) {
                alert(messageFromPHP);
            }
            
            // Toggle discount field based on product type
            const productTypeRadios = document.querySelectorAll('input[name="productType"]');
            const discountField = document.querySelector('.discount-field');
            
            function toggleDiscountField() {
                const isOffer = document.querySelector('input[name="productType"]:checked').value === 'offer';
                discountField.style.display = isOffer ? 'block' : 'none';
                
                // Make discount field required only for offers
                document.getElementById('discountPercentage').required = isOffer;
            }
            function toggleDiscountField() {
    const isOffer = document.querySelector('input[name="productType"]:checked').value === 'offer';
    discountField.style.display = isOffer ? 'block' : 'none';
    
    // Make discount field required only for offers
    document.getElementById('discountPercentage').required = isOffer;
    
    // NEW LINE: Also disable the field when not needed
    document.getElementById('discountPercentage').disabled = !isOffer;
}
            // Initial check
            toggleDiscountField();
            
            // Listen for changes
            productTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleDiscountField);
            });
            
            // Product Image preview functionality
            const imageInput = document.getElementById('productImage');
            const imagePreview = document.getElementById('imagePreview');
            
            imageInput.addEventListener('change', function() {
                while (imagePreview.firstChild) {
                    imagePreview.removeChild(imagePreview.firstChild);
                }
                
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Product Preview';
                        imagePreview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(file);
                } else {
                    const noImage = document.createElement('span');
                    noImage.textContent = 'Image Preview';
                    imagePreview.appendChild(noImage);
                }
            });
            
            // Offer Image preview functionality
            const offerImageInput = document.getElementById('offerImage');
            const offerImagePreview = document.getElementById('offerImagePreview');
            
            offerImageInput.addEventListener('change', function() {
                while (offerImagePreview.firstChild) {
                    offerImagePreview.removeChild(offerImagePreview.firstChild);
                }
                
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Offer Preview';
                        offerImagePreview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(file);
                } else {
                    const noImage = document.createElement('span');
                    noImage.textContent = 'Image Preview';
                    offerImagePreview.appendChild(noImage);
                }
            });
            
            // Automatic offer price calculation
            const originalPriceInput = document.getElementById('originalPrice');
            const discountPercentageInput = document.getElementById('offerDiscountPercentage');
            const offerPriceInput = document.getElementById('offerPrice');
            
            function calculateOfferPrice() {
                const originalPrice = parseFloat(originalPriceInput.value) || 0;
                const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
                
                if (originalPrice > 0 && discountPercentage > 0) {
                    const offerPrice = originalPrice - (originalPrice * discountPercentage / 100);
                    offerPriceInput.value = offerPrice.toFixed(2);
                }
            }
            
            // Calculate on input change
            originalPriceInput.addEventListener('input', calculateOfferPrice);
            discountPercentageInput.addEventListener('input', calculateOfferPrice);
            
            // Check if we need to show the offers tab
            <?php if ($editOfferData): ?>
                document.getElementById('offers-tab').click();
            <?php endif; ?>
        });
        
        // Functions for product actions
        function editProduct(id) {
            // Create a form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'edit_id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                // Create a form to submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_id';
                input.value = id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Functions for offer actions
        function editOffer(id) {
            // Create a form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'edit_offer_id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        function deleteOffer(id) {
            if (confirm('Are you sure you want to delete this offer?')) {
                // Create a form to submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_offer_id';
                input.value = id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

</body>

</html>
<?php 
// Clean up session variables
unset($_SESSION['edit_product']);
unset($_SESSION['edit_offer']); 

// Close the database connection
mysqli_close($conn);
?>