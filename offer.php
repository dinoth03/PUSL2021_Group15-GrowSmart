<?php
session_start();

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "growsmartDB";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Fetch offers grouped by category
$query = "SELECT * FROM offers WHERE is_active = 1 ORDER BY category, product_name";
$result = $conn->query($query);

$offers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $offers[$row['category']][] = $row;
    }
    $result->free();
} else {
    echo "Error fetching offers: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrowSmart - Special Offers & Promotions</title>
    <link rel="icon" type="image/png" href="Img/TitleLogo.png">
    <link rel="stylesheet" href="css/PromotionStyle.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff6b6b;
            color: white;
            padding: 5px 8px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 14px;
        }
        
        .product {
            position: relative;
        }
        
        .empty-category {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        /* Add to cart button styles */
        .product button {
            transition: all 0.3s ease;
        }
        
        .product button.adding {
            background-color: #4CAF50;
            color: white;
        }
        
        @keyframes addedToCart {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .product button.adding {
            animation: addedToCart 0.5s ease;
        }
        
        /* Toast notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>
<iframe src="components/header.html" style="width:100%; height: 140px; border:none;"></iframe> 
 
<header>
    <h2 class="promotion-title" style="margin-left: 150px;">🌿 Special Offers</h2>

    <div class="cart-icon" onclick="toggleCart()">
        <i class="fa fa-shopping-cart" style="margin-right: 150px;"></i>
        <span id="cart-count" style="margin-right: 150px;">0</span>
    </div>
</header>

<!-- Display offers by category -->
<?php if (empty($offers)): ?>
    <div class="empty-category">
        <h3>No special offers available at the moment</h3>
        <p>Please check back later for exciting deals!</p>
    </div>
<?php else: ?>
    <?php foreach ($offers as $category => $categoryOffers): ?>
        <div class="category-title"><?php echo htmlspecialchars($category); ?></div>
        <div class="product-list">
            <?php foreach ($categoryOffers as $offer): ?>
                <div class="product" 
                     data-id="<?php echo $offer['id']; ?>" 
                     data-name="<?php echo htmlspecialchars($offer['product_name']); ?>" 
                     data-price="<?php echo $offer['offer_price']; ?>">
                    <span class="discount-badge">-<?php echo $offer['discount_percentage']; ?>%</span>
                    <img src="<?php echo htmlspecialchars($offer['image_url']); ?>" alt="<?php echo htmlspecialchars($offer['product_name']); ?>"
                         onerror="this.src='Img/product_placeholder.png'">
                    <h3><?php echo htmlspecialchars($offer['product_name']); ?></h3>
                    <p>Rs.<?php echo $offer['original_price']; ?> > <b>Rs.<?php echo $offer['offer_price']; ?></b></p>
                    <button class="add-to-cart-btn" 
                            data-id="<?php echo $offer['id']; ?>" 
                            data-name="<?php echo htmlspecialchars($offer['product_name']); ?>" 
                            data-price="<?php echo $offer['offer_price']; ?>"
                            data-image="<?php echo htmlspecialchars($offer['image_url']); ?>"
                            data-category="<?php echo htmlspecialchars($category); ?>">
                        Add To Cart
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Shopping Cart -->
<div class="cart" id="cart">
    <div class="cart-header">
        <h2>Shopping Cart</h2>
        <button class="close-btn" onclick="toggleCart()">&times;</button>
    </div>
    <ul id="cart-items"></ul>
    <div class="cart-footer">
        <span id="cart-total">Total: Rs0.00</span>
        <button class="checkout-btn" onclick="checkout()">Check Out</button>
    </div>
</div>

<!-- Toast notification -->
<div id="toast" class="toast-notification">Item added to cart</div>

<br><br><br><br><br><br><br>

<!-- banner-->
<div class="banner">
    <div class="slider" style="--quantity: 10">
        <div class="item" style="--position: 1"><img src="Img/1st iamge.webp" alt=""></div>
        <div class="item" style="--position: 2"><img src="Img/2nd image.webp" alt=""></div>
        <div class="item" style="--position: 3"><img src="Img/3rd image.webp" alt=""></div>
        <div class="item" style="--position: 4"><img src="Img/4th image.webp" alt=""></div>
        <div class="item" style="--position: 5"><img src="Img/5th image.webp" alt=""></div>
        <div class="item" style="--position: 6"><img src="Img/6th image.webp" alt=""></div>
        <div class="item" style="--position: 7"><img src="Img/7th image.webp" alt=""></div>
        <div class="item" style="--position: 8"><img src="Img/8th image.avif" alt=""></div>
        <div class="item" style="--position: 9"><img src="Img/9th image.webp" alt=""></div>
        <div class="item" style="--position: 10"><img src="Img/10th image.jpg" alt=""></div>
    </div>
</div>

<iframe src="components/footer.html" style="width:100%; height: 575px; border:none;"></iframe> 
    
<script>
// Cart functionality
let cart = [];
let isCartVisible = false;

// Check if localStorage is available
function isLocalStorageAvailable() {
    try {
        const test = 'test';
        localStorage.setItem(test, test);
        localStorage.removeItem(test);
        return true;
    } catch(e) {
        return false;
    }
}

// Initialize cart from localStorage
document.addEventListener('DOMContentLoaded', function() {
    // Setup event listeners for all Add to Cart buttons
    setupAddToCartButtons();
    
    if (isLocalStorageAvailable()) {
        // Load cart from localStorage
        try {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCartCount();
                updateCartItems();
            }
        } catch (e) {
            console.error("Failed to load cart from localStorage", e);
            // Reset cart if corrupted
            cart = [];
        }
    } else {
        // Local storage not available, use session only cart
        console.warn("localStorage not available, cart will not persist between page loads");
    }
});

// Setup event listeners for all Add to Cart buttons
function setupAddToCartButtons() {
    const buttons = document.querySelectorAll('.add-to-cart-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            const image = this.getAttribute('data-image');
            const category = this.getAttribute('data-category');
            
            addToCart(id, name, price, image, category, this);
        });
    });
}

function toggleCart() {
    const cartElement = document.getElementById('cart');
    isCartVisible = !isCartVisible;
    cartElement.style.right = isCartVisible ? '0' : '-400px';
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function animateAddToCart(buttonElement) {
    // Add a class for animation
    buttonElement.classList.add('adding');
    
    // Change text temporarily
    const originalText = buttonElement.textContent;
    buttonElement.textContent = "Added!";
    
    // Reset after animation
    setTimeout(() => {
        buttonElement.classList.remove('adding');
        buttonElement.textContent = originalText;
    }, 1000);
}

function addToCart(id, name, price, image, category, buttonElement) {
    // Validate inputs
    if (!id || !name || isNaN(price)) {
        console.error("Invalid product data", { id, name, price });
        showToast("Sorry, there was an error adding this product to your cart.");
        return;
    }
    
    // Ensure price is a number
    price = parseFloat(price);
    
    // Check if already in cart
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            image: image || 'Img/product_placeholder.png', // Fallback image
            category: category || 'Other',
            quantity: 1
        });
    }
    
    // Save to localStorage
    if (isLocalStorageAvailable()) {
        try {
            localStorage.setItem('cart', JSON.stringify(cart));
        } catch (e) {
            console.error("Failed to save cart to localStorage", e);
        }
    }
    
    updateCartCount();
    updateCartItems();
    
    // Add visual feedback if button element was passed
    if (buttonElement && buttonElement.tagName === 'BUTTON') {
        animateAddToCart(buttonElement);
    }
    
    // Show toast notification
    showToast(`${name} added to your cart!`);
}

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    let totalItems = 0;
    
    cart.forEach(item => {
        totalItems += item.quantity;
    });
    
    cartCount.textContent = totalItems;
}

function updateCartItems() {
    const cartItemsElement = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    
    // Clear current items
    cartItemsElement.innerHTML = '';
    
    let total = 0;
    
    // Add each item
    cart.forEach((item, index) => {
        const li = document.createElement('li');
        
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        li.innerHTML = `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}" onerror="this.src='Img/product_placeholder.png'">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>Rs.${item.price.toFixed(2)} x ${item.quantity}</p>
                </div>
                <div class="cart-item-actions">
                    <button onclick="changeQuantity(${index}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="changeQuantity(${index}, 1)">+</button>
                    <button onclick="removeItem(${index})">🗑️</button>
                </div>
            </div>
        `;
        
        cartItemsElement.appendChild(li);
    });
    
    // Update total
    cartTotalElement.textContent = `Total: Rs${total.toFixed(2)}`;
}

function changeQuantity(index, delta) {
    cart[index].quantity += delta;
    
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    
    if (isLocalStorageAvailable()) {
        localStorage.setItem('cart', JSON.stringify(cart));
    }
    updateCartCount();
    updateCartItems();
}

function removeItem(index) {
    const itemName = cart[index].name;
    cart.splice(index, 1);
    
    if (isLocalStorageAvailable()) {
        localStorage.setItem('cart', JSON.stringify(cart));
    }
    updateCartCount();
    updateCartItems();
    
    // Show toast notification
    showToast(`${itemName} removed from your cart`);
}

function checkout() {
    if (cart.length === 0) {
        showToast('Your cart is empty!');
        return;
    }
    
    // Redirect to checkout page
    window.location.href = 'cart.html';
}
</script>

</body>
</html>
<?php $conn->close(); ?>