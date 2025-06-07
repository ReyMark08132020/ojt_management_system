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
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['establishment_name'] = $user['establishment_name']; // Store the establishment name

            // Redirect to a common page for all users
            header("Location: attendance2.php");
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
    <style>
        /* Custom styling for the login card */
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
        }
        .card-header {
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
      
            background-color: #6e8efb;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5a79e3;
        }
        .login-icon {
            font-size: 3rem;
            color: #6e8efb;
            margin-bottom: 1rem;
        }
        .card-footer {
            text-align: center;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>
<body>

    <!-- Login Form -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="login-icon bi bi-person-circle"></i>
                            <h2 class="card-title">Student Attendance</h2>
                        </div>
                        <!-- Error Message Placeholder -->
                        <?php if (isset($error_msg)): ?>
                            <div class="alert alert-danger"><?= $error_msg; ?></div>
                        <?php endif; ?>
                        <!-- Form -->
                        <form action="attendance-form.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Log In</button>
                            <a href="index.php" class="btn btn-secondary">Back</a>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons for the User Icon (Optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
