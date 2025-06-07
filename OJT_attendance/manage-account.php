<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include_once('db/db-con.php');

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM establishments WHERE id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Check if the form was submitted to update account details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password === $confirm_password) {
        // If password is set, hash it and update
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE establishments SET establishment_name = '$name', email = '$email', password = '$hashed_password' WHERE id = '$user_id'";
        } else {
            // If password is not updated, update without changing the password
            $update_sql = "UPDATE establishments SET establishment_name = '$name', email = '$email' WHERE id = '$user_id'";
        }

        if ($conn->query($update_sql)) {
            echo "<div class='alert alert-success'>Account updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating account.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <?php include('includes/header.php');?>

    <div class="container mt-5">
        <h2>Manage Account</h2>
        <form action="manage-account.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Establishment Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['establishment_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank if you don't want to change">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Leave blank if you don't want to change">
            </div>
            <button type="submit" class="btn btn-primary">Update Account</button>
            <a href="user-dashboard.php" class="btn ">Back</a>
        </form>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
