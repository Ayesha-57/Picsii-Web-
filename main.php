<?php
session_start();
if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php"); 
    exit();
}

include 'db.php'; 
include 'info.php'; 
$sno = $_SESSION['user_sno'];
$name = "Guest"; 

$stmt_user = $conn->prepare("SELECT name FROM users WHERE sno = ? LIMIT 1");
if ($stmt_user) {
    $stmt_user->bind_param("i", $sno); 
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    
    if ($userRow = $result_user->fetch_assoc()) {
        $name = $userRow['name'];
        $_SESSION['name'] = $name; 
    }
    $stmt_user->close();
} else {
}

$images = null;
$stmt_images = $conn->prepare("SELECT sno, file_name FROM pics WHERE user_sno = ? ORDER BY sno DESC");
if ($stmt_images) {
    $stmt_images->bind_param("i", $sno);
    $stmt_images->execute();
    $images = $stmt_images->get_result();
    
    // Check for query failure
    if (!$images) {
        die("Image query failed: " . $stmt_images->error);
    }
    // closing the statement AFTER getting the result
    $stmt_images->close(); 
}

$username = $_SESSION['name'] ?? $name; // Use the fetched name
$profile_pic = $profile_pic ?? 'images/default_profile.png'; // Define a default if info.php is missing or doesn't set it
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">HOME</div>
        <ul>
            <li><a href="upload.php">Upload</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
            <div class="nav-profile">
                <a href="profile.php">
                    <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" class="nav-profile-pic">
                </a>
            </div>
        </ul>
    </nav>

    <div class="container-home">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>

        <div class="gallery-preview">
            <?php 
            // Check if the query returned results
            if ($images && $images->num_rows > 0) : 
                while($row = $images->fetch_assoc()): 
            ?>
                <div class="image-card">
                    <img src="images/<?php echo rawurlencode($row['file_name']); ?>" alt="User Image">
                    <a href="view.php?id=<?php echo htmlspecialchars($row['sno']); ?>" class="btn-gallery">View Image</a>
                </div>
            <?php 
                endwhile; 
            else : 
            ?>
                <p>No images uploaded yet. Why not <a href="upload.php">upload one</a>?</p>
            <?php 
            endif;
            // The image result object will be closed automatically, but we can explicitly close it:
            if ($images) $images->free(); 
            ?>
        </div>
    </div>

</body>
</html>