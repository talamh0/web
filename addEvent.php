<?php
// addEvent.php
session_start();

// حماية صفحة الأدمن
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin.php");
    exit();
}

// ربط قاعدة البيانات
include("database/config.php");

$error = "";
$success = "";

if (isset($_POST['add'])) {

    $name        = trim($_POST['name']);
    $date_time   = trim($_POST['date_time']);
    $location    = trim($_POST['location']);
    $price       = $_POST['price'];
    $max_tickets = $_POST['max_tickets'];
    $image       = trim($_POST['image']); // اختياري (اسم ملف الصورة فقط)

    // تحقق بسيط
    if ($name === "" || $date_time === "" || $location === "" || $price === "" || $max_tickets === "") {
        $error = "All fields except image are required.";
    } else {
        // تحويل date_time لصيغة MySQL لو احتجنا
        // هنا نثق إن الـ input type="datetime-local" راح يرجع صيغة مناسبة
        $stmt = $conn->prepare("INSERT INTO events (name, date_time, location, price, max_tickets, image)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdis", $name, $date_time, $location, $price, $max_tickets, $image);
        
        if ($stmt->execute()) {
            // بعد الإضافة، رجّعيه لصفحة إدارة الفعاليات
            header("Location: manageEvents.php");
            exit();
        } else {
            $error = "Error while adding event.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Event</title>
    <!-- لو فولدر css داخل web، خليه css/style.css -->
    <link rel="stylesheet" href="css/style.css">
    </head>
<body>

<?php include "admin_sidebar.php"; ?>

<main class="main-content">
    <h2>Add New Event</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Event Name</label>
        <input type="text" name="name" required>

        <label>Date &amp; Time</label>
        <input type="datetime-local" name="date_time" required>

        <label>Location</label>
        <input type="text" name="location" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" required>

        <label>Maximum Tickets</label>
        <input type="number" name="max_tickets" required>

        <label>Image (optional - file name only)</label>
        <input type="text" name="image" placeholder="example.jpg">

        <button type="submit" name="add" class="btn btn-primary">Add Event</button>
    </form>

</main>

</div>
</body>
</html>
