<!-- filepath: c:\xampp\htdocs\grow\GrowSmart_Web\admin panel\index.php -->
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

// Initialize variables
$editData = null;
$result = null;

// Handle delete request
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
        if (!empty($imageToDelete) && file_exists("../" . $imageToDelete)) {
            unlink("../" . $imageToDelete);
        }
        $_SESSION['message'] = "Product deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting product: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    
    // Redirect to prevent resubmission
    header("Location: index.php");
    exit();
}

// Handle edit request
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
}
// If no edit data in POST, check SESSION
else if (isset($_SESSION['edit_product'])) {
    $editData = $_SESSION['edit_product'];
}

// Get products for the table
$sql = "SELECT * FROM products ORDER BY itemid DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    $_SESSION['message'] = "Error fetching products: " . mysqli_error($conn);
    $result = []; // Initialize as empty array to prevent errors
}

// Get product stats
$totalCount = mysqli_num_rows($result);

// Count plants category
$plantsQuery = "SELECT COUNT(*) as plants_count FROM products WHERE category LIKE '%plant%' OR category = 'plants'";
$plantsResult = mysqli_query($conn, $plantsQuery);
$plantsRow = mysqli_fetch_assoc($plantsResult);
$plantsCount = $plantsRow['plants_count'];

// Calculate other categories
$othersCount = $totalCount - $plantsCount;

// Calculate percentages
$plantsPercentage = $totalCount > 0 ? round(($plantsCount / $totalCount) * 100) : 0;
$othersPercentage = $totalCount > 0 ? round(($othersCount / $totalCount) * 100) : 0;

