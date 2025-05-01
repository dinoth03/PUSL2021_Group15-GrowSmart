<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user ID (either from session or from URL)
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// Get user data
$user = getUserById($conn, $user_id);

// Check if user exists
if (!$user) {
    $_SESSION['error'] = "User not found!";
    header("Location: login.php");
    exit();
}

// Get user's posts
$posts_query = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$posts_result = mysqli_query($conn, $posts_query);

// Check if logged-in user is following this profile
$is_following = false;
if ($user_id != $_SESSION['user_id']) {
    $is_following = isFollowing($conn, $_SESSION['user_id'], $user_id);
}

// Count followers
$follower_count = countFollowers($conn, $user_id);

// Get user ratings information
$ratings_query = "SELECT AVG(rating) as avg_rating, COUNT(id) as rating_count 
                  FROM ratings WHERE rated_id = $user_id";
$ratings_result = mysqli_query($conn, $ratings_query);
$ratings_data = mysqli_fetch_assoc($ratings_result);
$avg_rating = round($ratings_data['avg_rating'] ?? 0, 1);
$rating_count = $ratings_data['rating_count'] ?? 0;

// Check if current user has rated this profile
$has_rated = false;
$user_rating = 0;
if ($user_id != $_SESSION['user_id']) {
    $rated_query = "SELECT rating FROM ratings 
                   WHERE rater_id = " . $_SESSION['user_id'] . " 
                   AND rated_id = $user_id";
    $rated_result = mysqli_query($conn, $rated_query);
    if (mysqli_num_rows($rated_result) > 0) {
        $has_rated = true;
        $user_rating = mysqli_fetch_assoc($rated_result)['rating'];
    }
}

// Get individual ratings to display
$individual_ratings_query = "SELECT r.*, u.first_name, u.last_name, u.profile_pic
                           FROM ratings r
                           JOIN users u ON r.rater_id = u.id
                           WHERE r.rated_id = $user_id
                           ORDER BY r.created_at DESC
                           LIMIT 5";
