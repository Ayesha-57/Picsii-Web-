<?php
session_start();
if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';
include 'info.php';
if (!isset($_GET['id'])) {
    die("Image not found.");
}

$image_id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM pics WHERE sno = '$image_id' LIMIT 1");

if ($query && mysqli_num_rows($query) > 0) {
    $image = mysqli_fetch_assoc($query);
} else {
    die("Image not found in database.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Image</title>
    <link rel="stylesheet" href="style1.css">
    </head>
<body>

    <!-- Navbar -->
<div class="navbar">
    <h2 class="logo">Gallery System</h2>
    <ul>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="upload.php">Upload</a></li>
        <li><a href="edit.php?id=<?php echo $image['sno']; ?>" class="active">Edit</a></li>
        <div class="nav-profile">
      <a href="profile.php">
          <img src="<?= $profile_pic ?>" alt="Profile" class="nav-profile-pic">
        </a>
  </div>
    </ul>
</div>

    <!-- Image View -->
    <div class="view-container">
        <div class="image-title"><?php echo htmlspecialchars($image['file_name']); ?></div>
        <img src="images/<?php echo htmlspecialchars($image['file_name']); ?>" alt="Image" class="large-image">
        
        <div class="image-actions">
            <a href="gallery.php" class="btn-gallery">Back to Gallery</a>
            <a href="edit.php?id=<?php echo $image['sno']; ?>" class="btn-upload">Edit</a>
            <a href="delete.php?id=<?php echo $image['sno']; ?>" class="btn-upload delete" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
        </div>
    </div>

</body>
</html>


