<?php
session_start();

if(isset($_SESSION['admin_logged_in'])){
    header("Location: manageEvents.php");
    exit();
}

$error = "";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username === "admin" && $password === "admin123"){
        $_SESSION['admin_logged_in'] = true;
        header("Location: manageEvents.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
    </head>
<body>

<div class="container" style="max-width: 400px; margin-top: 80px;">

    <h2 style="text-align:center;">Admin Login</h2>

    <?php if($error): ?>
        <p style="color:red; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit" name="login" class="btn btn-primary" style="width:100%;">Login</button>
    </form>
</div>

</body>
</html>

