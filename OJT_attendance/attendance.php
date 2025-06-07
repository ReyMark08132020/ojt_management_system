<?php
session_start();
include_once('db/db-con.php'); // Ensure connection to DB
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "
        <script>
            alert('User not logged in.');
            window.location.href = 'index.php';
        </script>";
    exit();
}

$email = $_SESSION['email'];
$label = "";

// Fetch attendance settings for the logged-in user
$query = "SELECT `time_in`, `time_out`, `max_overtime` FROM `establishment_settings` WHERE `user_email` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $timeIn = $row['time_in'];
    $timeOut = $row['time_out'];
    $maxOvertime = $row['max_overtime']; // Max overtime in minutes

    // Get the current time
    $currentTime = date("H:i:s");

    // Convert time strings to DateTime objects for comparison
    $timeInObj = DateTime::createFromFormat('H:i:s', $timeIn);
    $timeOutObj = DateTime::createFromFormat('H:i:s', $timeOut);
    $currentTimeObj = DateTime::createFromFormat('H:i:s', $currentTime);

    // Overtime calculation: Add overtime to the timeOut
    // Create an overtime limit based on max overtime (in minutes)
$overtimeLimit = clone $timeOutObj;
$overtimeLimit->modify("+{$maxOvertime} minutes");  // Add max overtime minutes

// Create a timeOutLimit (30 minutes before actual time_out)
$timeOutLimit = clone $timeOutObj; 
$timeOutLimit->modify("-30 minutes");  // 30 minutes before actual time_out

// Now, update the conditions to reflect the changes
if ($currentTimeObj < $timeInObj) {
    $label = 'Unable to Attendance (Too Early)';
} elseif ($currentTimeObj >= $timeInObj && $currentTimeObj <= $timeOutLimit) {
    $label = 'Time In';
} elseif ($currentTimeObj > $timeOutLimit && $currentTimeObj <= $timeOutObj) {
    $label = 'Time Out';  // Within 30 minutes before the actual time_out
} elseif ($currentTimeObj > $timeOutObj && $currentTimeObj <= $overtimeLimit) {
    // Overtime condition: After time_out but within the overtime limit
    $label = 'Overtime';
} else {
    // After the overtime limit, it's no longer allowed
    $label = 'Unable to Attendance';
}

    
} else {
    $label = "No Settings Found";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .clock {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('includes/sidebar.php'); ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 form-container">
                <h3 class="text-center">Attendance Form</h3>
                <div id="current-date" class="text-center mb-3"></div>
                <div id="current-time" class="clock text-center"></div>
                <div id="current-phase" class="text-center text-success fw-bold"><?php echo htmlspecialchars($label); ?></div>

                <!-- Attendance Form -->
                <form id="attendanceForm" method="POST" action="check-student.php">
                    <div class="mb-3">
                        <label for="studentId" class="form-label">Student ID</label>
                        <input type="text" id="studentId" name="studentId" class="form-control" required>
                        <input type="hidden" name="current-phase" value="<?php echo htmlspecialchars($label); ?>">
                    </div>

                    <?php if ($label === 'Time In' || $label === 'Time Out' || $label === 'Overtime') { ?>
    <button type="submit" class="btn btn-primary"><?php echo $label; ?></button>
<?php } elseif ($label === 'Unable to Attendance (Too Early)') { ?>
    <button type="button" class="btn btn-danger" disabled>Unable to Attend</button>
<?php } else { ?>
    <button type="button" class="btn btn-warning" disabled>Attendance Unable</button>
<?php } ?>

                </form>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script>
        function updateDateTime() {
            const now = new Date();

            const currentDate = now.toLocaleDateString('en-GB', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const currentTime = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });

            document.getElementById("current-date").innerHTML = currentDate;
            document.getElementById("current-time").innerHTML = currentTime;
        }

        // Update the time on load and every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
