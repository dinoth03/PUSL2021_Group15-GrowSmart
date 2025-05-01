<?php
session_start();
require_once 'db.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Get user data
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Process profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    
    // Update user data
    $update_query = "UPDATE users SET 
                     first_name = '$first_name',
                     last_name = '$last_name',
                     address = '$address',
                     phone = '$phone',
                     bio = '$bio'
                     WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $message = '<div class="alert alert-success">Profile updated successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error updating profile: ' . mysqli_error($conn) . '</div>';
    }
    
    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $upload_dir = "uploads/profile_pics/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $_FILES['profile_pic']['name'];
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            $update_pic_query = "UPDATE users SET profile_pic = '$file_name' WHERE id = $user_id";
            mysqli_query($conn, $update_pic_query);
        }
    }
    
    // Handle cover photo upload
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] == 0) {
        $upload_dir = "uploads/cover_photos/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $_FILES['cover_photo']['name'];
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $target_file)) {
            $update_cover_query = "UPDATE users SET cover_photo = '$file_name' WHERE id = $user_id";
            mysqli_query($conn, $update_cover_query);
        }
    }
    
    // Refresh user data
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - GrowSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f6f9;
            padding-top: 20px;
        }
        .profile-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-container">
            <h2 class="mb-4">Edit Profile</h2>
            
            <?php echo $message; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $user['address']; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo $user['bio']; ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="profile_pic" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_pic" name="profile_pic">
                    <?php if($user['profile_pic'] && $user['profile_pic'] != 'default.jpg'): ?>
                        <img src="uploads/profile_pics/<?php echo $user['profile_pic']; ?>" class="preview-image mt-2">
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="cover_photo" class="form-label">Cover Photo</label>
                    <input type="file" class="form-control" id="cover_photo" name="cover_photo">
                    <?php if($user['cover_photo'] && $user['cover_photo'] != 'default_cover.jpg'): ?>
                        <img src="uploads/cover_photos/<?php echo $user['cover_photo']; ?>" class="preview-image mt-2">
                    <?php endif; ?>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <a href="profile.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>