<?php
session_start();
include_once('db/db-con.php'); // Ensure connection to DB

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_email = $_SESSION['email']; // Get the logged-in user's email

// Get data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['studentId'], $data['timeOut'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit();
}

$studentId = $data['studentId'];
$timeOut = $data['timeOut'];

// Update the attendance record where `time_out` is NULL
$sql = "UPDATE attendance 
        SET time_out = ? 
        WHERE student_id = ? AND user_email = ? AND time_out IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $timeOut, $studentId, $user_email);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Time Out saved"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No active attendance record found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update attendance record"]);
}

$stmt->close();
$conn->close();
?>