$individual_ratings_result = mysqli_query($conn, $individual_ratings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user['first_name'] . " " . $user['last_name']; ?> - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f3f6f9;
            padding-top: 70px; /* Space for navbar */
        }
        .navbar {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            color: #2e7d32;
        }
        .nav-user-pic {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .cover-photo {
            height: 200px;
            background-color: #e9ecef;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #e9ecef;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: #6c757d;
            background-size: cover;
            background-position: center;
        }
        .post-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .comment {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        /* Search results dropdown styling */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .search-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
        }
        .search-item:hover {
            background-color: #f8f9fa;
        }
        .search-item:last-child {
            border-bottom: none;
        }
        .search-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-highlight {
            background-color: #fff2cc;
            font-weight: bold;
        }
        .search-container {
            position: relative;
        }
        .post-image-container {
        max-height: 400px;
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .post-image {
        width: 100%;
        height: auto;
        object-fit: contain;
        max-height: 400px;
    }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-light">
        <div class="container">
            <a class="navbar-brand" href="user_profile.php">
                <i class="bi bi-tree"></i> GrowSmart
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="search-container me-auto ms-lg-3 my-2 my-lg-0">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search users..." autocomplete="off">
                    <div class="search-results" id="searchResults"></div>
                </div>
                
                <div class="d-flex align-items-center">
                    <a href="user_profile.php" class="text-decoration-none text-dark me-3">
                        <?php if(!empty($_SESSION['user_id']) && !empty($user['profile_pic']) && $user['profile_pic'] != 'default.jpg'): ?>
                            <img src="uploads/profile_pics/<?php echo $user['profile_pic']; ?>" class="nav-user-pic">
                        <?php else: ?>
                            <div class="nav-user-pic bg-secondary d-flex align-items-center justify-content-center text-white">
                                <?php echo substr($_SESSION['first_name'] ?? '', 0, 1); ?>
                            </div>
                        <?php endif; ?>
                    </a>
                    <a href="logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="profile-container">
            <!-- Cover Photo -->
            <div class="cover-photo" style="background-image: url('uploads/cover_photos/<?php echo $user['cover_photo']; ?>');">
                <?php if($user_id == $_SESSION['user_id']): ?>
                    <a href="edit_profile.php" class="btn btn-sm btn-primary float-end m-2">Edit Profile</a>
                <?php endif; ?>
            </div>

            <!-- Profile Info -->
            <div class="text-center">
                <div class="profile-pic" style="background-image: url('uploads/profile_pics/<?php echo $user['profile_pic']; ?>');">
                    <?php if(empty($user['profile_pic']) || $user['profile_pic'] == 'default.jpg'): ?>
                        <span>👤</span>
                    <?php endif; ?>
                </div>
                <h3 class="mt-3"><?php echo $user['first_name'] . " " . $user['last_name']; ?></h3>
                <p class="text-muted"><?php echo $user['address']; ?></p>
                
                <!-- Stats -->
                <div class="d-flex justify-content-center gap-4 my-3">
                    <div><strong><?php echo mysqli_num_rows($posts_result); ?></strong><br>Posts</div>
                    <div><strong><?php echo $follower_count; ?></strong><br>Followers</div>
                </div>
                <!-- Ratings Section -->
<div class="d-flex justify-content-center align-items-center gap-2 my-3">
    <div class="ratings-summary">
        <div class="d-flex align-items-center">
            <div class="rating-stars">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if($i <= $avg_rating): ?>
                        <i class="bi bi-star-fill text-warning"></i>
                    <?php elseif($i <= $avg_rating + 0.5): ?>
                        <i class="bi bi-star-half text-warning"></i>
                    <?php else: ?>
                        <i class="bi bi-star text-warning"></i>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <span class="ms-2">
                <strong><?php echo $avg_rating; ?></strong>
                <small class="text-muted">(<?php echo $rating_count; ?> <?php echo $rating_count == 1 ? 'rating' : 'ratings'; ?>)</small>
            </span>
        </div>
    </div>
</div>

<!-- Rating Form (only if viewing someone else's profile and not already rated) -->
<?php if($user_id != $_SESSION['user_id']): ?>
    <div class="my-3">
        <?php if(!$has_rated): ?>
            <button class="btn btn-outline-primary btn-sm" 
                    type="button" 
                    data-bs-toggle="modal" 
                    data-bs-target="#ratingModal">
                <i class="bi bi-star"></i> Rate this user
            </button>
        <?php else: ?>
            <button class="btn btn-primary btn-sm" 
                    type="button" 
                    data-bs-toggle="modal" 
                    data-bs-target="#ratingModal">
                <i class="bi bi-star-fill"></i> Update your rating
            </button>
        <?php endif; ?>
    </div>
    
    <!-- Rating Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $has_rated ? 'Update Rating' : 'Rate'; ?> 
                        <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ratingForm">
                        <input type="hidden" name="rated_id" value="<?php echo $user_id; ?>">
                        
                        <div class="rating-input text-center mb-3">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?php echo ($i <= $user_rating) ? '-fill' : ''; ?> text-warning star-rating" 
                                   data-rating="<?php echo $i; ?>" 
                                   style="font-size: 2rem; cursor: pointer;"></i>
                            <?php endfor; ?>
                            <input type="hidden" name="rating" id="selected-rating" value="<?php echo $user_rating; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment (optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitRating">
                        <?php echo $has_rated ? 'Update Rating' : 'Submit Rating'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Add this section if you want to display individual ratings -->
<?php if($rating_count > 0): ?>
    <div class="mt-4">
        <h5>Recent Ratings</h5>
        <?php while($rating = mysqli_fetch_assoc($individual_ratings_result)): ?>
            <div class="comment mb-2">
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <?php if(!empty($rating['profile_pic']) && $rating['profile_pic'] != 'default.jpg'): ?>
                            <img src="uploads/profile_pics/<?php echo $rating['profile_pic']; ?>" class="profile-pic-small" style="width:30px;height:30px;border-radius:50%;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                 style="width:30px;height:30px;border-radius:50%;">
                                <?php echo substr($rating['first_name'], 0, 1); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong><?php echo $rating['first_name'] . ' ' . $rating['last_name']; ?></strong>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?php echo ($i <= $rating['rating']) ? '-fill' : ''; ?> text-warning small"></i>
                        <?php endfor; ?>
                        <small class="text-muted">• <?php echo formatDate($rating['created_at']); ?></small>
                        
                        <?php if(!empty($rating['comment'])): ?>
                            <p class="mb-0"><?php echo $rating['comment']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
                
                <!-- Follow Button -->
                <?php if($user_id != $_SESSION['user_id']): ?>
                    <form method="post" action="follow_actions.php">
                        <input type="hidden" name="followed_id" value="<?php echo $user_id; ?>">
                        <?php if($is_following): ?>
                            <button type="submit" name="unfollow" class="btn btn-primary">Following</button>
                        <?php else: ?>
                            <button type="submit" name="follow" class="btn btn-outline-primary">Follow</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
                
                <!-- Bio -->
                <?php if(!empty($user['bio'])): ?>
                    <p class="mt-3"><?php echo $user['bio']; ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Rest of your profile content stays the same -->
        <!-- Create Post Form (only if viewing own profile) -->
        <?php if($user_id == $_SESSION['user_id']): ?>
            <div class="post-box">
                <h5>Create a Post</h5>
                <form action="create_post.php" method="post" enctype="multipart/form-data">
                    <textarea class="form-control mb-3" name="content" rows="3" placeholder="What's on your mind?" required></textarea>
                    <div class="mb-3">
                        <label for="image" class="form-label">Add Image (optional)</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <button type="submit" class="btn btn-success">Post</button>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Posts section remains unchanged -->
        <?php if(mysqli_num_rows($posts_result) > 0): ?>
            <?php while($post = mysqli_fetch_assoc($posts_result)): ?>
                <div class="post-box">
                    <!-- Post content and functionality remain the same -->
                    <!-- ... -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong><?php echo $user['first_name'] . " " . $user['last_name']; ?></strong>
                            <small class="text-muted">• <?php echo formatDate($post['created_at']); ?></small>
                        </div>
                    </div>
                    <p class="mt-2"><?php echo $post['content']; ?></p>
                    
                    <!-- Post Image -->
                    <?php if(!empty($post['image'])): ?>
    <div class="post-image-container">
        <img src="uploads/post_images/<?php echo $post['image']; ?>" class="post-image" alt="Post image">
    </div>
<?php endif; ?>
                    <!-- Like & Comment Buttons -->
                    <?php
                        $post_id = $post['id'];
                        $like_count = countLikes($conn, $post_id);
                        
                        // Check if user has liked this post
                        $like_check = mysqli_query($conn, "SELECT * FROM likes WHERE post_id = $post_id AND user_id = " . $_SESSION['user_id']);
                        $user_liked = mysqli_num_rows($like_check) > 0;
                        
                        // Get comments
                        $comments_query = "SELECT c.*, u.first_name, u.last_name FROM comments c 
                                        JOIN users u ON c.user_id = u.id 
                                        WHERE c.post_id = $post_id 
                                        ORDER BY c.created_at ASC";
                        $comments_result = mysqli_query($conn, $comments_query);
                    ?>
                    
                    <div class="d-flex gap-3">
                        <form method="post" action="post_actions.php" class="d-inline">
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <?php if($user_liked): ?>
                                <button type="submit" name="unlike" class="btn btn-sm btn-primary">
                                    👍 Liked (<?php echo $like_count; ?>)
                                </button>
                            <?php else: ?>
                                <button type="submit" name="like" class="btn btn-sm btn-outline-primary">
                                    👍 Like (<?php echo $like_count; ?>)
                                </button>
                            <?php endif; ?>
                        </form>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleComments(<?php echo $post_id; ?>)">
                            💬 Comments (<?php echo mysqli_num_rows($comments_result); ?>)
                        </button>
                    </div>
                    
                    <!-- Comments Section -->
                    <div id="comments-<?php echo $post_id; ?>" class="mt-3" style="display: none;">
                        <!-- Comment Form -->
                        <form method="post" action="post_actions.php" class="mb-3">
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="comment_content" placeholder="Write a comment..." required>
                                <button type="submit" name="add_comment" class="btn btn-outline-success">Post</button>
                            </div>
                        </form>
                        
                        <!-- Comments List -->
                        <?php while($comment = mysqli_fetch_assoc($comments_result)): ?>
                            <div class="comment">
                                <strong><?php echo $comment['first_name'] . " " . $comment['last_name']; ?></strong>
                                <small class="text-muted">• <?php echo formatDate($comment['created_at']); ?></small>
                                <p class="mb-0"><?php echo $comment['content']; ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">No posts yet.</div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle comments function
        function toggleComments(postId) {
            const commentsSection = document.getElementById(`comments-${postId}`);
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        }
        
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            
            // Handle search input
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                
                if (searchTerm.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                // Make AJAX request to search for users
                fetch('search_ajax.php?q=' + encodeURIComponent(searchTerm))
                    .then(response => response.json())
                    .then(data => {
                        // Clear previous results
                        searchResults.innerHTML = '';
                        
                        if (data.length > 0) {
                            // Add users to results
                            data.forEach(user => {
                                const item = document.createElement('div');
                                item.className = 'search-item';
                                
                                // Create profile picture element
                                let profilePic;
                                if (user.profile_pic && user.profile_pic !== 'default.jpg') {
                                    profilePic = `<img src="uploads/profile_pics/${user.profile_pic}" class="search-pic">`;
                                } else {
                                    const initials = user.name.split(' ').map(n => n[0]).join('').substring(0, 2);
                                    profilePic = `<div class="search-pic bg-secondary text-white d-flex align-items-center justify-content-center">${initials}</div>`;
                                }
                                
                                // Highlight matching text
                                const highlightedName = user.name.replace(
                                    new RegExp(searchTerm, 'gi'),
                                    match => `<span class="search-highlight">${match}</span>`
                                );
                                
                                item.innerHTML = `
                                    <a href="user_profile.php?id=${user.id}" class="d-flex align-items-center text-decoration-none text-dark w-100">
                                        ${profilePic}
                                        <div>
                                            <div>${highlightedName}</div>
                                            <small class="text-muted">${user.account_type || ''}</small>
                                        </div>
                                    </a>
                                `;
                                
                                searchResults.appendChild(item);
                            });
                            
                            searchResults.style.display = 'block';
                        } else {
                            // No results found
                            const noResults = document.createElement('div');
                            noResults.className = 'search-item text-center text-muted';
                            noResults.textContent = 'No users found';
                            searchResults.appendChild(noResults);
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(error => console.error('Error searching users:', error));
            });
            
            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
            
            // Show results when focusing on search input if it has content
            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2) {
                    searchResults.style.display = 'block';
                }
            });
        });
        // Add this to your existing script tag or create a new one
