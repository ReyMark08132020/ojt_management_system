<?php
// Include the database connection
include_once('db/db-con.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];

    // Sanitize input to prevent SQL injection
    $course_name = mysqli_real_escape_string($conn, $course_name);
    $course_description = mysqli_real_escape_string($conn, $course_description);

    // Check if the course already exists
    $check_sql = "SELECT * FROM courses WHERE course_name = '$course_name'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // Course already exists, show error message
        echo "<script>alert('Course with this name already exists. Please choose a different name.'); window.location.href='course.php';</script>";
    } else {
        // Insert data into courses table
        $sql = "INSERT INTO courses (course_name, course_description) VALUES ('$course_name', '$course_description')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Course added successfully!'); window.location.href='course.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
