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

    // Convert time strings to DateTime objects for validation
    $timeInObj = DateTime::createFromFormat('H:i:s', $timeIn);
    $timeOutObj = DateTime::createFromFormat('H:i:s', $timeOut);

    // Validate that time_in is before time_out
    if ($timeInObj >= $timeOutObj) {
        // If validation fails, set an appropriate error label
        $label = 'Invalid Schedule: Time In must be earlier than Time Out';
    } else {
        // Get the current time
        $currentTime = date("H:i:s");
        $currentTimeObj = DateTime::createFromFormat('H:i:s', $currentTime);

        // Overtime calculation: Add overtime to the timeOut
        $overtimeLimit = clone $timeOutObj;
        $overtimeLimit->modify("+{$maxOvertime} minutes"); // Add max overtime minutes

        // Create a timeOutLimit (30 minutes before actual time_out)
        $timeOutLimit = clone $timeOutObj;
        $timeOutLimit->modify("-30 minutes"); // 30 minutes before actual time_out

        // Attendance label logic
        if ($currentTimeObj < $timeInObj) {
            $label = 'Unable to Attendance (Too Early)';
        } elseif ($currentTimeObj >= $timeInObj && $currentTimeObj <= $timeOutLimit) {
            $label = 'Time In';
        } elseif ($currentTimeObj > $timeOutLimit && $currentTimeObj <= $timeOutObj) {
            $label = 'Time Out'; // Within 30 minutes before the actual time_out
        } elseif ($currentTimeObj > $timeOutObj && $currentTimeObj <= $overtimeLimit) {
            // Overtime condition: After time_out but within the overtime limit
            $label = 'Overtime';
        } else {
            // After the overtime limit, it's no longer allowed
            $label = 'Unable to Attendance';
        }
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
    

    <div class="container-fluid py-4">
    <div class="row justify-content-center">
        <!-- Main Content -->
        <main class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <h3 class="text-center text-primary mb-3">Attendance Form</h3>
                    <div id="current-date" class="text-center mb-2 text-secondary fs-5"></div>
                    <div id="current-time" class="clock text-center display-5 text-primary mb-2"></div>
                    <div id="current-phase" class="text-center fw-bold text-success mb-4">
                        <?php echo $label; ?>
                    </div>

                    <!-- Attendance Form -->
                    <form id="attendanceForm" method="POST" action="check-student2.php">
                        <div class="mb-4">
                            <label for="studentId" class="form-label fw-bold">Student ID</label>
                            <input type="text" id="studentId" name="studentId" class="form-control shadow-sm" placeholder="Enter Student ID" required>
                            <input type="hidden" id="current-phase" name="current-phase" value="<?php echo $label; ?>">
                        </div>

                        <!-- Button logic -->
                        <?php if ($label == 'Time In') { ?>
                        <button type="submit" class="btn btn-primary">Time In</button>
                    <?php } elseif ($label == 'Time Out') { ?>
                        <button type="submit" class="btn btn-primary">Time Out</button>
                    <?php } elseif ($label == 'Unable to Attendance (Too Early)') { ?>
                    <?php } elseif ($label == 'Overtime') { ?>
                        <button type="submit" class="btn btn-primary">Time Out</button>
                    <?php } elseif ($label == 'Unable to Attendance (Too Early)') { ?>
                        <button type="button" class="btn btn-danger" disabled>Unable to Attend</button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning" disabled>Attendance Unable</button>
                    <?php } ?>
                        <!-- Back Button -->
                        <div class="d-grid mt-3">
                            <a href="index.php" class="btn btn-outline-danger btn-lg">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>



<script>
    // Function to format and update the current date and time with AM/PM
    function updateDateTime() {
        // Get the current date and time in the Philippines timezone
        var now = new Date();

        // Get current date in format: Weekday, Day Month Year (e.g., Sunday, 17 November 2024)
        var currentDate = now.toLocaleDateString('en-GB', {
            weekday: 'long',  // Full day of the week (e.g. Monday)
            year: 'numeric', 
            month: 'long',  // Full month name (e.g. November)
            day: 'numeric'
        });

        // Get current time in 12-hour format with AM/PM
        var currentTime = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true  // 12-hour time format with AM/PM
        });

        // Set the current date and time into the HTML elements
        document.getElementById("current-date").innerHTML = currentDate;
        document.getElementById("current-time").innerHTML = currentTime;
    }

    // Call the function to update date and time
    updateDateTime();

    // Optional: Update the time every second
    setInterval(updateDateTime, 1000);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
