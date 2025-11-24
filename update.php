<?php
session_start();
include 'db.php';
include 'info.php';


if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

if (!empty($_POST['cropped_image'])) {
    $data = $_POST['cropped_image'];
    $data = str_replace('data:image/jpeg;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    $decoded = base64_decode($data);

    $file_name = 'edited_' . time() . '.jpg';
    $file_path = 'images/' . $file_name;
    file_put_contents($file_path, $decoded);

    // Update database
    $stmt = $conn->prepare("UPDATE pics SET file_name=?, file_path=? WHERE sno=?");
    $stmt->bind_param("ssi", $file_name, $file_path, $id);
    $stmt->execute();

    header("Location: view.php?id=$id");
    exit();
} else {
    echo "No cropped image data received.";
}
?>
