<?php
// Start the session and check for establishment user login
session_start();
include('db/db-con.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

$user_email = $_SESSION['email'];  // Get the email of the logged-in user

// Initialize message variable
$message = '';

// Handling form submission for adding or updating attendance times
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $time_in = trim($_POST['time_in']);
    $time_out = trim($_POST['time_out']);
    $max_overtime = intval($_POST['max_overtime']); // Ensure it's an integer

   // Verify that the user_email exists in the establishments table
$check_user_stmt = $conn->prepare("SELECT email FROM establishments WHERE email = ?");
$check_user_stmt->bind_param("s", $user_email);
$check_user_stmt->execute();
$user_exists = $check_user_stmt->get_result()->num_rows > 0;
$check_user_stmt->close();

if (!$user_exists) {
    $message = "Error: The user email does not exist in the establishments table.";
} else {
    // Proceed with the INSERT or UPDATE
    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE establishment_settings 
                                SET time_in = ?, time_out = ?, max_overtime = ? 
                                WHERE user_email = ?");
        $stmt->bind_param("ssis", $time_in, $time_out, $max_overtime, $user_email);
        if ($stmt->execute()) {
            $message = "Settings updated successfully!";
        } else {
            $message = "Error updating settings: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['save'])) {
        $stmt = $conn->prepare("INSERT INTO establishment_settings (user_email, time_in, time_out, max_overtime)
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $user_email, $time_in, $time_out, $max_overtime);
        if ($stmt->execute()) {
            $message = "Settings saved successfully!";
        } else {
            $message = "Error saving settings: " . $stmt->error;
        }
        $stmt->close();
    }
}

}

// Fetch existing settings based on the logged-in user's email
$stmt = $conn->prepare("SELECT time_in, time_out, max_overtime FROM establishment_settings WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc() ?: ['time_in' => '', 'time_out' => '', 'max_overtime' => '']; // Default empty values
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Attendance Settings</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('includes/sidebar.php'); ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 container-main">
                <h2 class="mt-4">Set Attendance Settings</h2>
                
                <div class="container mt-5">
                    <!-- Display message -->
                    <?php if (!empty($message)): ?>
                        <div class="alert <?= strpos($message, 'Error') === false ? 'alert-success' : 'alert-danger'; ?>">
                            <?= htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <!-- Time In -->
                        <div class="mb-3">
                            <label for="time_in" class="form-label">Time In</label>
                            <input 
                                type="time" 
                                class="form-control" 
                                id="time_in" 
                                name="time_in" 
                                value="<?= htmlspecialchars($settings['time_in']); ?>" 
                                required>
                        </div>
                        <!-- Time Out -->
                        <div class="mb-3">
                            <label for="time_out" class="form-label">Time Out</label>
                            <input 
                                type="time" 
                                class="form-control" 
                                id="time_out" 
                                name="time_out" 
                                value="<?= htmlspecialchars($settings['time_out']); ?>" 
                                required>
                        </div>
                        <!-- Maximum Overtime -->
                        <div class="mb-3">
                            <label for="max_overtime" class="form-label">Maximum Overtime (minutes)</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="max_overtime" 
                                name="max_overtime" 
                                value="<?= htmlspecialchars($settings['max_overtime']); ?>" 
                                required>
                        </div>
                        <!-- Submit Button -->
                        <?php if (!empty($settings['time_in']) || !empty($settings['time_out'])): ?>
                            <button type="submit" name="update" class="btn btn-warning">Update Settings</button>
                        <?php else: ?>
                            <button type="submit" name="save" class="btn btn-primary">Save Settings</button>
                        <?php endif; ?>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