// Get message if any
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['edit_id']) && !isset($_POST['delete_id'])) {
    // Get form data
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $productCategory = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $productPrice = (float) $_POST['productPrice'];
    $productWeight = mysqli_real_escape_string($conn, $_POST['productStock']);
    $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription'] ?? '');
    $productType = isset($_POST['productType']) ? $_POST['productType'] : 'shop';
    $isOffer = ($productType == 'offer') ? 1 : 0;
    $discountPercentage = $isOffer ? (int) $_POST['discountPercentage'] : 0;
    $editMode = isset($_POST['productId']) && !empty($_POST['productId']);
    $productId = $editMode ? (int) $_POST['productId'] : 0;
    
    // Handle image upload
    $targetDir = "../uploads/products/"; // Store images in parent directory
    
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
                $imageUrl = "uploads/products/" . $uniqueName; // Relative path from root
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
                header("Location: index.php");
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
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['message'] = "Error adding product: " . mysqli_error($conn);
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
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GrowSmart - Admin Panel </title>
  <link rel="icon" type="image/png" href="../Img/TitleLogo.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
  <link rel="stylesheet" href="style.css">
  <style>
    /* Additional styles for product management */
    .product-list {
      margin-top: 2rem;
    }
    
    .product-list table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .product-list th {
      text-align: left;
      padding: 0.8rem;
      background: var(--color-light);
    }
    
    .product-list td {
      padding: 0.8rem;
      border-bottom: 1px solid var(--color-light);
    }
    
    .product-list tr:hover {
      background-color: var(--color-light);
    }
    
    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }
    
    .edit-btn, .delete-btn {
      border: none;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      cursor: pointer;
      display: flex;
      align-items: center;
    }
    
    .edit-btn {
      background: var(--color-warning);
      color: white;
    }
    
    .delete-btn {
      background: var(--color-danger);
      color: white;
    }
    
    .product-form {
      background: var(--color-white);
      padding: 1.5rem;
      border-radius: var(--card-border-radius);
      box-shadow: var(--box-shadow);
      margin-top: 1rem;
      display: <?= $editData ? 'block' : 'none' ?>;
    }
    
    .product-form.active {
      display: block;
    }
    
    .product-form h2 {
      margin-bottom: 1rem;
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }
    
    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid var(--color-light);
      border-radius: 0.3rem;
    }
    
    .form-action {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
    }
    
    .btn {
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 0.3rem;
      cursor: pointer;
      font-weight: 500;
    }
    
    .btn-primary {
      background: var(--color-primary);
      color: white;
    }
    
    .btn-cancel {
      background: var(--color-light);
    }
    
    .product-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 4px;
    }
    
    .product-status {
      padding: 0.3rem 0.6rem;
      border-radius: 0.3rem;
      font-size: 0.8rem;
    }
    
    .status-active {
      background: var(--color-success-light);
      color: var(--color-success);
    }
    
    .status-inactive {
      background: var(--color-danger-light);
      color: var(--color-danger);
    }
    
    .filter-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    
    .search-box {
      padding: 0.5rem;
      border: 1px solid var(--color-light);
      border-radius: 0.3rem;
      width: 300px;
    }
    
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    
    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid var(--color-primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .alert {
      padding: 10px 15px;
      border-radius: 4px;
      margin-bottom: 15px;
    }
    
    .alert-success {
      background-color: var(--color-success-light);
      color: var(--color-success);
    }
    
    .alert-danger {
      background-color: var(--color-danger-light);
      color: var(--color-danger);
    }
    
    .image-preview {
      width: 100px;
      height: 100px;
      border: 1px solid #ddd;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
    }
    
    .image-preview img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    
    .product-type-selector {
      border: 2px solid var(--color-primary);
      border-radius: 5px;
      padding: 8px 15px;
      background-color: var(--color-white);
      margin-bottom: 15px;
    }
    
    .product-type-selector label {
      margin-right: 15px;
      cursor: pointer;
    }
    
    .product-type-selector input[type="radio"] {
      margin-right: 5px;
    }
    
    .discount-field {
      display: none;
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
    /* Add this to your existing style tag */
.section-divider {
  margin: 3rem 0 1.5rem;
  border-bottom: 2px solid var(--color-primary-light);
  padding-bottom: 0.5rem;
}

.section-divider h2 {
  color: var(--color-primary);
  font-size: 1.5rem;
}

.offer-form {
  background: var(--color-white);
  padding: 1.5rem;
  border-radius: var(--card-border-radius);
  box-shadow: var(--box-shadow);
  margin-top: 1rem;
  display: none;
}

.offer-list {
  margin-top: 1rem;
}

.offer-list table {
  width: 100%;
  border-collapse: collapse;
}

.offer-list th {
  text-align: left;
  padding: 0.8rem;
  background: var(--color-light);
}

.offer-list td {
  padding: 0.8rem;
  border-bottom: 1px solid var(--color-light);
}

.offer-status {
  padding: 0.3rem 0.6rem;
  border-radius: 0.3rem;
  font-size: 0.8rem;
}

.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-row .form-group {
  flex: 1;
}
  </style>
</head>
<body>
   <div class="container">
      <aside>
           
         <div class="top">
           <div class="logo">
             <h2>GROW<span class="danger">SMART</span> </h2>
           </div>
           <div class="close" id="close_btn">
            <span class="material-symbols-sharp">
              close
              </span>
           </div>
         </div>
         <!-- end top -->
          <div class="sidebar">

            <a href="#" class="active">
              <span class="material-symbols-sharp">grid_view </span>
              <h3>Dashboard</h3>
           </a>
           <a href="#">
              <span class="material-symbols-sharp">person_outline </span>
              <h3>Customers</h3>
           </a>
           <a href="#">
              <span class="material-symbols-sharp">inventory_2</span>
              <h3>Products</h3>
           </a>
           <a href="#">
              <span class="material-symbols-sharp">insights </span>
              <h3>Analytics</h3>
           </a>
           <a href="#">
              <span class="material-symbols-sharp">mail_outline </span>
              <h3>Messages</h3>
              <span class="msg_count">14</span>
           </a>
           <a href="#">
              <span class="material-symbols-sharp">receipt_long </span>
              <h3>Orders</h3>
           </a>
           <a href="#">
              <span class="material-symbols-sharp">report_gmailerrorred </span>
              <h3>Reports</h3>
           </a>
           <a href="#">
              <span class="material-symbols-sharp">settings </span>
              <h3>Settings</h3>
           </a>
           <a href="../home new.html">
              <span class="material-symbols-sharp">logout </span>
              <h3>Logout</h3>
           </a>
           <a href="offers.php">
  <span class="material-symbols-sharp">local_offer</span>
  <h3>Offers</h3>
</a>
          </div>

      </aside>
      <!-- End aside -->

      <!-- Start main part -->
      <main>
           <h1>Products Management</h1>

           <div class="date">
             <input type="date">
           </div>

        <div class="insights">
           <!-- Products Stats -->
            <div class="sales">
               <span class="material-symbols-sharp">inventory_2</span>
               <div class="middle">
                 <div class="left">
                   <h3>Total Products</h3>
                   <h1 id="totalProductsCount"><?= $totalCount ?></h1>
                 </div>
                  <div class="progress">
                      <svg>
                         <circle r="30" cy="40" cx="40"></circle>
                      </svg>
                      <div class="number"><p>100%</p></div>
                  </div>
               </div>
               <small>All Categories</small>
            </div>
           
            <!-- Plant Category -->
            <div class="expenses">
              <span class="material-symbols-sharp">yard</span>
              <div class="middle">
                <div class="left">
                  <h3>Plants</h3>
                  <h1 id="plantsCount"><?= $plantsCount ?></h1>
                </div>
                <div class="progress">
                    <svg>
                       <circle r="30" cy="40" cx="40"></circle>
                    </svg>
                    <div class="number"><p id="plantsPercentage"><?= $plantsPercentage ?>%</p></div>
                </div>
              </div>
              <small>Indoor & Outdoor Plants</small>
            </div>
           
            <!-- Other categories -->
            <div class="income">
              <span class="material-symbols-sharp">category</span>
              <div class="middle">
                <div class="left">
                  <h3>Other Categories</h3>
                  <h1 id="othersCount"><?= $othersCount ?></h1>
                </div>
                <div class="progress">
                    <svg>
                       <circle r="30" cy="40" cx="40"></circle>
                    </svg>
                    <div class="number"><p id="othersPercentage"><?= $othersPercentage ?>%</p></div>
                </div>
              </div>
              <small>Seeds, Tools & More</small>
            </div>
        </div>
       <!-- End insights -->
       
      <!-- Alert messages -->
      <?php if (!empty($message)): ?>
      <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>">
        <?= $message ?>
      </div>
      <?php endif; ?>
       
      <!-- Product Form -->
      <div class="product-form" id="productForm">
        <h2 id="formTitle"><?= $editData ? 'Edit Product' : 'Add New Product' ?></h2>
        <form id="addProductForm" method="post" enctype="multipart/form-data">
          <input type="hidden" id="productId" name="productId" value="<?= $editData['itemid'] ?? '' ?>">
          <input type="hidden" name="currentImageUrl" value="<?= $editData['imageurl'] ?? '' ?>">
          
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
            <div class="form-group">
              <label for="productName">Product Name</label>
              <input type="text" id="productName" name="productName" value="<?= $editData['productname'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
              <label for="productCategory">Category</label>
              <select id="productCategory" name="productCategory" required>
                <option value="" disabled <?= !$editData ? 'selected' : '' ?>>Select Category</option>
                <?php
                $categories = ['vegetables', 'plants', 'fruits', 'fertilizers', 'Indoor Plants', 'Outdoor Plants', 'Seeds', 'Tools'];
                foreach ($categories as $cat) {
                    $selected = ($editData && isset($editData['category']) && $editData['category'] == $cat) ? 'selected' : '';
                    echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          
          <div class="row">
            <div class="form-group">
              <label for="productPrice">Price (Rs)</label>
              <input type="number" id="productPrice" name="productPrice" value="<?= $editData['price'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
              <label for="productStock">Weight/Pieces</label>
              <input type="text" id="productStock" name="productStock" value="<?= $editData['weight'] ?? '' ?>" required>
            </div>
            
            <div class="form-group discount-field">
              <label for="discountPercentage">Discount Percentage (%)</label>
              <input type="number" name="discountPercentage" id="discountPercentage" min="1" max="99" value="<?= $editData['discount'] ?? '10' ?>">
            </div>
          </div>
          
          <div class="form-group">
            <label for="productDescription">Description</label>
            <textarea id="productDescription" name="productDescription" rows="4"><?= $editData['description'] ?? '' ?></textarea>
          </div>
          
          <div class="form-group">
            <label for="productImage">Product Image</label>
            <div class="image-preview" id="imagePreview">
              <?php if ($editData && !empty($editData['imageurl'])): ?>
                <img id="previewImg" src="../<?= htmlspecialchars($editData['imageurl']) ?>" alt="Product Preview">
              <?php else: ?>
                <img id="previewImg" src="./images/placeholder.jpg" alt="Preview">
              <?php endif; ?>
            </div>
            <input type="file" id="productImage" name="productImage" accept="image/*" <?= $editData ? '' : 'required' ?>>
          </div>
          
          <div class="form-action">
            <button type="submit" class="btn btn-primary" id="saveButton"><?= $editData ? 'Update Product' : 'Save Product' ?></button>
            <button type="button" class="btn btn-cancel" id="cancelButton">Cancel</button>
          </div>
        </form>
      </div>
      
      <!-- Product List -->
      <div class="product-list">
        <div class="filter-row">
          <input type="text" class="search-box" placeholder="Search products..." id="searchProducts">
          <button class="btn btn-primary" id="addNewProduct">Add New Product</button>
        </div>
        
        <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Weight/Stock</th>
              <th>Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="productTableBody">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td>
                    <?php if (!empty($row['imageurl'])): ?>
                      <img src="../<?= htmlspecialchars($row['imageurl']) ?>" alt="<?= htmlspecialchars($row['productname']) ?>" class="product-image" onerror="this.src='./images/placeholder.jpg'">
                    <?php else: ?>
                      <img src="./images/placeholder.jpg" alt="No Image" class="product-image">
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['productname']) ?></td>
                  <td><?= htmlspecialchars($row['category']) ?></td>
                  <td>Rs. <?= htmlspecialchars($row['price']) ?></td>
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
                    <div class="action-buttons">
                      <button class="edit-btn" onclick="editProduct(<?= $row['itemid'] ?>)">
                        <span class="material-symbols-sharp">edit</span>
                      </button>
                      <button class="delete-btn" onclick="deleteProduct(<?= $row['itemid'] ?>)">
                        <span class="material-symbols-sharp">delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align: center;">No products found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <br><br>
      <!-- Offers Section -->
      <div class="section-divider">
        <h2>Special Offers Management</h2>
      </div>
      
      <!-- Offer List -->
      <div class="offer-list">
        <div class="filter-row">
          <input type="text" class="search-box" placeholder="Search offers..." id="searchOffers">
          <button class="btn btn-primary" id="addNewOffer">Add New Offer</button>
        </div>
        
        <?php
        // Fetch offers from database
        $offersQuery = "SELECT * FROM offers ORDER BY id DESC";
        $offersResult = mysqli_query($conn, $offersQuery);
        ?>
        
        <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Original Price</th>
              <th>Offer Price</th>
              <th>Discount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="offerTableBody">
            <?php if ($offersResult && mysqli_num_rows($offersResult) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($offersResult)): ?>
                <tr>
                  <td>
                    <?php if (!empty($row['image_url'])): ?>
                      <img src="../<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="product-image" onerror="this.src='./images/placeholder.jpg'">
                    <?php else: ?>
                      <img src="./images/placeholder.jpg" alt="No Image" class="product-image">
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['product_name']) ?></td>
                  <td><?= htmlspecialchars($row['category']) ?></td>
                  <td>Rs. <?= htmlspecialchars($row['original_price']) ?></td>
                  <td>Rs. <?= htmlspecialchars($row['offer_price']) ?></td>
                  <td><?= htmlspecialchars($row['discount_percentage']) ?>%</td>
                  <td>
                    <span class="offer-status <?= $row['is_active'] ? 'status-active' : 'status-inactive' ?>">
                      <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="edit-btn" onclick="editOffer(<?= $row['id'] ?>)">
                        <span class="material-symbols-sharp">edit</span>
                      </button>
                      <button class="delete-btn" onclick="deleteOffer(<?= $row['id'] ?>)">
                        <span class="material-symbols-sharp">delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" style="text-align: center;">No offers found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <!-- Offer Form -->
      <div class="offer-form" id="offerForm" style="display:none;">
        <h2 id="offerFormTitle">Add New Offer</h2>
        <form id="addOfferForm" method="post" enctype="multipart/form-data">
          <input type="hidden" id="offerId" name="offerId" value="">
          <input type="hidden" name="currentOfferImageUrl" value="">
          
          <div class="form-row">
            <div class="form-group">
              <label for="offerProductName">Product Name</label>
              <input type="text" id="offerProductName" name="offerProductName" required>
            </div>
            
            <div class="form-group">
              <label for="offerCategory">Category</label>
              <select id="offerCategory" name="offerCategory" required>
                <option value="" disabled selected>Select Category</option>
                <?php
                $categories = ['Vegetables', 'Plants', 'Fruits', 'Fertilizers', 'Indoor Plants', 'Outdoor Plants', 'Seeds', 'Tools'];
                foreach ($categories as $cat) {
                    echo "<option value='$cat'>" . ucfirst($cat) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="originalPrice">Original Price (Rs)</label>
              <input type="number" id="originalPrice" name="originalPrice" min="1" step="0.01" required>
            </div>
            
            <div class="form-group">
              <label for="discountPercentage">Discount Percentage (%)</label>
              <input type="number" id="offerDiscountPercentage" name="offerDiscountPercentage" min="1" max="99" value="10" required>
            </div>
            
            <div class="form-group">
              <label for="offerPrice">Offer Price (Rs)</label>
              <input type="number" id="offerPrice" name="offerPrice" min="0" step="0.01" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="offerStatus">Status</label>
            <select id="offerStatus" name="offerStatus" required>
              <option value="1" selected>Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="offerImage">Offer Image</label>
            <div class="image-preview" id="offerImagePreview">
              <img id="offerPreviewImg" src="./images/placeholder.jpg" alt="Preview">
            </div>
            <input type="file" id="offerImage" name="offerImage" accept="image/*" required>
          </div>
          
          <div class="form-action">
            <button type="submit" class="btn btn-primary" id="saveOfferButton">Save Offer</button>
            <button type="button" class="btn btn-cancel" id="cancelOfferButton">Cancel</button>
          </div>
        </form>
      </div>
      </main>
      <!-- End main -->

      <!-- Start right section -->
    <div class="right">
      <div class="top">
         <button id="menu_bar">
           <span class="material-symbols-sharp">menu</span>
         </button>

         <div class="theme-toggler">
           <span class="material-symbols-sharp active">light_mode</span>
           <span class="material-symbols-sharp">dark_mode</span>
         </div>
          <div class="profile">
             <div class="info">
                 <p><b>Visal</b></p>
                 <p>Admin</p>
                 <small class="text-muted"></small>
             </div>
             <div class="profile-photo">
               <img src="./images/profile-1.jpg" alt=""/>
             </div>
          </div>
      </div>

      <div class="recent_updates">
         <h2>Recent Updates</h2>
         <div class="updates">
            <div class="update">
               <div class="profile-photo">
                  <img src="./images/profile-4.jpg" alt=""/>
               </div>
              <div class="message">
                 <p><b>Product Update:</b> Added new plant varieties</p>
                 <small class="text-muted">2 Minutes Ago</small>
              </div>
            </div>
            <div class="update">
              <div class="profile-photo">
              <img src="./images/profile-3.jpg" alt=""/>
              </div>
             <div class="message">
                <p><b>Inventory Alert:</b> Stock running low</p>
                <small class="text-muted">25 Minutes Ago</small>
             </div>
           </div>
           <div class="update">
            <div class="profile-photo">
               <img src="./images/profile-2.jpg" alt=""/>
            </div>
           <div class="message">
              <p><b>Price Update:</b> Updated prices for seasonal plants</p>
              <small class="text-muted">1 Hour Ago</small>
           </div>
         </div>
        </div>
      </div>

      <div class="sales-analytics">
         <h2>Product Analytics</h2>

          <div class="item onlion">
            <div class="icon">
              <span class="material-symbols-sharp">trending_up</span>
            </div>
            <div class="right_text">
              <div class="info">
                <h3>Top Selling</h3>
                <small class="text-muted">Last 24 Hours</small>
              </div>
              <h5 class="success">+12%</h5>
              <h3>Anthurium Plant</h3>
            </div>
          </div>
          
          <div class="item onlion">
            <div class="icon">
              <span class="material-symbols-sharp">scatter_plot</span>
            </div>
            <div class="right_text">
              <div class="info">
                <h3>Most Viewed</h3>
                <small class="text-muted">Last 24 Hours</small>
              </div>
              <h5 class="success">+35%</h5>
              <h3>Garden Tools</h3>
            </div>
          </div>
          
          <div class="item onlion">
            <div class="icon">
              <span class="material-symbols-sharp">trending_down</span>
            </div>
            <div class="right_text">
              <div class="info">
                <h3>Low in Stock</h3>
                <small class="text-muted">Immediate Attention</small>
              </div>
              <h5 class="danger">3 Items</h5>
              <h3>Needs Restock</h3>
            </div>
          </div>
      </div>
    </div>
  </div>

  <script>
    // Get DOM elements
    const productForm = document.getElementById('productForm');
    const addProductForm = document.getElementById('addProductForm');
    const cancelButton = document.getElementById('cancelButton');
    const addNewProductBtn = document.getElementById('addNewProduct');
    const searchInput = document.getElementById('searchProducts');
    const productImage = document.getElementById('productImage');
    const previewImg = document.getElementById('previewImg');
    
    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Image preview functionality
      productImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
          }
          reader.readAsDataURL(file);
        }
      });
      
      // Add new product button
      addNewProductBtn.addEventListener('click', function() {
        // Reset form
        addProductForm.reset();
        document.getElementById('productId').value = '';
        previewImg.src = './images/placeholder.jpg';
        document.getElementById('formTitle').textContent = 'Add New Product';
        
        // Show form
        productForm.style.display = 'block';
        
        // Scroll to form
        productForm.scrollIntoView({behavior: 'smooth'});
      });
      
      // Cancel button
      cancelButton.addEventListener('click', function() {
        productForm.style.display = 'none';
      });
      
      // Search functionality
      searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#productTableBody tr');
        
        rows.forEach(row => {
          const productName = row.cells[1].textContent.toLowerCase();
          const category = row.cells[2].textContent.toLowerCase();
          
          if (productName.includes(searchTerm) || category.includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
      
      // Toggle discount field based on product type
      const productTypeRadios = document.querySelectorAll('input[name="productType"]');
      const discountField = document.querySelector('.discount-field');
      
      function toggleDiscountField() {
        const isOffer = document.querySelector('input[name="productType"]:checked').value === 'offer';
        discountField.style.display = isOffer ? 'block' : 'none';
        
        // Make discount field required only for offers
        document.getElementById('discountPercentage').required = isOffer;
      }
      
      // Initial check
      toggleDiscountField();
      
      // Listen for changes
      productTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleDiscountField);
      });
    });
    
    // Theme toggle functionality (existing)
    const themeToggler = document.querySelector('.theme-toggler');
    themeToggler.addEventListener('click', () => {
      document.body.classList.toggle('dark-theme-variables');
      themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
      themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
    });
    
    // Mobile menu toggle (existing)
    const menuBtn = document.getElementById('menu_bar');
    const closeBtn = document.getElementById('close_btn');
    const sidebar = document.querySelector('aside');
    
    menuBtn.addEventListener('click', () => {
      sidebar.style.display = 'block';
    });
    
    closeBtn.addEventListener('click', () => {
      sidebar.style.display = 'none';
    });
    
    // Edit product function
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
    
    // Delete product function
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
    // Add this to the end of your existing script tag

