<?php
session_start();
include_once('db/db-con.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve the input values
    $establishment_name = htmlspecialchars(trim($_POST['establishment_name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $contact_person = htmlspecialchars(trim($_POST['contact_person']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $usertype = isset($_POST['usertype']) ? (int)$_POST['usertype'] : 1; // Default to 'user' type

    // Generate a random password for the new establishment (could also be set by the user)
    $password = password_hash("defaultpassword", PASSWORD_DEFAULT); // Placeholder password

    // Check if required fields are provided and email is valid
    if ($establishment_name && $address && $contact_person && $phone && $email) {
        // Prepare the SQL statement to insert the data into the establishments table
        $sql = "INSERT INTO establishments (establishment_name, address, contact_person, phone, email, password, usertype, createAt) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $establishment_name, $address, $contact_person, $phone, $email, $password, $usertype);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to the establishments list page with a success message
            $_SESSION['success'] = "Establishment added successfully!";
            echo "<script>window.location.href = 'users.php';</script>";
            exit;
        } else {
            $_SESSION['error'] = "Failed to add establishment. Please try again.";
            echo "<script>window.location.href = 'users.php';</script>";
            exit;
        }
    } else {
        $_SESSION['error'] = "Please fill in all required fields and provide a valid email.";
        echo "<script>window.location.href = 'users.php';</script>";
        exit;
    }
} else {
    // If the request is not POST, redirect to the establishments list page
    echo "<script>window.location.href = 'users.php';</script>";
    exit;
}
?>
