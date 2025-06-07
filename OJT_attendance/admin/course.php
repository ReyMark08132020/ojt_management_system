<?php
session_start();
// Include the database connection
include_once('db/db-con.php');

// Fetch courses from the database
$course_sql = "SELECT * FROM courses";
$course_result = $conn->query($course_sql);

// Fetch schools from the database
$school_sql = "SELECT * FROM schools";
$school_result = $conn->query($school_sql);
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

                <!-- Add Course Button -->
                <button class="btn btn-primary my-2" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add Course</button>

                <!-- Add School Button -->
                <button class="btn btn-primary my-2" data-bs-toggle="modal" data-bs-target="#addSchoolModal">Add School</button>

                <!-- Add Course Modal -->
                <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="add-course.php" method="POST">
                                    <div class="mb-3">
                                        <label for="courseName" class="form-label">Course Name</label>
                                        <input type="text" class="form-control" id="courseName" name="course_name" required oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="mb-3">
                                        <label for="courseDescription" class="form-label">Course Description</label>
                                        <textarea class="form-control" id="courseDescription" name="course_description"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Course</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add School Modal -->
                <div class="modal fade" id="addSchoolModal" tabindex="-1" aria-labelledby="addSchoolModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSchoolModalLabel">Add New School</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="add-school.php" method="POST">
                                    <div class="mb-3">
                                        <label for="schoolName" class="form-label">School Name</label>
                                        <input type="text" class="form-control" id="schoolName" name="school_name" required oninput="this.value = this.value.toUpperCase();">
                                    </div>
                                    <div class="mb-3">
                                        <label for="schoolAddress" class="form-label">School Address</label>
                                        <input type="text" class="form-control" id="schoolAddress" name="school_address" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add School</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table for Courses -->
                <div class="mt-5">
                    <h3>Courses List</h3>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Course Name</th>
                                <th scope="col">Course Description</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamically populate rows with PHP -->
                            <?php while($course = $course_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $course['id']; ?></td>
                                    <td><?php echo $course['course_name']; ?></td>
                                    <td><?php echo $course['course_description']; ?></td>
                                    <td>
                                    <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete-course.php?id=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table for Schools -->
                <div class="mt-5">
                    <h3>Schools List</h3>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">School Name</th>
                                <th scope="col">School Address</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamically populate rows with PHP -->
                            <?php while($school = $school_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $school['id']; ?></td>
                                    <td><?php echo $school['school_name']; ?></td>
                                    <td><?php echo $school['school_address']; ?></td>
                                    <td>
                                    <a href="edit-school.php?id=<?php echo $school['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete-school.php?id=<?php echo $school['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this school?')">Delete</a>
            </td>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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

<?php
// Close the database connection
$conn->close();
?>