// Offers Management
const offerForm = document.getElementById('offerForm');
const addOfferForm = document.getElementById('addOfferForm');
const cancelOfferButton = document.getElementById('cancelOfferButton');
const addNewOfferBtn = document.getElementById('addNewOffer');
const searchOffersInput = document.getElementById('searchOffers');
const offerImage = document.getElementById('offerImage');
const offerPreviewImg = document.getElementById('offerPreviewImg');
const originalPriceInput = document.getElementById('originalPrice');
const discountPercentageInput = document.getElementById('offerDiscountPercentage');
const offerPriceInput = document.getElementById('offerPrice');

// Calculate offer price automatically
originalPriceInput.addEventListener('input', calculateOfferPrice);
discountPercentageInput.addEventListener('input', calculateOfferPrice);

function calculateOfferPrice() {
  const originalPrice = parseFloat(originalPriceInput.value) || 0;
  const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
  
  if (originalPrice > 0 && discountPercentage > 0) {
    const offerPrice = originalPrice - (originalPrice * discountPercentage / 100);
    offerPriceInput.value = offerPrice.toFixed(2);
  }
}

// Override calculation if manually changed
offerPriceInput.addEventListener('input', function() {
  // User is manually setting the price, do nothing
});

// Image preview functionality
offerImage.addEventListener('change', function() {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      offerPreviewImg.src = e.target.result;
    }
    reader.readAsDataURL(file);
  }
});

