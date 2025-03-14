<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $subject_id = $_POST['subject_id'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $semester = $_POST['semester'];
    $subjects = $_POST['subjects']; // Now it's a properly structured array

    // Encode subjects array to JSON
    $subjects_json = json_encode($subjects, JSON_PRETTY_PRINT); // This is the fix

    // Prepare a prepared statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO student_subject (subject_id, course, semester, year_level, subjects) 
                            VALUES (?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param(
        "sssss", // Correct format string
        $subject_id, 
        $course, 
        $semester, 
        $year_level, 
        $subjects_json // Use the encoded JSON string here
    );

    // Execute the query
    if ($stmt->execute()) {
        echo "Record saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>


