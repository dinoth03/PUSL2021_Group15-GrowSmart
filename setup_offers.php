<?php
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

// Initial offers data from your HTML
$offersData = [
    // Herbal Plants
    [
        'product_name' => 'Aloe Vera කෝමාරිකා',
        'description' => 'Medicinal plant good for skin care and burns',
        'original_price' => 180.00,
        'offer_price' => 100.00,
        'category' => 'Herbal Plants',
        'image_url' => 'Img/Aloe-vera plant-.webp'
    ],
    [
        'product_name' => 'Ashwagandha අශ්වගන්ධ',
        'description' => 'Ayurvedic medicinal plant with multiple health benefits',
        'original_price' => 200.00,
        'offer_price' => 120.00,
        'category' => 'Herbal Plants',
        'image_url' => 'Img/Ashwagandha (1).jpg'
    ],
    [
        'product_name' => 'German chamomile ජර්මන් කැලමිල්',
        'description' => 'Herbal plant used for tea and relaxation',
        'original_price' => 200.00,
        'offer_price' => 100.00,
        'category' => 'Herbal Plants',
        'image_url' => 'Img/German chamomile.jpeg'
    ],
    [
        'product_name' => 'Lavender Lavance ලැවෙන්ඩර් මල්',
        'description' => 'Fragrant plant with calming properties',
        'original_price' => 1000.00,
        'offer_price' => 500.00,
        'category' => 'Herbal Plants',
        'image_url' => 'Img/LavenderLavance.jpg'
    ],
    [
        'product_name' => 'Turmeric කහ',
        'description' => 'Anti-inflammatory spice plant with many uses',
        'original_price' => 1000.00,
        'offer_price' => 750.00,
        'category' => 'Herbal Plants',
        'image_url' => 'Img/Turmeric.webp'
    ],
    
    // House Plants
    [
        'product_name' => 'Spider plant මකුළු ශාකය',
        'description' => 'Air-purifying indoor plant',
        'original_price' => 500.00,
        'offer_price' => 200.00,
        'category' => 'House Plants',
        'image_url' => 'Img/L11PeaceLilyGrey_1100x.jpg'
    ],
    [
        'product_name' => 'ZZ plant සල්ලි ගහ',
        'description' => 'Low-maintenance indoor plant',
        'original_price' => 400.00,
        'offer_price' => 200.00,
        'category' => 'House Plants',
        'image_url' => 'Img/Beards-Daises.webp'
    ],
    [
        'product_name' => 'aglaonemar ඇග්ලොනිමා',
        'description' => 'Colorful foliage plant for indoors',
        'original_price' => 150.00,
        'offer_price' => 100.00,
        'category' => 'House Plants',
        'image_url' => 'Img/aglaonema.webp'
    ],
    [
        'product_name' => 'anthurium ඇන්තුරියම්',
        'description' => 'Flowering indoor plant with glossy leaves',
        'original_price' => 440.00,
        'offer_price' => 220.00,
        'category' => 'House Plants',
        'image_url' => 'Img/anthurium.webp'
    ],
    [
        'product_name' => 'money tree මනී ප්ලාන්ට්',
        'description' => 'Good luck plant for home or office',
        'original_price' => 420.00,
        'offer_price' => 260.00,
        'category' => 'House Plants',
        'image_url' => 'Img/money tree.webp'
    ],
    
    // Flower Plants
    [
        'product_name' => 'Rose රෝස',
        'description' => 'Classic flowering plant with fragrant blooms',
        'original_price' => 480.00,
        'offer_price' => 200.00,
        'category' => 'Flower Plants',
        'image_url' => 'Img/rose.webp'
    ],
    [
        'product_name' => 'Hydrangea තුම්මස්මල්',
        'description' => 'Flowering shrub with large blooms',
        'original_price' => 300.00,
        'offer_price' => 200.00,
        'category' => 'Flower Plants',
        'image_url' => 'Img/Hydrangea.avif'
    ],
    [
        'product_name' => 'Bougainvillea බෝගන්විලා',
        'description' => 'Vibrant flowering plant for garden or pots',
        'original_price' => 300.00,
        'offer_price' => 200.00,
        'category' => 'Flower Plants',
        'image_url' => 'Img/Bougainvillea.jpg'
    ],
    [
        'product_name' => 'Hibiscus සපත්තු මල',
        'description' => 'Tropical flowering plant with large blooms',
        'original_price' => 440.00,
        'offer_price' => 220.00,
        'category' => 'Flower Plants',
        'image_url' => 'Img/qcaFgEgbhTzdJZpN8f8fHY-1024-80.jpg.webp'
    ],
    [
        'product_name' => 'Orchid ඕකිඩ්',
        'description' => 'Elegant flowering plant for indoors',
        'original_price' => 320.00,
        'offer_price' => 260.00,
        'category' => 'Flower Plants',
        'image_url' => 'Img/Orchid.jpg'
    ]
];

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO offers (product_name, description, original_price, offer_price, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ssddss", $product_name, $description, $original_price, $offer_price, $category, $image_url);

// Insert each offer
$successCount = 0;
foreach ($offersData as $offer) {
    $product_name = $offer['product_name'];
    $description = $offer['description'];
    $original_price = $offer['original_price'];
    $offer_price = $offer['offer_price'];
    $category = $offer['category'];
    $image_url = $offer['image_url'];
    
    if ($stmt->execute()) {
        $successCount++;
    } else {
        echo "Error adding offer '$product_name': " . $stmt->error . "<br>";
    }
}

// Close statement and connection
$stmt->close();
$conn->close();

echo "<h1>Offers Setup Complete</h1>";
echo "<p>Successfully added $successCount out of " . count($offersData) . " offers to the database.</p>";
echo "<a href='offer.php'>Go to Offers Page</a>";
?>