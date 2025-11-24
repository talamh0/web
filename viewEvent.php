<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ header("Location: admin.php"); exit(); }
include("database/config.php");


$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Event</title>
    <link rel="stylesheet" href="css/style.css">
    </head>
<body>

<?php include "admin_sidebar.php"; ?>

<main class="main-content">
    <h2>View Event</h2>

    <?php if($event): ?>
        <p><b>Name:</b> <?= $event['name']; ?></p>
        <p><b>Date Time:</b> <?= $event['date_time']; ?></p>
        <p><b>Location:</b> <?= $event['location']; ?></p>
        <p><b>Price:</b> <?= $event['price']; ?></p>
        <p><b>Max Tickets:</b> <?= $event['max_tickets']; ?></p>
    <?php else: ?>
        <p>Event not found.</p>
    <?php endif; ?>
</main>

</div>
</body>
</html>
