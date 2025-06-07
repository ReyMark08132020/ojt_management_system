<?php
// Include the database connection
include_once('db/db-con.php');

// Start the session
session_start();

// Check if the student ID is passed in the URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch the student's current data from the database
    $sql = "SELECT * FROM students WHERE id = '$student_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Get the student data from the database
        $student = $result->fetch_assoc();
    } else {
        // Redirect to the student list if no student is found
        header("Location: student.php");
        exit();
    }
} else {
    // Redirect to the student list if the ID is not passed in the URL
    header("Location: student.php");
    exit();
}

// Update the student's data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $course = $conn->real_escape_string($_POST['course']);
    $school = $conn->real_escape_string($_POST['school']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $number_hours = $conn->real_escape_string($_POST['number_hours']);

    // Update the student in the database
    $update_sql = "UPDATE students SET name = '$name', course = '$course', school = '$school', start_date = '$start_date', number_hours = '$number_hours' WHERE id = '$student_id'";

    if ($conn->query($update_sql) === TRUE) {
        // Redirect to the student list page after updating
        header("Location: student.php");
        exit();
    } else {
        echo "Error updating student details: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Edit Student</h2>

                        <!-- Edit Form -->
                        <form action="edit-student.php?id=<?php echo $student_id; ?>" method="POST">
                            <!-- Student Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                            </div>

                            <!-- Course -->
                            <div class="mb-3">
                                <label for="course" class="form-label">Course</label>
                                <input type="text" class="form-control" id="course" name="course" value="<?php echo htmlspecialchars($student['course']); ?>" required>
                            </div>

                            <!-- School -->
                            <div class="mb-3">
                                <label for="school" class="form-label">School</label>
                                <input type="text" class="form-control" id="school" name="school" value="<?php echo htmlspecialchars($student['school']); ?>" required>
                            </div>

                            <!-- Start Date -->
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($student['start_date']); ?>" required>
                            </div>
                            <!-- Number of Hours -->
                            <div class="mb-3">
                                <label for="number_hours" class="form-label">Number of Hours</label>
                                <input type="number" class="form-control" id="number_hours" name="number_hours" value="<?php echo htmlspecialchars($student['number_hours']); ?>" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">Update Student</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

