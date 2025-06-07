<?php
session_start();
include_once('db/db-con.php'); // Ensure connection to DB

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "
        <script>
            alert('User not logged in.');
            window.location.href = 'login.php';
        </script>";
    exit();
}

$user_email = $_SESSION['email'];

// Check if student ID is provided
if (isset($_POST['studentId'])) {
    $studentId = $_POST['studentId'];
    $label = $_POST['current-phase'];  // "Time In", "Time Out", "Overtime", etc.

 

    // Query to check if the student exists for the logged-in user
    $checkStudentSql = "SELECT id FROM students WHERE student_id = ? AND user_email = ?";
    $stmt = $conn->prepare($checkStudentSql);
    $stmt->bind_param("ss", $studentId, $user_email);
    $stmt->execute();
    $studentResult = $stmt->get_result();

    if ($studentResult && $studentResult->num_rows > 0) {
        // Check if the student has already Time In or Time Out for today
        $attendanceCheckSql = "
            SELECT id, time_out FROM attendance 
            WHERE student_id = ? AND user_email = ? 
            AND DATE(time_in) = CURDATE() 
            LIMIT 1";

        $stmtAttendance = $conn->prepare($attendanceCheckSql);
        $stmtAttendance->bind_param("ss", $studentId, $user_email);
        $stmtAttendance->execute();
        $attendanceResult = $stmtAttendance->get_result();

        if ($attendanceResult && $attendanceResult->num_rows > 0) {
            // Student has a time-in record for today
            $attendance = $attendanceResult->fetch_assoc();
            
            if ($label == "Time Out") {
                if (!empty($attendance['time_out'])) {
                    // Prevent multiple "Time Out" on the same day
                    echo "
                        <script>
                            alert('You have already Time Out for today.');
                            window.location.href = 'attendance.php';
                        </script>";
                } else {
                    // Update the time_out value and session_info for the active attendance record
                    $currentDateTime = date('Y-m-d H:i:s'); // Get the current timestamp
                    $sessionInfo = "Present Today"; // Set the session info to "Present Today"
                    
                    $updateQuery = "UPDATE attendance 
                                    SET time_out = ?, session_info = ? 
                                    WHERE id = ?";
                    $stmtUpdate = $conn->prepare($updateQuery);
                    $stmtUpdate->bind_param("ssi", $currentDateTime, $sessionInfo, $attendance['id']);
                
                    if ($stmtUpdate->execute()) {
                        echo "
                            <script>
                                alert('Time-out successfully recorded and session updated to Present Today!');
                                window.location.href = 'attendance.php';
                            </script>";
                    } else {
                        echo "
                            <script>
                                alert('Error updating time-out and session info: " . $conn->error . "');
                                window.location.href = 'attendance.php';
                            </script>";
                    }
                }
            } elseif ($label == "Time In") {
                // Prevent multiple "Time In" on the same day
                echo "
                    <script>
                        alert('You have already Time In for today.');
                        window.location.href = 'attendance.php';
                    </script>";
            } else {
                // Invalid action for an already existing attendance record
                echo "
                    <script>
                        alert('Invalid action for today. Please check your attendance status.');
                        window.location.href = 'attendance.php';
                    </script>";
            }
        } else {
            // Insert a new record since no Time In or Time Out exists
            if ($label == "Time In") {
                $date = date('Y-m-d'); // Current date
                $timeIn = date('H:i:s'); // Current time
                $sessionInfo = "Time In"; // Define session info
        
                $insertAttendanceSql = "
                    INSERT INTO attendance (student_id, date, time_in, user_email, session_info) 
                    VALUES (?, ?, ?, ?, ?)";
                $stmtInsert = $conn->prepare($insertAttendanceSql);
                $stmtInsert->bind_param("sssss", $studentId, $date, $timeIn, $user_email, $sessionInfo);
        
                if ($stmtInsert->execute()) {
                    echo "
                        <script>
                            alert('Time In successfully recorded!');
                            window.location.href = 'attendance.php';
                        </script>";
                } else {
                    echo "
                        <script>
                            alert('Error occurred while recording Time In.');
                            window.location.href = 'attendance.php';
                        </script>";
                }
            } else {
                // Invalid label or action
                echo "
                    <script>
                        alert('Invalid action. Time In is required first.');
                        window.location.href = 'attendance.php';
                    </script>";
            }
        }
        
        
    } else {
        echo "
            <script>
                alert('Student ID not found for this user.');
                window.location.href = 'attendance.php';
            </script>";
    }
} else {
    echo "
        <script>
            alert('Student ID is required.');
            window.location.href = 'attendance.php';
        </script>";
}
?>
