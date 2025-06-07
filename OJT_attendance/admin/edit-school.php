<?php
include_once('db/db-con.php');

// Fetch the school details to prefill the form fields
if (isset($_GET['id'])) {
    $school_id = $_GET['id'];
    $sql = "SELECT * FROM schools WHERE id = $school_id";
    $result = mysqli_query($conn, $sql);
    $school = mysqli_fetch_assoc($result);
}

// Update the school if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_name = $_POST['school_name'];
    $school_address = $_POST['school_address'];

    // Sanitize the input to prevent SQL injection
    $school_name = mysqli_real_escape_string($conn, $school_name);
    $school_address = mysqli_real_escape_string($conn, $school_address);

    // Update the school in the database
    $update_sql = "UPDATE schools SET school_name = '$school_name', school_address = '$school_address' WHERE id = $school_id";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('School updated successfully!'); window.location.href='index.php';</script>";
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
    <title>Edit School</title>
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
        <h2>Edit School</h2>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="schoolName" class="form-label">School Name</label>
                <input type="text" class="form-control" id="schoolName" name="school_name" value="<?php echo $school['school_name']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="schoolAddress" class="form-label">School Address</label>
                <input type="text" class="form-control" id="schoolAddress" name="school_address" value="<?php echo $school['school_address']; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update School</button>
            <a href="course.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<!-- Bootstrap 5 JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
