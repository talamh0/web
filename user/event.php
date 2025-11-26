<?php
session_start();
include 'config.php';

$userName = $_SESSION["name"] ?? "Guest";

// get event id
if (!isset($_GET['id'])) {
    die("No event ID specified.");
}
$event_id = (int)$_GET['id'];

// جلب بيانات الحدث من جدول events
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
if (!$event) die("Event not found.");

// حساب عدد التذاكر المتاحة بعد خصم الحجوزات
$stmtBooked = $conn->prepare("SELECT SUM(quantity) as booked FROM bookings WHERE event_id = ?");
$stmtBooked->bind_param("i", $event_id);
$stmtBooked->execute();
$resultBooked = $stmtBooked->get_result()->fetch_assoc();
$booked = $resultBooked['booked'] ?? 0;

// المتاح حالياً
$availableTickets = $event['available'] - $booked;
if ($availableTickets < 0) $availableTickets = 0;

$error_msg = "";
$success_msg = "";

// Add to cart logic
if (isset($_POST['add_to_cart'])) {

    $qty = (int)$_POST['qty'];

    // 1) check user is login
    if (!isset($_SESSION['user_id'])) {
        $error_msg = "⚠️ You must be logged in to add tickets to your cart.";
    }

    // 2) make sure 1 event is added to cart
    if (!$error_msg && !empty($_SESSION['cart'])) {
        $existingEventId = array_key_first($_SESSION['cart']);
        if ($existingEventId != $event_id) {
            $error_msg = "You can only book tickets for one event at a time. Please empty your cart first.";
        }
    }

    // 3) available tickets
    if (!$error_msg) {
        $existingQty = $_SESSION['cart'][$event_id] ?? 0;

        if ($qty + $existingQty > $availableTickets) {
            $remaining = $availableTickets - $existingQty;
            $error_msg = "Not enough tickets available! You can only add $remaining more tickets.";
        }
    }

    // 4) add to cart
    if (!$error_msg) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $_SESSION['cart'][$event_id] = $existingQty + $qty;
        $success_msg = "$qty tickets added to cart!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($event['name']); ?> - Event Booking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<div class="header-logo">
    <a href="home.php">
        <img src="images/logo.png" alt="Logo" class="logo">
    </a>
</div>

<div class="header-title">
    <a href="home.php">
        <span>Event Booking System</span>
    </a>
</div>

<div>
    <span>Welcome <?php echo htmlspecialchars($userName); ?>.</span>
    <a href="cart.php">Cart</a>
    <a href="logout.php">Logout</a>
</div>
</header>

<main>
    <section class="event-details">

        <!-- زر الرجوع للهوم -->
        <a href="home.php">
            <img src="images/back-arrow.png" alt="Back" style="width:20px; height:20px;">
        </a>

        <h1><?php echo htmlspecialchars($event['name']); ?></h1>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
        <p><strong>Price:</strong> <?php echo $event['price']; ?></p>
        <p><strong>Available:</strong> <?php echo $availableTickets; ?></p>

        <form method="POST">
            <label>Number of tickets:</label>
            <input type="number" name="qty" min="1" max="<?php echo $availableTickets; ?>" required>
            <button type="submit" name="add_to_cart">Add to Cart</button>
        </form>

        <?php if ($error_msg) { echo "<p class='error'>$error_msg</p>"; } ?>
        <?php if ($success_msg) { echo "<p class='success'>$success_msg</p>"; } ?>
    </section>
</main>

<footer>
    <p>© Event Booking System — <?php echo date("Y"); ?></p>
</footer>

</body>
</html>
