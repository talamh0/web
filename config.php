<?php
// database/config.php

$host = "localhost";
$user = "root";
$pass = "root";       // هذا الافتراضي في MAMP، لو مغيرته عدليه
$db   = "event_booking";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
