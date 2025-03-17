<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the request
    $data = json_decode(file_get_contents('php://input'), true);

    $course = $data['course'];
    $yearLevel = $data['yearLevel'];
    $semester = $data['semester'];

    // Fetch the subject_id from the student_subject table
    $stmt = $conn->prepare("SELECT subject_id FROM student_subject WHERE course = ? AND year_level = ? AND semester = ?");
    $stmt->bind_param("sss", $course, $yearLevel, $semester);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        echo json_encode(['success' => true, 'subject_id' => $row['subject_id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No subject ID found for the given course, year, and semester.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>