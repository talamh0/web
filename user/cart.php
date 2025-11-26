<?php
session_start();
require 'config.php';

// user must be logged in to access cart
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?login_required=1");
    exit();
}

// get user name
$userName = $_SESSION['name'];

// get cart items
$cart = $_SESSION['cart'] ?? [];
$totalPrice = 0;
$cartItems = [];
$success_msg = "";

// reserve tickets
if (isset($_POST['reserve']) && $cart) {
    foreach ($cart as $event_id => $qty) {

        // 2) جلب سعر الحدث
        $stmtPrice = $conn->prepare("SELECT price FROM events WHERE id=?");
        $stmtPrice->bind_param("i", $event_id);
        $stmtPrice->execute();
        $priceResult = $stmtPrice->get_result()->fetch_assoc();
        $price = $priceResult['price'];
        $total = $qty * $price;

        // 3) إضافة الحجز لجدول bookings
        $stmtBooking = $conn->prepare("INSERT INTO bookings (user_id, event_id, quantity, total_price, booking_date) VALUES (?, ?, ?, ?, NOW())");
        $stmtBooking->bind_param("iiid", $_SESSION['user_id'], $event_id, $qty, $total);
        $stmtBooking->execute();
    }

    $_SESSION['cart'] = [];
    $cart = [];
    $success_msg = "Booking confirmed! Tickets reserved.";
}

// بعد عملية الحجز، جلب كل حجوزات هذا اليوزر
$bookings = [];
$stmtAll = $conn->prepare("
    SELECT b.id, b.user_id, b.event_id, b.quantity, b.total_price, b.booking_date, e.name as event_name
    FROM bookings b
    JOIN events e ON b.event_id = e.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmtAll->bind_param("i", $_SESSION['user_id']);
$stmtAll->execute();
$resultAll = $stmtAll->get_result();
while ($row = $resultAll->fetch_assoc()) {
    $bookings[] = $row;
}


// get event information
if ($cart) {
    $ids = implode(',', array_keys($cart));
    $query = "SELECT * FROM events WHERE id IN ($ids)";
    $result = $conn->query($query);

    while ($event = $result->fetch_assoc()) {
        $qty = $cart[$event['id']];
        $price = $event['price'];
        $total = $qty * $price;
        $totalPrice += $total;

        $cartItems[] = [
            'name' => $event['name'],
            'date' => $event['event_date'],
            'qty' => $qty,
            'price' => $price,
            'total' => $total
        ];
    }
}

// Get Date & Time
$currentDateTime = date("Y-m-d H:i:s");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - Event Booking</title>
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
    <section class="cart-section">

        <a href="home.php">
        <img src="images/back-arrow.png" alt="Back" style="width:20px; height:20px;">
        </a>

        <h1>Your Cart</h1>
        <p><strong>Current Date & Time:</strong> <?php echo $currentDateTime; ?></p>

        <?php if ($cartItems) { ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($cartItems as $item) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['date']); ?></td>
                            <td><?php echo $item['qty']; ?></td>
                            <td><?php echo number_format($item['price'],2); ?> $</td>
                            <td><?php echo number_format($item['total'],2); ?> $</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <p><strong>Total Price:</strong> <?php echo number_format($totalPrice, 2); ?> $</p>

            <form method="POST">
                <button type="submit" name="reserve">Reserve Tickets</button>
            </form>

            <form method="POST" style="display:inline;">
                <button type="submit" name="empty_cart" class="btn-empty">Empty Cart</button>
            </form>

            <?php if ($success_msg) { ?>
                <p class="message success"><?php echo htmlspecialchars($success_msg); ?></p>
            <?php } ?>

        <?php } else { ?>
            <p>Your cart is empty.</p>
        <?php } ?>
    </section>

    <section>
    <?php if ($bookings): ?>
    <h2>Your Bookings</h2>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Event</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Booking Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= $b['id']; ?></td>
                    <td><?= htmlspecialchars($b['event_name']); ?></td>
                    <td><?= $b['quantity']; ?></td>
                    <td><?= number_format($b['total_price'],2); ?> $</td>
                    <td><?= $b['booking_date']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</section>

</main>

<footer>
    <p>© Event Booking System — <?php echo date("Y"); ?></p>
</footer>

</body>
</html>