<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
include 'info.php'; //This will automatically give $profile_pic and $username

if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_sno'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $visibility = mysqli_real_escape_string($conn, $_POST['visibility']);

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

        // Move file
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $sql = "UPDATE users 
                    SET name='$new_username', visibility='$visibility', profile_picture='$target_file' 
                    WHERE sno='$user_id'";
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    } else {
        $sql = "UPDATE users 
                SET name='$new_username', visibility='$visibility' 
                WHERE sno='$user_id'";
    }

    mysqli_query($conn, $sql);
    echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
}

// Fetch updated data for display
$result = mysqli_query($conn, "SELECT * FROM users WHERE sno='$user_id'");
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Settings</title>
    <link rel="stylesheet" href="style1.css"> 
</head>
<body>
<div class="profile-container">
    <div class="profile-header">
        <h2>Profile Settings</h2>
        <p>Update your personal details and privacy settings</p>
    </div>

    <div class="profile-picture">
        <img src="<?= $user['profile_picture'] ?: 'default.png' ?>" alt="Profile Picture">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="profile-details">

            <div class="profile-card">
                <h4>Profile Picture</h4>
                <input type="file" name="profile_picture">
            </div>

            <div class="profile-card">
                <h4>Username</h4>
                <input type="text" name="username" value="<?= $user['name'] ?>" placeholder="Enter new username">
            </div>

            <div class="profile-card">
                <h4>Visibility</h4>
                <select name="visibility">
                    <option value="private" <?= $user['visibility']=='private'?'selected':'' ?>>Private</option>
                    <option value="public" <?= $user['visibility']=='public'?'selected':'' ?>>Public</option>
                </select>
            </div>
        </div>

        <div class="profile-actions">
            <button type="submit" class="btn-profile">Save Changes</button>
            <a href="main.php" class="btn-back">Back to Home</a>
        </div>
    </form>
</div>
</body>
</html>