<?php
session_start();
include_once('db/db-con.php');

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Set the number of records per page
    $recordsPerPage = 10;

    // Determine the current page
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Query to count total establishments
    $totalQuery = "SELECT COUNT(*) as total FROM establishments";
    $totalResult = $conn->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'];

    // Calculate total pages
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Query to fetch establishments for the current page
    $sql = "SELECT * FROM establishments LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $offset, $recordsPerPage);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establishments List</title>
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
                <h2 class="my-4">Establishments List</h2>

                <!-- Button to trigger the "Add Establishment" modal -->
                <button class="btn btn-primary my-4" data-bs-toggle="modal" data-bs-target="#addEstablishmentModal">Add Establishment</button>

                <!-- Table to display establishments -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Establishment Name</th>
                            <th>Address</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = $offset + 1; ?>
                            <?php while ($establishment = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td><?php echo htmlspecialchars($establishment['establishment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($establishment['address']); ?></td>
                                    <td><?php echo htmlspecialchars($establishment['contact_person']); ?></td>
                                    <td><?php echo htmlspecialchars($establishment['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($establishment['email']); ?></td>
                                    <td><?php echo $establishment['usertype'] == 0 ? 'Admin' : 'User'; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($establishment['createAt'])); ?></td>
                                    <td>
                                        <a href="view-establishment.php?id=<?php echo $establishment['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                        <a href="edit-establishment.php?id=<?php echo $establishment['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="delete-establishment.php?id=<?php echo $establishment['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this establishment?');">
        Delete
    </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">No establishments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination Links -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $currentPage) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <!-- Modal for adding a new establishment -->
                <div class="modal fade" id="addEstablishmentModal" tabindex="-1" aria-labelledby="addEstablishmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addEstablishmentModalLabel">Add Establishment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="add-establishment.php" method="POST">
                                    <div class="mb-3">
                                        <label for="establishment_name" class="form-label">Establishment Name</label>
                                        <input type="text" class="form-control" id="establishment_name" name="establishment_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_person" class="form-label">Contact Person</label>
                                        <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="usertype" class="form-label">User Type</label>
                                        <select class="form-select" id="usertype" name="usertype" required>
                                            <option value="0">Admin</option>
                                            <option value="1">User</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Establishment</button>
                                </form>
                            </div>
                        </div>
                    </div>
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
