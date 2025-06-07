

<?php
session_start();
// Include the database connection
include_once('db/db-con.php');

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Set the number of students per page
    $studentsPerPage = 10;

    // Determine the current page
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $studentsPerPage;

    // Query to count total students
    $totalQuery = "SELECT COUNT(*) as total FROM students";
    $totalResult = $conn->query($totalQuery);
    $totalStudents = $totalResult->fetch_assoc()['total'];

    // Calculate total pages
    $totalPages = ceil($totalStudents / $studentsPerPage);

    // Query to fetch students for the current page
    $sql = "SELECT * FROM students LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $offset, $studentsPerPage);
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
    <title>OJT Student Attendance</title>
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
                <h2 class="my-4">Students List</h2>

                <!-- Button to trigger the "Add Student" modal -->
                <button class="btn btn-primary my-4" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>

                <!-- Table to display students -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>School</th>
                            <th># of Hours</th>
                            <th>Start Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = $offset + 1; ?>
                            <?php while ($student = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td> 
                                    <td><?php echo $student['student_id']; ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['course']); ?></td>
                                    <td><?php echo htmlspecialchars($student['school']); ?></td>
                                    <td><?php echo htmlspecialchars($student['number_hours']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($student['start_date'])); ?></td>
                                    <td>
                                        <a href="view-student.php?id=<?php echo $student['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                        <a href="edit-student.php?id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="delete-student.php?id=<?php echo $student['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No students found.</td>
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

                <!-- Modal for adding a new student -->
                <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="add-student.php" method="POST">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">Student ID</label>
                                        <input type="number" class="form-control" id="student_id" name="student_id" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Student Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="course" class="form-label">Course</label>
                                        <input type="text" class="form-control" id="course" name="course" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="school" class="form-label">School</label>
                                        <input type="text" class="form-control" id="school" name="school" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="number_hours" class="form-label">Number of Hours</label>
                                        <input type="number" class="form-control" id="number_hours" name="number_hours" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Student</button>
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
