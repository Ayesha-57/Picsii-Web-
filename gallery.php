<?php
session_start();
include 'db.php';
include 'info.php';

if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_sno'];

$query = "
    SELECT p.*, u.name 
    FROM pics p
    JOIN users u ON p.user_sno = u.sno
    WHERE u.visibility = 'public' OR p.user_sno = '$user_id'
";

if (isset($_GET['delete'])) {
    $file = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM pics WHERE user_sno=? AND file_name=?");
    $stmt->bind_param("is", $user_id, $file);
    $stmt->execute();
    $stmt->close();
    if (file_exists("images/$file")) {
        unlink("images/$file");
    }
    header("Location: gallery.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Gallery</title>
  <link rel="stylesheet" href="style1.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">MyGallery</div>
    <ul>
        <li><a href="main.php">Home</a></li>
        <li><a href="upload.php">Upload</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
        <div class="nav-profile">
            <a href="profile.php">
                <img src="<?= $profile_pic ?>" alt="Profile" class="nav-pic">
            </a>
        </div>
    </ul>
</nav>

<div class="gallery-container">
  <h2>Your Uploaded Images</h2>

  <div class="gallery-grid">
    <?php
    $result = $conn->query($query);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    if ($result->num_rows == 0) {
        echo "<p class='no-images'>No images found for user $users_sno</p>";
    }

    while ($row = $result->fetch_assoc()) {
        $file = htmlspecialchars($row['file_name']);
        echo "
        <div class='gallery-card'>
          <img src='images/$file' alt='User Image'>
          <div class='gallery-actions'>
            <a href='images/$file' download class='btn-gallery'>Download</a>
            <a href='gallery.php?delete=$file' onclick='return confirm(\"Delete this image?\")' class='btn-gallery delete'>Delete</a>
          </div>
        </div>";
    }
    ?>
  </div>
</div>
</body>
</html>
