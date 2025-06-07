<?php
// Include database connection
include_once('db/db-con.php');

// Get the student ID from the URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Delete the student from the database
    $sql = "DELETE FROM courses WHERE id = $student_id";

    if ($conn->query($sql) === TRUE) {
        // Redirect after deleting
        header("Location: course.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
