
<?php
// Include database connection
include_once('db/db-con.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form inputs
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $course = $conn->real_escape_string($_POST['course']);
    $school = $conn->real_escape_string($_POST['school']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $number_hours = $conn->real_escape_string($_POST['number_hours']);
    $email = $conn->real_escape_string($_POST['email']); // New field for email

    // Insert the new student into the database
    $sql = "INSERT INTO students (student_id, name, course, school, start_date, number_hours, user_email) 
            VALUES ('$student_id', '$name', '$course', '$school', '$start_date', '$number_hours', '$email')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>
        alert('Student Added');
        window.location.href = 'student.php';
      </script>";
   
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
