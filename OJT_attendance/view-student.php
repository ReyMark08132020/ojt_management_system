<?php
// Include the database connection
include_once('db/db-con.php');

// Start the session
session_start();

// Check if the required parameters (`id` and `student_id`) are present in the URL
if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {
    $id = intval($_GET['id']); // Retrieve and sanitize the `id` parameter
    $student_id = intval($_GET['student_id']); // Retrieve and sanitize the `student_id` parameter

    // Fetch student's data using the primary key `id`
    $student_query = "SELECT student_id, name, number_hours, school, course FROM students WHERE id = ?";
    $stmt_student = $conn->prepare($student_query);
    $stmt_student->bind_param("i", $id);
    $stmt_student->execute();
    $student_result = $stmt_student->get_result();

    // Check if the student exists
    if ($student_result->num_rows == 1) {
        $student = $student_result->fetch_assoc(); // Fetch student details
    } else {
        $error_message = "Student not found.";
        $stmt_student->close();
        $conn->close();
        exit();
    }
    $stmt_student->close();

    // Fetch attendance records for the retrieved `student_id`
    $attendance_query = "SELECT date, time_in, time_out FROM attendance WHERE student_id = ?";
    $stmt_attendance = $conn->prepare($attendance_query);
    $stmt_attendance->bind_param("i", $student_id);
    $stmt_attendance->execute();
    $attendance_result = $stmt_attendance->get_result();

    // Initialize variables
    $total_hours_worked = 0;
    $attendance_data = [];

    // Process attendance records
    while ($attendance = $attendance_result->fetch_assoc()) {
        $time_in = new DateTime($attendance['time_in']);
        
        // Check if `time_out` is not NULL
        if ($attendance['time_out'] !== NULL) {
            $time_out = new DateTime($attendance['time_out']);
            
            // Validate time entries
            if ($time_out > $time_in) {
                // Calculate hours worked for this record
                $interval = $time_in->diff($time_out);
                $hours_worked = $interval->h + ($interval->i / 60); // Convert minutes to hours
                $total_hours_worked += $hours_worked;

                // Store attendance data
                $attendance_data[] = [
                    'date' => $attendance['date'],
                    'time_in' => $time_in->format('H:i'),
                    'time_out' => $time_out->format('H:i'),
                    'hours_worked' => round($hours_worked, 2)
                ];
            } else {
                // Handle invalid time entries (time_out < time_in)
                $attendance_data[] = [
                    'date' => $attendance['date'],
                    'time_in' => $time_in->format('H:i'),
                    'time_out' => $attendance['time_out'], // Display time_out as it is
                    'hours_worked' => "Invalid time entry"
                ];
            }
        } else {
            // Handle missing time_out (NULL)
            $attendance_data[] = [
                'date' => $attendance['date'],
                'time_in' => $time_in->format('H:i'),
                'time_out' => "Missing time out",
                'hours_worked' => "Incomplete entry"
            ];
        }
    }

    // Calculate remaining hours
    $remaining_hours = max(0, $student['number_hours'] - $total_hours_worked);

    $stmt_attendance->close();
    $conn->close();
} else {
    $error_message = "Invalid URL parameters.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <h2>Student Details</h2>
            <table class="table table-bordered">
                <tr>
                    <th>Student ID</th>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                </tr>
                <tr>
                    <th>Student Name</th>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                </tr>
                <tr>
                    <th>Student Course</th>
                    <td><?php echo htmlspecialchars($student['course']); ?></td>
                </tr>
                <tr>
                    <th>Student School</th>
                    <td><?php echo htmlspecialchars($student['school']); ?></td>
                </tr>
                <tr>
                    <th>Total Required Hours</th>
                    <td><?php echo htmlspecialchars($student['number_hours']); ?> hours</td>
                </tr>
                <tr>
                    <th>Total Hours Worked</th>
                    <td><?php echo round($total_hours_worked, 2); ?> hours</td>
                </tr>
                <tr>
                    <th>Remaining Hours</th>
                    <td><?php echo round($remaining_hours, 2); ?> hours</td>
                </tr>
            </table>

            <h3>Attendance Records</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours Worked</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_data as $attendance): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attendance['date']); ?></td>
                            <td><?php echo htmlspecialchars($attendance['time_in']); ?></td>
                            <td><?php echo htmlspecialchars($attendance['time_out']); ?></td>
                            <td><?php echo htmlspecialchars($attendance['hours_worked']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="student.php" class="btn btn-secondary">Back to Students List</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
