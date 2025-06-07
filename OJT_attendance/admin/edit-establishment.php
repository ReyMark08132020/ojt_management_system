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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $establishment_name = $_POST['establishment_name'];
    $address = $_POST['address'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $usertype = $_POST['usertype'];

    // Query to update the establishment data
    $update_query = "UPDATE establishments SET establishment_name = ?, address = ?, contact_person = ?, phone = ?, email = ?, usertype = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssssssi', $establishment_name, $address, $contact_person, $phone, $email, $usertype, $establishment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Establishment updated successfully!'); window.location.href = 'users.php';</script>";
    } else {
        echo "Error updating establishment!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Establishment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <div class="container">
        <h2 class="my-4">Edit Establishment</h2>

        <form method="POST" action="edit-establishment.php?id=<?php echo $establishment['id']; ?>">
            <div class="mb-3">
                <label for="establishment_name" class="form-label">Establishment Name</label>
                <input type="text" class="form-control" id="establishment_name" name="establishment_name" value="<?php echo htmlspecialchars($establishment['establishment_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($establishment['address']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact_person" class="form-label">Contact Person</label>
                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($establishment['contact_person']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($establishment['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($establishment['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="usertype" class="form-label">User Type</label>
                <select class="form-select" id="usertype" name="usertype" required>
                    <option value="0" <?php echo ($establishment['usertype'] == 0) ? 'selected' : ''; ?>>Admin</option>
                    <option value="1" <?php echo ($establishment['usertype'] == 1) ? 'selected' : ''; ?>>User</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="users.php" class="btn btn-secondary">Back to List</a>
        </form>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
