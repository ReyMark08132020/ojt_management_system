<!-- Header -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<header class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">OJT Student Attendance</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User is logged in -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['establishment_name']); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="manage-account.php"><i class="fas fa-cogs"></i> Manage Account</a></li>
                            <li><a class="dropdown-item" href="log-out.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- User is not logged in, display login and sign-up options -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sign-up.php"><i class="fas fa-user-plus"></i> Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</header>
