<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ header("Location: admin.php"); exit(); }
include("database/config.php");


$sql = "SELECT * FROM events ORDER BY date_time ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Events</title>
    <link rel="stylesheet" href="css/style.css">
    </head>
<body>

<?php include "admin_sidebar.php"; ?>

<main class="main-content">
    <h2>Manage Events</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Event</th>
                <th>Date & Time</th>
                <th>Location</th>
                <th>Price</th>
                <th>Tickets</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['date_time']); ?></td>
                    <td><?= htmlspecialchars($row['location']); ?></td>
                    <td><?= htmlspecialchars($row['price']); ?></td>
                    <td><?= htmlspecialchars($row['max_tickets']); ?></td>

                    <td>
                        <a href="viewEvent.php?id=<?= $row['id'] ?>">View</a> |
                        <a href="editEvent.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="deleteEvent.php?id=<?= $row['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No events found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>

</div>
</body>
</html>
