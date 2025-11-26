<?php
include 'config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirm"]);

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Email is already registered.";
    }

    if (count($errors) === 0) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password)
                VALUES ('$name', '$email', '$hashed')";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?registered=1");
            exit();
        } else {
            $errors[] = "An error occurred during registration.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-box">
    <h2>Create Account</h2>

    <?php
    if (!empty($errors)) {
        echo "<div class='error'>";
        foreach ($errors as $e) echo "<p>$e</p>";
        echo "</div>";
    }
    ?>

    <form method="POST" action="">
        <input type="text" name="name" placeholder="Full Name">
        <input type="email" name="email" placeholder="Email Address">
        <input type="password" name="password" placeholder="Password">
        <input type="password" name="confirm" placeholder="Confirm Password">
        <button type="submit">Register</button>
    </form>

    <div class="link">
        Already have an account? <a href="index.php">Login here</a>
    </div>
</div>

</body>
</html>