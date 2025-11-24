<?php
session_start();
if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';

if (!isset($_GET['id'])) {
    die("No image selected.");
}

$image_id = intval($_GET['id']);

// Fetch file name before deleting
$query = mysqli_query($conn, "SELECT * FROM pics WHERE sno = '$image_id' LIMIT 1");
if ($query && mysqli_num_rows($query) > 0) {
    $image = mysqli_fetch_assoc($query);
    $filePath = "images/" . $image['file_name'];

    // Delete from DB
    mysqli_query($conn, "DELETE FROM pics WHERE sno = '$image_id'");

    // Delete from folder if exists
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Redirect to gallery
    header("Location: gallery.php?deleted=1");
    exit();
} else {
    die("Image not found.");
}
?>
