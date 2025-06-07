<?php
// Start the session
session_start();

// Include the database connection
include_once('db/db-con.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize the form inputs
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare a statement to check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM establishments WHERE email = ?");
    $stmt->bind_param("s", $email);  // Bind email parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, fetch data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store session variables
            $_SESSION['usertype'] = $user['usertype'];  
            $_SESSION['email'] = $user['email'];  
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['establishment_name'] = $user['establishment_name']; // Store the establishment name

            // Redirect based on usertype (0 = admin, 1 = user)
            if ($user['usertype'] == 0) {
                // Admin login - redirect to admin dashboard
                header("Location: admin/admin-dashboard.php");
            } else {
                // Regular user login - redirect to user dashboard
                header("Location: user-dashboard.php");
            }
            exit();
        } else {
            $error_msg = "Invalid email or password!";
        }
    } else {
        $error_msg = "No user found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Student Attendance - Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Page Background and Container Styling */
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 2rem;
        }
        .card-title {
            font-size: 1.5rem;
            color: #6a11cb;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 10px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #6a11cb;
            box-shadow: none;
        }
        .btn-primary {
            background-color: #6a11cb;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5a00b0;
        }
        .btn-secondary, .btn-success {
            border-radius: 10px;
        }
        .alert {
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        .card-footer {
            text-align: center;
            font-size: 0.85rem;
            color: #555;
        }
        .card-footer a {
            color: #6a11cb;
            text-decoration: none;
        }
        .card-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Main Login Form Area -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title">OJT Student Login</h2>
                        
                        <!-- Display Error Message if any -->
                        <?php if (isset($error_msg)): ?>
                            <div class="alert alert-danger text-center"><?= $error_msg; ?></div>
                        <?php endif; ?>
                        
                        <!-- Login Form -->
                        <form action="index.php" method="POST">
                            <div class="mb-3">
                                <label for="login-email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="login-email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="login-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="login-password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Log In</button>
</form>

<!-- Additional Actions Container -->
<div class="d-flex justify-content-between flex-column flex-sm-row gap-2 align-items-center">
    <!-- Create Account Button -->
   

    <!-- Go to Attendance Button with Left Arrow Below -->
    <div class="d-flex flex-column align-items-center w-100">
        <a href="attendance-form.php" class="btn btn-outline-success w-100">Go to Attendance</a>
       
        <!-- Bootstrap Icon Arrow -->
    </div>
   
</div>
                  
                    
                    <!-- Footer Links -->
                    <div class="card-footer">
                        <small>Forgot your password? <a href="#">Reset it here</a>.</small>
                        <small> <a href="sign-up.php">Create Your Account<i class="bi bi-arrow-right mt-2" style=" color: #198754;"> </i> </a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
   
</body>
</html>