document.addEventListener('DOMContentLoaded', function() {
    // Star rating functionality 
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('selected-rating');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            
            // Update star visualization
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });
        
        // Hover effects
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                }
            });
        });
        
        star.addEventListener('mouseleave', function() {
            const selectedRating = parseInt(ratingInput.value) || 0;
            
            stars.forEach((s, index) => {
                if (index < selectedRating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });
    });
    
    // Submit rating
    const submitRatingBtn = document.getElementById('submitRating');
    if (submitRatingBtn) {
        submitRatingBtn.addEventListener('click', function() {
            const form = document.getElementById('ratingForm');
            const formData = new FormData(form);
            
            // Validate rating
            const rating = formData.get('rating');
            if (!rating || rating < 1) {
                alert('Please select a rating');
                return;
            }
            
            // Submit rating via AJAX
            fetch('save_rating.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('ratingModal'));
                    modal.hide();
                    
                    // Reload page to show updated rating
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save rating'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving your rating');
            });
        });
    }
    
    // Update search results to show ratings and allow rating from search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Modify the existing search code to show ratings
        const originalFetchHandler = searchInput.oninput;
        
        // Override the search results rendering to include ratings
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            
            if (searchTerm.length < 2) {
                document.getElementById('searchResults').style.display = 'none';
                return;
            }
            
            fetch('search_ajax.php?q=' + encodeURIComponent(searchTerm))
                .then(response => response.json())
                .then(data => {
                    const searchResults = document.getElementById('searchResults');
                    searchResults.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(user => {
                            const item = document.createElement('div');
                            item.className = 'search-item';
                            
                            // Create profile picture element
                            let profilePic;
                            if (user.profile_pic && user.profile_pic !== 'default.jpg') {
                                profilePic = `<img src="uploads/profile_pics/${user.profile_pic}" class="search-pic">`;
                            } else {
                                const initials = user.name.split(' ').map(n => n[0]).join('').substring(0, 2);
                                profilePic = `<div class="search-pic bg-secondary text-white d-flex align-items-center justify-content-center">${initials}</div>`;
                            }
                            
                            // Create stars for ratings
                            let stars = '';
                            for (let i = 1; i <= 5; i++) {
                                if (i <= user.avg_rating) {
                                    stars += '<i class="bi bi-star-fill text-warning"></i>';
                                } else if (i <= user.avg_rating + 0.5) {
                                    stars += '<i class="bi bi-star-half text-warning"></i>';
                                } else {
                                    stars += '<i class="bi bi-star text-warning"></i>';
                                }
                            }
                            
                            // Add rating button
                            const rateBtn = user.has_rated ? 
                                `<button class="btn btn-sm btn-primary rate-user-btn" data-user-id="${user.id}">Update Rating</button>` : 
                                `<button class="btn btn-sm btn-outline-primary rate-user-btn" data-user-id="${user.id}">Rate User</button>`;
                            
                            item.innerHTML = `
                                <div class="d-flex align-items-center w-100">
                                    <a href="user_profile.php?id=${user.id}" class="d-flex align-items-center text-decoration-none text-dark flex-grow-1">
                                        ${profilePic}
                                        <div>
                                            <div>${user.name}</div>
                                            <small>${user.account_type || ''}</small>
                                            <div class="small">
                                                ${stars} 
                                                <span class="text-muted">(${user.rating_count})</span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="ms-auto">
                                        ${rateBtn}
                                    </div>
                                </div>
                            `;
                            
                            searchResults.appendChild(item);
                        });
                        
                        // Add click handlers for rate buttons
                        document.querySelectorAll('.rate-user-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const userId = this.dataset.userId;
                                window.location.href = `user_profile.php?id=${userId}#rating`;
                            });
                        });
                        
                        searchResults.style.display = 'block';
                    } else {
                        const noResults = document.createElement('div');
                        noResults.className = 'search-item text-center text-muted';
                        noResults.textContent = 'No users found';
                        searchResults.appendChild(noResults);
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error searching users:', error));
        });
    }
});
    </script>
</body>
</html>