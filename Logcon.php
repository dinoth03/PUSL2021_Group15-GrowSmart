<?php
session_start();
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "growsmartDB"; // Change as needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set common session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['account_type'] = $user['account_type'];
            
            // Role-based redirection
            if ($user['account_type'] === 'Admin') {
                // Redirect admin to admin dashboard
                header("Location: admin panel/admin_dashboard.php");
            } else {
                // Redirect sellers and regular users to home page
                header("Location: home new.html");
            }
            exit();
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "No account found with this email.";
    }
}

$conn->close();
?>