<?php
// Include the database connection
include_once('db/db-con.php');

// Start the session
session_start();

// Check if the student ID is passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student's data
    $student_query = "SELECT student_id, name, number_hours FROM students WHERE id = ?";
    $stmt_student = $conn->prepare($student_query);
    $stmt_student->bind_param("i", $student_id);
    $stmt_student->execute();
    $student_result = $stmt_student->get_result();

    // Check if student exists
    if ($student_result->num_rows == 1) {
        $student = $student_result->fetch_assoc();
    } else {
        echo "Student not found.";
        exit();
    }
    $stmt_student->close();

    // Fetch attendance records and calculate hours worked
    $attendance_query = "SELECT date, time_in, time_out FROM attendance WHERE student_id = ?";
    $stmt_attendance = $conn->prepare($attendance_query);
    $stmt_attendance->bind_param("i", $student_id);
    $stmt_attendance->execute();
    $attendance_result = $stmt_attendance->get_result();

    // Initialize variables
    $total_hours_worked = 0;
    $attendance_data = [];

    // Calculate total hours worked and store attendance data
    while ($attendance = $attendance_result->fetch_assoc()) {
        $time_in = new DateTime($attendance['time_in']);
        $time_out = new DateTime($attendance['time_out']);
        
        // Calculate hours for this record
        $interval = $time_in->diff($time_out);
        $hours_worked = $interval->h + ($interval->i / 60); // Convert minutes to hours
        $total_hours_worked += $hours_worked;

        // Store attendance data in an array for later display
        $attendance_data[] = [
            'date' => $attendance['date'],
            'time_in' => $time_in->format('H:i'),
            'time_out' => $time_out->format('H:i'),
            'hours_worked' => round($hours_worked, 2)
        ];
    }

    // Calculate remaining hours
    $remaining_hours = $student['number_hours'] - $total_hours_worked;

    $stmt_attendance->close();
    $conn->close();
} else {
    echo "Invalid student ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
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
                        <td><?php echo htmlspecialchars($attendance['hours_worked']); ?> hours</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="student.php" class="btn btn-secondary">Back to Students List</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
