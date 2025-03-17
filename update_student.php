<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the request
    $data = json_decode(file_get_contents('php://input'), true);

    $studentID = $data['studentID'];
    $course = $data['course'];
    $yearLevel = $data['yearLevel'];
    $semester = $data['semester'];
    $schoolYear = $data['schoolYear'];

    // Update the student's details in the database
    $sql = "UPDATE enrollments SET course = ?, year = ?, semester = ?, school_year = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssss", $course, $yearLevel, $semester, $schoolYear, $studentID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Student details updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update student details.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }

    $conn->close();
}
?>