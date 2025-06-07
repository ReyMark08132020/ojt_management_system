<?php
// Start the session and include the database connection
session_start();
include('db/db-con.php');

// Variable to store the system message
$message = '';

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Get the logged-in user's email
    $user_email = $_SESSION['email'];

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the student ID from the form
        $student_id = $_POST['student_id'];

        // Get the current date, time, and session from the hidden fields
        $current_date = date('Y-m-d');  // Format: YYYY-MM-DD
        $current_time = $_POST['current_time'];  // Format: HH:MM:SS
        $session_info = $_POST['session_info'];  // The session label (e.g., "Time In for Morning")
       
        // Check if the student ID exists and is associated with the logged-in establishment
        $check_query = "SELECT student_id FROM students WHERE student_id = ? AND user_email = ?";
        
        if ($stmt = $conn->prepare($check_query)) {
            // Bind the student ID and establishment email parameters
            $stmt->bind_param('ss', $student_id, $user_email);
            
            // Execute the query
            $stmt->execute();
            
            // Store the result
            $stmt->store_result();
            
            // Check if the student ID exists and belongs to the logged-in establishment
            if ($stmt->num_rows > 0) {
                // Student ID exists for this establishment, check if the student already has a record for today
                $attendance_check_query = "SELECT * FROM attendance WHERE student_id = ? AND date = ?";
                
                if ($stmt_check = $conn->prepare($attendance_check_query)) {
                    // Bind the parameters
                    $stmt_check->bind_param('ss', $student_id, $current_date);
                    $stmt_check->execute();
                    $result = $stmt_check->get_result();

                    // Check if there's already a record for today
                    if ($result->num_rows > 0) {
                        // Record exists, check if the student is timing out or in
                        $attendance = $result->fetch_assoc();

                        if ($attendance['time_out'] === NULL && ($session_info == "Time In for Morning" || $session_info == "Time In for Afternoon")) {
                            // Student already Time In, update the record to Time Out
                            $update_query = "UPDATE attendance SET time_out = ?, session_info = ? WHERE student_id = ? AND date = ?";
                            if ($update_stmt = $conn->prepare($update_query)) {
                                $update_stmt->bind_param('ssss', $current_time, $session_info, $student_id, $current_date);
                                if ($update_stmt->execute()) {
                                    echo "<script>
                                    alert('$session_info successful!');
                                    window.location.href = 'attendance.php';
                                  </script>";
                                } else {
                                    echo "<script>
                                    alert('Error updating record');
                                    window.location.href = 'attendance.php';
                                  </script>";
                                }
                            }
                        } else {
                            echo "<script>
                                alert('Warning! You have already timed in today');
                                window.location.href = 'attendance.php';
                              </script>";
                        }
                    } else {
                        // No attendance record for today, insert new record (Time In)
                        $insert_query = "INSERT INTO attendance (student_id, date, time_in, session_info, user_email) VALUES (?, ?, ?, ?, ?)";
                        
                        if ($stmt_insert = $conn->prepare($insert_query)) {
                            // Bind the parameters for the insert query
                            $stmt_insert->bind_param('sssss', $student_id, $current_date, $current_time, $session_info, $user_email);
                            
                            // Execute the insert query
                            if ($stmt_insert->execute()) {
                                echo "<script>
                                alert('$session_info successful!');
                                window.location.href = 'attendance.php';
                              </script>";
                            } else {
                                echo "<script>
                                alert('Error inserting record');
                                window.location.href = 'attendance.php';
                              </script>";
                            }
                        }
                    }

                    // Close the attendance check statement
                    $stmt_check->close();
                }
            } else {
                // Student ID does not exist or does not belong to this establishment
                echo "<script>
                    alert('Student ID does not exist or is not associated with this establishment');
                    window.location.href = 'attendance.php';
                  </script>";
            }

            // Close the prepared statement for checking the student ID
            $stmt->close();
        } else {
            // Error preparing the check query
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Could not prepare the check query.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        }
    }
} else {
    echo "<h1>Please log in to access this feature.</h1>";
    exit;
}

// Close the database connection
$conn->close();
?>

<!-- Display the system message -->
<?php if (!empty($message)): ?>
    <div class="container mt-4">
        <?php echo $message; ?>
    </div>
<?php endif; ?> 
