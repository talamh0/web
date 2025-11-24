<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ header("Location: admin.php"); exit(); }
include("database/config.php");


$sql = "
SELECT b.*, u.name AS uname, u.email, e.name AS ename, e.date_time 
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN events e ON b.event_id = e.id
ORDER BY b.booking_date DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>View Bookings</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "admin_sidebar.php"; ?>

<main class="main-content">
<h2>All Bookings</h2>

<table class="table">
<thead>
<tr>
<th>User</th>
<th>Email</th>
<th>Event</th>
<th>Event Date</th>
<th>Tickets</th>
<th>Total Price</th>
</tr>
</thead>
<tbody>

<?php if($result && $result->num_rows > 0): ?>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['uname']; ?></td>
<td><?= $row['email']; ?></td>
<td><?= $row['ename']; ?></td>
<td><?= $row['date_time']; ?></td>
<td><?= $row['quantity']; ?></td>
<td><?= $row['total_price']; ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="6">No bookings yet.</td></tr>
<?php endif; ?>

</tbody>
</table>

</main>
</body>
</html>
