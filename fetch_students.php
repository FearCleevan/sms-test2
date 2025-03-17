<?php
session_start();
include("connect.php");

// Debugging: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the selected department from the query parameter
$department = $_GET['department'] ?? '';

if (!empty($department)) {
    // Debugging: Check if the department exists in the database
    $query = mysqli_query($conn, "SELECT * FROM enrollments WHERE department = '$department'");
    if (!$query) {
        die("Query failed: " . mysqli_error($conn));
    }

    $students = [];
    while ($row = mysqli_fetch_assoc($query)) {
        // Handle the profile image
        if (!empty($row['profile'])) {
            // If the profile column contains a file path, use it directly
            $row['profile'] = $row['profile'];
        } else {
            // If no profile image is available, use a default image
            $row['profile'] = './uploads/default-profile.jpg';
        }
        $students[] = $row;
    }

    // Debugging: Check the fetched data
    echo json_encode($students);
} else {
    echo json_encode([]);
}
?>