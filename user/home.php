<?php
session_start();
include 'config.php';


$userName = $_SESSION["name"] ?? "Guest";
// جلب اسم المستخدم


// جلب كل الأحداث من قاعدة البيانات
$events = [];
$query = "SELECT * FROM events";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Event Booking System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<div class="header-logo">
        <img src="images/logo.png" alt="Logo" class="logo">
    </a>
</div>

<div class="header-title">
        <span>Event Booking System</span>
    </a>
</div>
    <div class="user-actions">
        <span>Welcome <?php echo htmlspecialchars($userName); ?>.</span>
        <a href="cart.php">Cart</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<main>
    <section class="events-grid">
        <?php foreach($events as $event) { ?>
            <div class="event-card">
                <img src="<?php echo htmlspecialchars($event['image'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($event['name']); ?>">
                <div class="event-info">
                    <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                    <p><?php echo htmlspecialchars($event['event_date']); ?></p>
                </div>
                <form method="GET" action="event.php">
                    <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                    <button type="submit">Book Now</button>
                </form>
            </div>
        <?php } ?>
    </section>
</main>

<footer>
    <p>© Event Booking System — <?php echo date("Y"); ?></p>
</footer>

</body>
</html>
