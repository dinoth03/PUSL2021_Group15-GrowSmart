<?php
// filepath: c:\xampp\htdocs\grow\GrowSmart_Web\components\virtual_assistant.php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "growsmartDB";

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set charset to utf8
$conn->set_charset("utf8");

// Handle different actions based on the request
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_categories':
        getCategories($conn);
        break;
        
    case 'get_plants_by_category':
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        getPlantsByCategory($conn, $category);
        break;
        
    case 'get_plant_details':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        getPlantDetails($conn, $id);
        break;
        
    case 'search_plants':
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        searchPlants($conn, $query);
        break;
        
    case 'get_quiz_questions':
        getQuizQuestions($conn);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action specified']);
        break;
}

// Function to get all plant categories
function getCategories($conn) {
    $categories = [];
    
    // Query to get distinct categories
    $sql = "SELECT DISTINCT category FROM plants WHERE category IS NOT NULL AND category != ''";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
    }
    
    echo json_encode($categories);
}

// Function to get plants by category
function getPlantsByCategory($conn, $category) {
    $plants = [];
    
    if (!empty($category)) {
        $stmt = $conn->prepare("SELECT id, name FROM plants WHERE category = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $plants[] = $row;
            }
        }
        
        $stmt->close();
    }
    
    echo json_encode($plants);
}

// Function to get plant details
function getPlantDetails($conn, $id) {
    if ($id > 0) {
        // Get plant basic info
        $stmt = $conn->prepare("SELECT * FROM plants WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $plant = $result->fetch_assoc();
            
            // Get care instructions
            $careStmt = $conn->prepare("SELECT * FROM plant_care WHERE plant_id = ?");
            $careStmt->bind_param("i", $id);
            $careStmt->execute();
            $careResult = $careStmt->get_result();
            
            if ($careResult && $careResult->num_rows > 0) {
                $plant['care'] = $careResult->fetch_assoc();
            } else {
                $plant['care'] = null;
            }
            
            // Get benefits
            $benefitsStmt = $conn->prepare("SELECT benefit FROM plant_benefits WHERE plant_id = ?");
            $benefitsStmt->bind_param("i", $id);
            $benefitsStmt->execute();
            $benefitsResult = $benefitsStmt->get_result();
            
            $plant['benefits'] = [];
            if ($benefitsResult && $benefitsResult->num_rows > 0) {
                while ($row = $benefitsResult->fetch_assoc()) {
                    $plant['benefits'][] = $row['benefit'];
                }
            }
            
            echo json_encode($plant);
            $careStmt->close();
            $benefitsStmt->close();
        } else {
            echo json_encode(['error' => 'Plant not found']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Invalid plant ID']);
    }
}

// Function to search plants
function searchPlants($conn, $query) {
    $plants = [];
    
    if (!empty($query)) {
        // Search by name or scientific name
        $searchTerm = "%$query%";
        $stmt = $conn->prepare("SELECT id, name, category 
                              FROM plants 
                              WHERE name LIKE ? OR scientific_name LIKE ?
                              LIMIT 10");
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $plants[] = $row;
            }
        }
        
        $stmt->close();
    }
    
    echo json_encode($plants);
}

// Function to get quiz questions
function getQuizQuestions($conn) {
    $questions = [];
    
    // Get questions from database
    $sql = "SELECT * FROM plant_quiz ORDER BY RAND() LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $questions[] = [
                'question' => $row['question'],
                'options' => [
                    $row['option1'],
                    $row['option2'],
                    $row['option3'],
                    $row['option4']
                ],
                'correctAnswer' => (int)$row['correct_answer']
            ];
        }
        echo json_encode($questions);
    } else {
        // Return hardcoded questions if none in database
        $hardcoded_questions = [
            [
                'question' => 'Which of these plants is best for air purification?',
                'options' => ['Rose', 'Snake Plant', 'Cactus', 'Orchid'],
                'correctAnswer' => 1
            ],
            [
                'question' => 'What is the ideal watering frequency for most succulents?',
                'options' => ['Daily', 'Every 2-3 days', 'Weekly', 'When soil is completely dry'],
                'correctAnswer' => 3
            ],
            [
                'question' => 'Which light condition is best for growing tomatoes?',
                'options' => ['Full shade', 'Partial shade', 'Full sun', 'Low light'],
                'correctAnswer' => 2
            ],
            [
                'question' => 'What does "deadheading" refer to in gardening?',
                'options' => ['Removing dead plants', 'Pruning roots', 'Removing spent flowers', 'Cutting off new growth'],
                'correctAnswer' => 2
            ],
            [
                'question' => 'Which of these is NOT a common plant disease?',
                'options' => ['Powdery mildew', 'Black spot', 'Root rot', 'Leaf enhancement'],
                'correctAnswer' => 3
            ]
        ];
        echo json_encode($hardcoded_questions);
    }
}

// Close database connection
$conn->close();
?>