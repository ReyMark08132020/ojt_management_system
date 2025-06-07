<?php
include_once('db/db-con.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize it
    $establishment_name = $conn->real_escape_string($_POST['establishment_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact_person = $conn->real_escape_string($_POST['contact_person']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Hash the password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the email already exists
    $checkEmailSql = "SELECT * FROM establishments WHERE email = '$email'";
    $result = $conn->query($checkEmailSql);

    if ($result->num_rows > 0) {
        // Email already exists, display an error message
        echo "<script>
                alert('Error: This email is already registered. Please use a different email.');
                window.history.back();
              </script>";
    } else {
        // Email does not exist, proceed with registration
        $sql = "INSERT INTO establishments (establishment_name, address, contact_person, phone, email, password) 
                VALUES ('$establishment_name', '$address', '$contact_person', '$phone', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            // Display success message and redirect to login page
            echo "<script>
                    alert('Registration successful! Please log in.');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            // Display error message if registration failed
            echo "<script>
                    alert('Error: Unable to register. Please try again later.');
                  </script>";
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Sign Up</title>
    <!-- Bootstrap 5 CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .w-100 {
            width: 100%;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Establishment Sign Up Form -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Sign Up</h2>
                        <form action="sign-up.php" method="POST">
                            <!-- Establishment Name -->
                            <div class="mb-3">
                                <label for="establishment-name" class="form-label">Establishment Name</label>
                                <input type="text" class="form-control" id="establishment-name" name="establishment_name" required>
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Full Address</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>

                            <!-- Contact Person -->
                            <div class="mb-3">
                                <label for="contact-person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact-person" name="contact_person" required>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="phone" 
                                    name="phone" 
                                    pattern="^[0-9]{11}$" 
                                    maxlength="11" 
                                    required
                                    title="Please enter exactly 11 digits"
                                >
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       minlength="8" 
                                       pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                       title="Password must be at least 8 characters long, contain one letter, one number, and one special character">
                                <div id="passwordHelp" class="form-text">Must be at least 8 characters, include a letter, number, and special character.</div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">Sign Up</button>

                            <div class="text-center mt-3">
                                <a href="index.php">Already have an account?</a>
                            </div>
                        </form>
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
