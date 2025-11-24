<?php
session_start();
include 'db.php'; 

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? ''; 
    
    if (empty($name) || empty($password)) {
        $error = "Both username and password are required!";
    } 
    elseif (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $name)) {
        $error = "Username must be 3–20 characters (letters, numbers, underscores allowed)";
    } 
    else {
        $stmt = $conn->prepare("SELECT sno, name, password FROM users WHERE name = ?");
        if ($stmt === false) {
             $error = "Database error: Could not prepare statement.";
        } else {
             $stmt->bind_param("s", $name);
             $stmt->execute();
             $result = $stmt->get_result();
             $row = $result->fetch_assoc();
             if ($row && password_verify($password, $row['password'])) {
                 $_SESSION['user_sno'] = $row['sno'];
                 $_SESSION['name'] = $row['name'];
                 header("Location: main.php"); 
                 exit();
                 
             } else {
                 $error = "Invalid username or password!";
             }
             $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Page</title>
    <link rel = "stylesheet" href = "style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx"
        crossorigin="anonymous" />
</head>
<body>
    <section class="login-section py-5 bg-light">
        <div class="container">
            <div class="row g-0">
                <div class="col-lg-5">
                    <img src="images/cfe05099-7486-47f8-9436-2ed5d827a388_removalai_preview.png" class="img-fluid" alt="Login Image">
                </div>
                <div class="col-lg-7 text-center py-5">
                    <h1 class="animate__animated animate__backInDown mb-4">Welcome to Picsi!</h1>

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Username" 
                                required 
                                pattern="[A-Za-z0-9_]{3,20}" 
                                title="3–20 characters, only letters, numbers, underscores allowed.">
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Password" 
                                required 
                                minlength="6">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>

                    <p class="mt-3">Don't have an account? <a href="signup.php" class="btn btn-success btn-sm" role ="button">Sign up here</a></p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>