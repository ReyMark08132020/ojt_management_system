<?php
// Start the session and include the database connection
session_start();
include('db/db-con.php');

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Get the logged-in user's email
    $user_email = $_SESSION['email'];

    // Initialize date filter variable
    $filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : '';

    // Base query to fetch attendance data only for students associated with the logged-in user's email
    $query = "SELECT a.student_id, s.name, a.date, a.time_in, a.time_out, a.session_info 
              FROM attendance a
              INNER JOIN students s ON a.student_id = s.student_id
              WHERE s.user_email = ?";

    // If a date filter is set, add an additional condition to the query
    if ($filter_date) {
        $query .= " AND a.date = ?";
    }

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind parameters: user email and optionally the date if it's set
    if ($filter_date) {
        $stmt->bind_param('ss', $user_email, $filter_date);
    } else {
        $stmt->bind_param('s', $user_email);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch records into an array
    $attendance_records = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $attendance_records[] = $row;
        }
    } else {
        $attendance_records = null;
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
} else {
    echo "<script> window.location.href = 'index.php'; </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Student Attendance Report</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php');?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('includes/sidebar.php');?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 container-main">
                <div class="row justify-content-center mt-4">
                    <div class="col-md-12">
                        <h2 class="text-center">OJT Student Attendance Report</h2>

                        <!-- Date Filter Form -->
                        <form action="" method="POST" class="mb-4">
                            <div class="row justify-content-center">
                                <div class="col-md-4">
                                    <input type="date" name="filter_date" class="form-control" value="<?= htmlspecialchars($filter_date) ?>" />
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>

                        <!-- Attendance Report Table -->
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Date</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Session</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php if ($attendance_records): ?>
                                        <?php foreach ($attendance_records as $record): ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td><?= htmlspecialchars($record['student_id']) ?></td>
                                                <td><?= htmlspecialchars($record['name']) ?></td>
                                                <td><?= htmlspecialchars($record['date']) ?></td>
                                                <td><?= htmlspecialchars($record['time_in']) ?></td>
                                                <td>
                                                    <?php 
                                                    if ($record['time_out'] === NULL) {
                                                        echo "Not yet time out";
                                                    } else {
                                                        echo htmlspecialchars($record['time_out']);
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= htmlspecialchars($record['session_info']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No attendance records found for this date.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php');?>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
