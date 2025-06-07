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
if (!$data || !isset($data['studentId'], $data['date'], $data['timeIn'], $data['sessionInfo'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit();
}

$studentId = $data['studentId'];
$date = $data['date'];
$timeIn = $data['timeIn'];
$sessionInfo = $data['sessionInfo'];

// Insert into the database
$sql = "INSERT INTO attendance (student_id, date, time_in, time_out, user_email, session_info) 
        VALUES (?, ?, ?, NULL, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $studentId, $date, $timeIn, $user_email, $sessionInfo);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Attendance record saved"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save attendance record"]);
}

$stmt->close();
$conn->close();
?>
