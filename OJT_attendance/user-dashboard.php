<?php 
// Start the session
session_start();

// Include your database connection file
include('db/db-con.php');

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Get the logged-in user's email
    $user_email = $_SESSION['email'];
    
    // Initialize variables to store the data
    $total_students = 0;
   
    $absent_today = 0;

    // Query to get the total number of students for the logged-in user's establishment
    $total_students_query = "SELECT COUNT(*) AS total_students FROM students WHERE user_email = ?";
    $stmt_total_students = $conn->prepare($total_students_query);
    $stmt_total_students->bind_param("s", $user_email);
    $stmt_total_students->execute();
    $result_total_students = $stmt_total_students->get_result();
    
    if ($result_total_students && $result_total_students->num_rows > 0) {
        $total_students_data = $result_total_students->fetch_assoc();
        $total_students = $total_students_data['total_students'];
    }

    // Get today's date
    $today_date = date('Y-m-d');
   // Query to get the number of students present today (time_in and time_out must be filled)
$present_today_query = "SELECT COUNT(DISTINCT student_id) AS present_today 
FROM attendance 
WHERE user_email = ? AND DATE(date) = ? AND time_in IS NOT NULL AND time_out IS NOT NULL";
$stmt_present_today = $conn->prepare($present_today_query);
$stmt_present_today->bind_param("ss", $user_email, $today_date);
$stmt_present_today->execute();
$result_present_today = $stmt_present_today->get_result();

$present_today = 0;
if ($result_present_today && $result_present_today->num_rows > 0) {
$present_today_data = $result_present_today->fetch_assoc();
$present_today = $present_today_data['present_today'];
}



    // Calculate the number of absent students
    $absent_today = $total_students - $present_today;
    // Query to get the number of students who logged time_in today
$time_in_today_query = "SELECT COUNT(DISTINCT student_id) AS time_in_today 
FROM attendance 
WHERE user_email = ? AND DATE(date) = ? AND time_in IS NOT NULL";
$stmt_time_in_today = $conn->prepare($time_in_today_query);
$stmt_time_in_today->bind_param("ss", $user_email, $today_date);
$stmt_time_in_today->execute();
$result_time_in_today = $stmt_time_in_today->get_result();

$time_in_today = 0;
if ($result_time_in_today && $result_time_in_today->num_rows > 0) {
$time_in_today_data = $result_time_in_today->fetch_assoc();
$time_in_today = $time_in_today_data['time_in_today'];
}

// Close the prepared statement
$stmt_time_in_today->close();


    // Close the prepared statements
    $stmt_total_students->close();
    $stmt_present_today->close();

} else {
    echo "<script> window.location.href = 'index.php'; </script>";
    exit;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Student Attendance</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    .card {
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    .card .icon {
        font-size: 3rem;
    }
    .card-title {
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .stat-info {
        font-size: 2.5rem;
        font-weight: bold;
        color: #333;
    }
    .bg-light {
        background-color: #f9f9f9 !important;
    }
    .mb-3 {
        margin-bottom: 1.5rem !important;
    }
</style>

</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php');?>

    <div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('includes/sidebar.php'); ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 container-main">
            <div class="row justify-content-center mt-4">
                <!-- Card for Present Today -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm text-center bg-light">
                        <div class="card-body">
                            <i class="fas fa-check-circle text-success icon mb-3"></i>
                            <h5 class="card-title">Present Today</h5>
                            <p class="stat-info display-4"><?php echo $present_today; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Card for Absent Today -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm text-center bg-light">
                        <div class="card-body">
                            <i class="fas fa-user-times text-danger icon mb-3"></i>
                            <h5 class="card-title">Absent Today</h5>
                            <p class="stat-info display-4"><?php echo $absent_today; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Card for Registered Students -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm text-center bg-light">
                        <div class="card-body">
                            <i class="fas fa-users text-primary icon mb-3"></i>
                            <h5 class="card-title">Registered Students</h5>
                            <p class="stat-info display-4"><?php echo $total_students; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
    <div class="card shadow-sm text-center bg-light">
        <div class="card-body">
            <i class="fas fa-clock text-warning icon mb-3"></i>
            <h5 class="card-title">Time In Today</h5>
            <p class="stat-info display-4"><?php echo $time_in_today; ?></p>
        </div>
    </div>
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
