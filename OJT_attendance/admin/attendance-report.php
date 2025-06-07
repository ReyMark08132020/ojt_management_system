<?php
// Start the session and include the database connection
session_start();
include('db/db-con.php');

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Initialize variables for pagination
    $records_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $records_per_page;

    // Initialize the date filter variable
    $filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : '';

    // Base query to fetch attendance data for all students
    $query = "SELECT a.student_id, s.name, a.date, a.time_in, a.time_out, a.session_info 
              FROM attendance a
              INNER JOIN students s ON a.student_id = s.student_id";

    // If a date filter is set, add an additional condition to the query
    if ($filter_date) {
        $query .= " WHERE a.date = ?";
    }

    // Add LIMIT and OFFSET for pagination
    $query .= " LIMIT ? OFFSET ?";

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind parameters
    if ($filter_date) {
        $stmt->bind_param('sii', $filter_date, $records_per_page, $offset);
    } else {
        $stmt->bind_param('ii', $records_per_page, $offset);
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

    // Get total number of records (for pagination calculations)
    $count_query = "SELECT COUNT(*) AS total_records FROM attendance a INNER JOIN students s ON a.student_id = s.student_id";
    if ($filter_date) {
        $count_query .= " WHERE a.date = ?";
    }

    $count_stmt = $conn->prepare($count_query);
    if ($filter_date) {
        $count_stmt->bind_param('s', $filter_date);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total_records'];
    $total_pages = ceil($total_records / $records_per_page);

    // Close statements and database connection
    $stmt->close();
    $count_stmt->close();
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
                                    <?php $counter = $offset + 1; ?>
                                    <?php if ($attendance_records): ?>
                                        <?php foreach ($attendance_records as $record): ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td><?= htmlspecialchars($record['student_id']) ?></td>
                                                <td><?= htmlspecialchars($record['name']) ?></td>
                                                <td><?= htmlspecialchars($record['date']) ?></td>
                                                <td><?= htmlspecialchars($record['time_in']) ?></td>
                                                <td>
                                                    <?= $record['time_out'] ? htmlspecialchars($record['time_out']) : "Not yet time out" ?>
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

                        <!-- Pagination Buttons -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo; Previous</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                            <span aria-hidden="true">Next &raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
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
