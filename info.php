<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// Check if the user is logged in
if (isset($_SESSION['user_sno'])) {
    $user_id = $_SESSION['user_sno'];
    $user_query = mysqli_query($conn, "SELECT name, profile_picture FROM users WHERE sno='$user_id'");
    $user_data = mysqli_fetch_assoc($user_query);

    $username = $user_data['name'] ?? 'Guest';
    $profile_pic = !empty($user_data['profile_picture']) ? $user_data['profile_picture'] : 'images/default.png';
} else {
    $username = 'Guest';
    $profile_pic = 'images/default.png';
}
?>
