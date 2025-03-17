<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_id = $_POST['subject_id'];

    $stmt = $conn->prepare("DELETE FROM student_subject WHERE subject_id = ?");
    $stmt->bind_param("s", $subject_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting subject.']);
    }
}
?>