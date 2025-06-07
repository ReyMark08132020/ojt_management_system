<?php
// Start the session and include the database connection
session_start();
include('db/db-con.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Check if an ID is provided in the URL
if (isset($_GET['id'])) {
    $establishment_id = $_GET['id'];

    // Query to get establishment details
    $query = "SELECT * FROM establishments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $establishment_id); // 'i' means integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the establishment was found
    if ($result->num_rows > 0) {
        $establishment = $result->fetch_assoc();
    } else {
        echo "Establishment not found!";
        exit;
    }
} else {
    echo "No ID provided!";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Establishment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2 class="my-4">Establishment Details</h2>

        <table class="table table-bordered">
            <tr>
                <th>Establishment Name</th>
                <td><?php echo htmlspecialchars($establishment['establishment_name']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo htmlspecialchars($establishment['address']); ?></td>
            </tr>
            <tr>
                <th>Contact Person</th>
                <td><?php echo htmlspecialchars($establishment['contact_person']); ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($establishment['phone']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($establishment['email']); ?></td>
            </tr>
            <tr>
                <th>User Type</th>
                <td><?php echo $establishment['usertype'] == 0 ? 'Admin' : 'User'; ?></td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo htmlspecialchars($establishment['createAt']); ?></td>
            </tr>
        </table>

        <a href="users.php" class="btn btn-secondary">Back to List</a>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
