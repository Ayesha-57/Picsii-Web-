<?php
session_start();
include 'db.php';
include 'info.php';

if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $user_sno = $_SESSION['user_sno'];
    $target_dir = "images/";

    $original_file_name = basename($_FILES["image"]["name"]);
    $file_extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
    $safe_file_name = uniqid('img_', true) . '.' . strtolower($file_extension);
    $target_file = $target_dir . $safe_file_name;

    $file_size = $_FILES["image"]["size"];
    $mime_type = $_FILES["image"]["type"];
    $uploadOk = 1;
    $maxFileSize = 5000000; // 5MB limit

    if ($file_size > $maxFileSize) {
        $_SESSION['upload_message'] = "Sorry, your file is too large (max 5MB).";
        $uploadOk = 0;
    }

//  Allow certain file formats (MIME types)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime_type, $allowed_types)) {
        $_SESSION['upload_message'] = "Sorry, only JPG, JPEG, PNG, GIF, and WEBP files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
    } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO pics (user_sno, file_name, file_size, mime_type,file_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $_SESSION['upload_message'] = "Prepare failed: " . $conn->error;
            unlink($target_file); 
        } else {
            $stmt->bind_param("isiss", $user_sno, $safe_file_name, $file_size,$mime_type, $target_file,);

            if ($stmt->execute()) {
                $_SESSION['upload_message'] = "Image uploaded successfully!";
            } else {
                $_SESSION['upload_message'] = "Database error: " . $stmt->error;
                unlink($target_file); 
            }
            $stmt->close();
        }
    } else {
        $_SESSION['upload_message'] = "Failed to upload file. Check folder permissions.";
    }
    
    header("Location: upload.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Image</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">MyGallery</div>
    <ul>
        <li><a href="main.php">Home</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
        <div class="nav-profile">
            <a href="profile.php">
                <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" class="nav-pic">
            </a>
        </div>
    </ul>
</nav>

<div class="upload-container">
    <div class="upload-card">
        <h2>Upload an Image</h2>
        <?php
        if (isset($_SESSION['upload_message'])) {
            $color = (strpos($_SESSION['upload_message'], 'successfully') !== false) ? 'green' : 'red';
            echo "<p style='color:{$color};text-align:center'>" . htmlspecialchars($_SESSION['upload_message']) . "</p>";
            unset($_SESSION['upload_message']);
        }
        ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="fileUpload">Choose Image (Max 5MB)</label>
            <input type="file" name="image" id="fileUpload" required>
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>
</div>
</body>
</html>