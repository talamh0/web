<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ header("Location: admin.php"); exit(); }
include("database/config.php");


$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

$error = "";

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $dt   = $_POST['date_time'];
    $loc  = $_POST['location'];
    $price= $_POST['price'];
    $max  = $_POST['max_tickets'];

    if($name === "" || $dt === "" || $loc === ""){
        $error = "All fields are required.";
    } else {
        $stmt2 = $conn->prepare("UPDATE events SET name=?, date_time=?, location=?, price=?, max_tickets=? WHERE id=?");
        $stmt2->bind_param("sssddi", $name, $dt, $loc, $price, $max, $id);
        $stmt2->execute();

        header("Location: manageEvents.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="css/style.css">
    </head>
<body>
<?php include "admin_sidebar.php"; ?>

<main class="main-content">
<h2>Edit Event</h2>

<?php if($error): ?>
    <p style="color:red;"><?= $error; ?></p>
<?php endif; ?>

<form method="POST">

    <label>Name</label>
    <input type="text" name="name" value="<?= $event['name']; ?>" required>

    <label>Date & Time</label>
    <input type="datetime-local" name="date_time"
     value="<?= date('Y-m-d\TH:i', strtotime($event['date_time'])); ?>" required>

    <label>Location</label>
    <input type="text" name="location" value="<?= $event['location']; ?>" required>

    <label>Price</label>
    <input type="number" name="price" value="<?= $event['price']; ?>" required>

    <label>Max Tickets</label>
    <input type="number" name="max_tickets" value="<?= $event['max_tickets']; ?>" required>

    <button type="submit" name="update" class="btn btn-primary">Update Event</button>

</form>

</main>
</body>
</html>
