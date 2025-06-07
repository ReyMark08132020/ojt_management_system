<?php 
// Start the session
session_start();

// Include database connection
include('./db/db-con.php');

// Initialize variables for counts
$establishmentCount = 0;
$studentCount = 0;

// Query to count registered establishments
$sqlEstablishments = "SELECT COUNT(*) as count FROM establishments";
$resultEstablishments = mysqli_query($conn, $sqlEstablishments);
if ($resultEstablishments) {
    $row = mysqli_fetch_assoc($resultEstablishments);
    $establishmentCount = $row['count'];
}

// Query to count registered students
$sqlStudents = "SELECT COUNT(*) as count FROM students";
$resultStudents = mysqli_query($conn, $sqlStudents);
if ($resultStudents) {
    $row = mysqli_fetch_assoc($resultStudents);
    $studentCount = $row['count'];
}
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
        .card-body { text-align: center; }
        .card-body .icon { font-size: 3rem; }
        .card-body .stat-info { font-size: 1.5rem; }
        .card { border-radius: 1rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease; }
        .card:hover { transform: scale(1.05); }
        .stat-section { margin: 10px; }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php');?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('includes/sidebar.php');?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 container-main">
                <div class="row justify-content-center mt-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-center">Dashboard</h3>
                                
                                <!-- Registered Establishment Section -->
                                <div class="stat-section">
                                    <i class="fas fa-check-circle text-success icon"></i>
                                    <p class="stat-info"><strong>Registered Establishment: </strong><?php echo $establishmentCount; ?></p>
                                </div>
                              
                                <!-- Registered Students Section -->
                                <div class="stat-section">
                                    <i class="fas fa-users text-primary icon"></i>
                                    <p class="stat-info"><strong>Registered Students: </strong><?php echo $studentCount; ?></p>
                                </div>
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
