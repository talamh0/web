<?php
session_start();
include 'config.php';

$error = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
            $query = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                if (!empty($row["password"]) && password_verify($password, $row["password"])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['name']    = $row['name'];
                    header("Location: home.php");
                    exit();
                } else {
                    $error = "Wrong password.";
                }
            } else {
                $error = "User not found.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Event Booking System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="auth-box">
    <h1>page name</h1>
    <h3>Login</h3>

    <?php
    if (isset($_GET["registered"])) {
        echo "<div class='success'>Account created successfully. You can now login.</div>";
    }

    if (isset($_GET["login_required"])) {
        echo "<div class='error'>Please login to continue.</div>";
    }

    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    }
    ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email Address">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>

    <div class="link">
        Not a member? <a href="register.php">Create an account</a>
    </div>
</div>

</body>
</html>