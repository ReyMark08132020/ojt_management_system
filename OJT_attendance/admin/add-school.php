<?php
// Include the database connection
include_once('db/db-con.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $school_name = $_POST['school_name'];
    $school_address = $_POST['school_address'];

    // Sanitize input to prevent SQL injection
    $school_name = mysqli_real_escape_string($conn, $school_name);
    $school_address = mysqli_real_escape_string($conn, $school_address);

    // Check if the school already exists
    $check_sql = "SELECT * FROM schools WHERE school_name = '$school_name'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // School already exists, show error message
        echo "<script>alert('A school with this name already exists. Please choose a different name.'); window.location.href='index.php';</script>";
    } else {
        // Insert data into schools table
        $sql = "INSERT INTO schools (school_name, school_address) VALUES ('$school_name', '$school_address')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('School added successfully!'); window.location.href='course.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