// Add new offer button
addNewOfferBtn.addEventListener('click', function() {
  // Reset form
  addOfferForm.reset();
  document.getElementById('offerId').value = '';
  offerPreviewImg.src = './images/placeholder.jpg';
  document.getElementById('offerFormTitle').textContent = 'Add New Offer';
  document.getElementById('saveOfferButton').textContent = 'Save Offer';
  
  // Show form
  offerForm.style.display = 'block';
  
  // Scroll to form
  offerForm.scrollIntoView({behavior: 'smooth'});
});

// Cancel button
cancelOfferButton.addEventListener('click', function() {
  offerForm.style.display = 'none';
});

// Search offers functionality
searchOffersInput.addEventListener('keyup', function() {
  const searchTerm = this.value.toLowerCase();
  const rows = document.querySelectorAll('#offerTableBody tr');
  
  rows.forEach(row => {
    const offerName = row.cells[1].textContent.toLowerCase();
    const category = row.cells[2].textContent.toLowerCase();
    
    if (offerName.includes(searchTerm) || category.includes(searchTerm)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});

// Edit offer function
function editOffer(id) {
  // Send AJAX request to get offer data
  fetch('get_offer.php?id=' + id)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Populate form
        document.getElementById('offerId').value = data.offer.id;
        document.getElementById('offerProductName').value = data.offer.product_name;
        document.getElementById('offerCategory').value = data.offer.category;
        document.getElementById('originalPrice').value = data.offer.original_price;
        document.getElementById('offerDiscountPercentage').value = data.offer.discount_percentage;
        document.getElementById('offerPrice').value = data.offer.offer_price;
        document.getElementById('offerStatus').value = data.offer.is_active;
        document.getElementsByName('currentOfferImageUrl')[0].value = data.offer.image_url;
        
        // Update image preview
        if (data.offer.image_url) {
          offerPreviewImg.src = '../' + data.offer.image_url;
        } else {
          offerPreviewImg.src = './images/placeholder.jpg';
        }
        
        // Update form title and button
        document.getElementById('offerFormTitle').textContent = 'Edit Offer';
        document.getElementById('saveOfferButton').textContent = 'Update Offer';
        
        // Make image upload optional for edits
        document.getElementById('offerImage').removeAttribute('required');
        
        // Show form
        offerForm.style.display = 'block';
        offerForm.scrollIntoView({behavior: 'smooth'});
      } else {
        alert('Failed to load offer data: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while fetching the offer data.');
    });
}

// Delete offer function
function deleteOffer(id) {
  if (confirm('Are you sure you want to delete this offer?')) {
    // Send AJAX request to delete
    fetch('delete_offer.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'id=' + id
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Offer deleted successfully!');
        location.reload(); // Refresh the page
      } else {
        alert('Failed to delete offer: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while deleting the offer.');
    });
  }
}

// Offer form submission
addOfferForm.addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Create FormData object to handle file upload
  const formData = new FormData(this);
  
  // Add action parameter
  formData.append('action', document.getElementById('offerId').value ? 'update' : 'add');
  
  // Send AJAX request
  fetch('process_offer.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload(); // Refresh the page
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while processing the offer.');
  });
});
  </script>

</body>
</html>
<?php 
// Clean up session variables
unset($_SESSION['edit_product']); 

// Close the database connection
mysqli_close($conn);
?>