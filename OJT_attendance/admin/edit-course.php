<?php
include_once('db/db-con.php');

// Fetch the course details to prefill the form fields
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    $sql = "SELECT * FROM courses WHERE id = $course_id";
    $result = mysqli_query($conn, $sql);
    $course = mysqli_fetch_assoc($result);
}

// Update the course if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];

    // Sanitize the input to prevent SQL injection
    $course_name = mysqli_real_escape_string($conn, $course_name);
    $course_description = mysqli_real_escape_string($conn, $course_description);

    // Update the course in the database
    $update_sql = "UPDATE courses SET course_name = '$course_name', course_description = '$course_description' WHERE id = $course_id";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Course updated successfully!'); window.location.href='course.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
      
    </style>
</head>
<body>

<!-- Form Container -->
<div class="container">
    <div class="form-container shadow-sm">
        <h2>Edit Course</h2>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="courseName" class="form-label">Course Name</label>
                <input type="text" class="form-control" id="courseName" name="course_name" value="<?php echo $course['course_name']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="courseDescription" class="form-label">Course Description</label>
                <textarea class="form-control" id="courseDescription" name="course_description" required><?php echo $course['course_description']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Course</button>
            <a href="course.php" class="btn btn-secondary ">Back</a>
        </form>
    </div>
</div>

<!-- Bootstrap 5 JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
