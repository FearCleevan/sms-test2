<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the request
    $data = json_decode(file_get_contents('php://input'), true);

    $studentID = $data['studentID'];
    $subject_id = $data['subject_id'];
    $status = $data['status'];

    // Update the enrollments table
    $sql = "UPDATE enrollments SET subject_id = ?, status = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $subject_id, $status, $studentID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Student enrolled successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to enroll student.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>