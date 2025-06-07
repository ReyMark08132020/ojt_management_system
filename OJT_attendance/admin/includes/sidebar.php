<style>
    /* Custom styling for the page */
    .sidebar {
        height: 100vh;
        background-color: #f8f9fa;
        padding-top: 20px;
    }
    .sidebar a {
        color: #333;
        text-decoration: none;
    }
    .sidebar a:hover {
        color: #007bff;
    }
    .container-main {
        margin-top: 20px;
    }
    /* Custom active link styles */
.nav-link.active {
    background-color: #007bff; /* Change background color */
    color: white; /* Text color for active link */
}

</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar bg-light">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <!-- Always show 'Dashboard' and 'Students' for all users -->
            <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 0): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php') ? 'active' : ''; ?>" href="admin-dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <?php endif; ?>
            <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'user-dashboard.php') ? 'active' : ''; ?>" href="user-dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'student.php') ? 'active' : ''; ?>" href="student.php">
                    <i class="fas fa-users"></i> Students
                </a>
            </li>
            <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'attendance.php') ? 'active' : ''; ?>" href="attendance.php">
                    <i class="fas fa-clipboard-list"></i> Attendance
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'attendance-report.php') ? 'active' : ''; ?>" href="attendance-report.php">
                    <i class="fas fa-clipboard"></i> Attendance Report
                </a>
            </li>

            <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 0): ?>
                <!-- If the user is an admin (usertype 0), show additional options -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-users-cog"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'course.php') ? 'active' : ''; ?>" href="course.php">
                        <i class="fas fa-book"></i> Courses
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
