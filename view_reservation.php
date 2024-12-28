<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotelms";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check session status and start session if needed
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Retrieve reservations for the logged-in user
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your reservations.");
}

$user_id = $_SESSION['user_id'];

// Query reservations
$sql = "SELECT * FROM reservations WHERE user_id = ? ORDER BY check_in_date DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Your Reservation Details</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Room Number</th>
            <th>Check-In Date</th>
            <th>Check-Out Date</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0) { 
            $count = 1;
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['check_out_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php } 
        } else { ?>
            <tr>
                <td colspan="5" class="text-center">No reservations found.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close connection
$stmt->close();
$conn->close();
?>
