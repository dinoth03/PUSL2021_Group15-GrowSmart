<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Only execute API logic when accessed via API requests, not when loading the HTML
if (isset($_GET['action'])) {
    $server = "localhost";
    $username = "root";
    $password = "";
    $db = "growsmartDB";

    // Connect to MySQL
    $conn = mysqli_connect($server, $username, $password, $db);

    // Check connection
    if (!$conn) {
        echo json_encode(["error" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    $action = $_GET['action'];

    switch ($action) {
        case 'get_categories':
            getCategories($conn);
            break;
        case 'get_plants_by_category':
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            getPlantsbyCategory($conn, $category);
            break;
        case 'get_plant_details':
            $plant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            getPlantDetails($conn, $plant_id);
            break;
        case 'search_plants':
            $query = isset($_GET['query']) ? $_GET['query'] : '';
            searchPlants($conn, $query);
            break;
        case 'get_quiz_questions':
            getQuizQuestions($conn);
            break;
        default:
            echo json_encode(["error" => "Invalid action"]);
    }

    mysqli_close($conn);
    exit; // Stop execution after handling the API request
}

// API Functions
function getCategories($conn) {
    $sql = "SELECT DISTINCT category FROM plants";
    $result = mysqli_query($conn, $sql);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['category'];
    }
    
    echo json_encode($categories);
}

function getPlantsbyCategory($conn, $category) {
    $category = mysqli_real_escape_string($conn, $category);
    $sql = "SELECT id, name FROM plants WHERE category = '$category'";
    $result = mysqli_query($conn, $sql);
    
    $plants = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $plants[] = $row;
    }
    
    echo json_encode($plants);
}

function getPlantDetails($conn, $plant_id) {
    // Get main plant info
    $sql = "SELECT * FROM plants WHERE id = $plant_id";
    $result = mysqli_query($conn, $sql);
    $plant = mysqli_fetch_assoc($result);
    
    if (!$plant) {
        echo json_encode(["error" => "Plant not found"]);
        return;
    }
    
    // Get care info
    $sql = "SELECT * FROM plant_care WHERE plant_id = $plant_id";
    $result = mysqli_query($conn, $sql);
    $care = mysqli_fetch_assoc($result);
    
    // Get benefits
    $sql = "SELECT benefit FROM plant_benefits WHERE plant_id = $plant_id";
    $result = mysqli_query($conn, $sql);
    $benefits = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $benefits[] = $row['benefit'];
    }
    
    // Combine all data
    $plant['care'] = $care;
    $plant['benefits'] = $benefits;
    
    echo json_encode($plant);
}

function searchPlants($conn, $query) {
    $query = mysqli_real_escape_string($conn, $query);
    $sql = "SELECT id, name, category FROM plants 
            WHERE name LIKE '%$query%' 
            OR scientific_name LIKE '%$query%'";
    $result = mysqli_query($conn, $sql);
    
    $plants = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $plants[] = $row;
    }
    
    echo json_encode($plants);
}

function getQuizQuestions($conn) {
    $sql = "SELECT * FROM plant_quiz";
    $result = mysqli_query($conn, $sql);
    
    $questions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = [
            "question" => $row['question'],
            "options" => [
                $row['option1'],
                $row['option2'],
                $row['option3'],
                $row['option4']
            ],
            "correctAnswer" => $row['correct_answer'] - 1 // Adjust to 0-based index for JavaScript
        ];
    }
    
    echo json_encode($questions);
}
?>