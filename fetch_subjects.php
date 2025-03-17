<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the course, year, and semester from the POST request
    $course = $_POST['course'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    // Debugging: Log the received values
    error_log("Received course: $course, year: $year, semester: $semester");

    // Fetch the JSON string from the student_subject table
    $stmt = $conn->prepare("SELECT subjects FROM student_subject WHERE course = ? AND year_level = ? AND semester = ?");
    $stmt->bind_param("sss", $course, $year, $semester);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Debugging: Log the fetched row
        error_log("Fetched row: " . print_r($row, true));

        // Decode the JSON string into an array
        $subjects = json_decode($row['subjects'], true);

        if ($subjects) {
            echo json_encode(['success' => true, 'data' => $subjects]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No subjects found or invalid JSON data.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No subjects found for the given course, year, and semester.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
