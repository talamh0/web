<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role = $_POST['role'] ?? 'user';

    if ($role === 'admin') {
        header("Location: web/admin.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select Role | Event Booking System</title>
<link rel="stylesheet" href="style1.css">
</head>
<body>

<div class="role-box">
    <h2>Select Role</h2>
    <form method="POST" action="">
        <div class="switch-wrapper">
            <input type="radio" name="role" value="user" id="user" checked>
            <input type="radio" name="role" value="admin" id="admin">
            <label for="user" class="switch-label">User</label>
            <label for="admin" class="switch-label">Admin</label>
            <span class="slider"></span>
        </div>

        <button type="submit">Continue</button>
    </form>
</div>

</body>
</html>
