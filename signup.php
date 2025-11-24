<?php
// signup.php

session_start();
include 'db.php'; // Ensures $conn is available

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve inputs
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $age = (int)($_POST['age'] ?? 0); // Ensure age is an integer

    // --- 1. Basic Validations (Good!) ---
    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $name)) {
        $errors[] = "Username must be 3-20 characters, only letters, numbers, underscores allowed.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors[] = "Phone must be 10–15 digits.";
    }
    if (empty($first_name) || empty($last_name)) {
        $errors[] = "First and last name cannot be empty.";
    }
    // Added a check for age
    if ($age <= 0 || $age > 120) {
        $errors[] = "Please enter a valid age.";
    }

    // --- 2. Uniqueness Check (Crucial for Sign-up) ---
    if (empty($errors)) {
        // Check if username already exists
        $stmt_check = $conn->prepare("SELECT sno FROM users WHERE name = ? OR email = ? LIMIT 1");
        $stmt_check->bind_param("ss", $name, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            // Check which field is duplicate
            $existing_user = $result_check->fetch_assoc();
            $errors[] = "Username or Email already registered.";
        }
        $stmt_check->close();
    }


    // --- 3. Database Insertion ---
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // SQL: Use backticks if your table is called 'user' (it is users, but good practice).
        // CRITICAL FIX: The data types in bind_param were 'ssssssi' but should be 'ssssssi'.
        // Wait, your original was correct: ssssssi (7 strings, 1 integer)
        // Check your database: is 'phone' a string (VARCHAR) or an integer? 
        // Based on your regex, it should be a string, so we keep 's' for phone.
        $stmt = $conn->prepare("INSERT INTO users (name, password, first_name, last_name, email, phone, age) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // ssssssi: name(s), password(s), first_name(s), last_name(s), email(s), phone(s), age(i)
        $stmt->bind_param("ssssssi", $name, $hashedPassword, $first_name, $last_name, $email, $phone, $age); 

        if ($stmt->execute()) {
            $_SESSION['user_sno'] = $stmt->insert_id;
            $_SESSION['name'] = $name;
            header("Location: main.php");
            exit();
        } else {
            // A more detailed error for debugging (e.g., if the unique index fails)
            $errors[] = "Registration failed. Database Error: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
        // DO NOT close the $conn here, as it might be needed for rendering the form!
        // $conn->close(); 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<div class="signup-container">
    <h2>Create Account</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="signup-form">
        <input type="text" name="name" placeholder="Enter username" value="<?= htmlspecialchars($name ?? '') ?>" required>
        <input type="password" name="password" placeholder="Enter password" required>
        <input type="password" name="confirm_password" placeholder="Confirm password" required>
        <input type="text" name="first_name" placeholder="Enter first name" value="<?= htmlspecialchars($first_name ?? '') ?>" required>
        <input type="text" name="last_name" placeholder="Enter last name" value="<?= htmlspecialchars($last_name ?? '') ?>" required>
        <input type="email" name="email" placeholder="Enter email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        <input type="text" name="phone" placeholder="Enter phone number" value="<?= htmlspecialchars($phone ?? '') ?>" required>
        <input type="number" name="age" placeholder="Enter age" value="<?= htmlspecialchars($age ?? '') ?>" required min="1" max="120">
        <button type="submit" class="btn-signup">Signup</button>
    </form>
</div>
</body>
</html>