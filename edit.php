<?php
session_start();
include 'db.php'; 
include 'info.php';
if (!isset($_SESSION['user_sno'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_sno'];

$result = mysqli_query($conn, "SELECT visibility FROM users WHERE sno='$user_id'");
if (!$result || mysqli_num_rows($result) == 0) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($result);

if ($user['visibility'] === 'public') {
    die("You cannot edit photos in public mode for security reasons.");
}

if (!isset($_GET['id'])) {
    die("Image not found.");
}

$image_id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM pics WHERE sno = '$image_id' LIMIT 1");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Image not found in database.");
}

$image = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Image</title>
    <link rel="stylesheet" href="style1.css">

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h2 class="logo">Gallery System</h2>
        <ul>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="upload.php">Upload</a></li>
            <li><a href="view.php?id=<?php echo $image['sno']; ?>">View Image</a></li>
            <li><a href="#" class="active">Edit</a></li>
            <div class="nav-profile">
        <a href="profile.php">
    <img src="<?= $profile_pic ?>" alt="Profile" class="nav-pic">
</a>
</div>
        </ul>
    </div>

    <div class="edit-container">
        <h2>Edit Image</h2>

        <!-- Editable Image -->
        <img id="editableImage" src="images/<?php echo htmlspecialchars($image['file_name']); ?>" alt="Image" class="large-image">

        <!-- Replace Image Form -->
        <form id="updateForm" action="update.php?id=<?php echo $image['sno']; ?>" method="post" enctype="multipart/form-data">
            <label for="file">Replace Image:</label>
            <input type="file" name="file" id="file" accept="image/*">

            <!-- Hidden field to store cropped image data -->
            <input type="hidden" name="cropped_image" id="cropped_image">

            <div class="edit-tools">
                <button type="button" id="cropBtn">Crop</button>
                <button type="button" id="rotateBtn">Rotate</button>
                <button type="button" id="resetBtn">Reset</button>
                <button type="submit" class="btn">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
let image, cropper;

window.addEventListener('load', () => {
    image = document.getElementById('editableImage');
    cropper = new Cropper(image, {
        aspectRatio: NaN,
        viewMode: 1,
        autoCropArea: 1,
    });

    const input = document.getElementById('file');
    const form = document.getElementById('updateForm');
    const hiddenInput = document.getElementById('cropped_image');

    // Replace image if new one is selected
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                cropper.replace(event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Rotate image
    document.getElementById('rotateBtn').addEventListener('click', () => {
        cropper.rotate(90);
    });

    // Reset image
    document.getElementById('resetBtn').addEventListener('click', () => {
        cropper.reset();
    });

    // When submitting form → generate cropped image
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const canvas = cropper.getCroppedCanvas({
            width: image.naturalWidth,
            height: image.naturalHeight,
        });

        if (canvas) {
            hiddenInput.value = canvas.toDataURL('image/jpeg');
        } else {
            alert("Please select or crop the image first.");
            return;
        }

        // Submit form after setting cropped image
        form.submit();
    });
});
</script>

</body>
</html>
