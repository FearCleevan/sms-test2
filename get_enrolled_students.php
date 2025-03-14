<?php
session_start();
include("connect.php");

if (!isset($_SESSION['username'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

if (!isset($_GET['subject_id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Subject ID is required');
}

$subject_id = $_GET['subject_id'];

$stmt = $conn->prepare("
    SELECT e.student_id, e.first_name, e.last_name, ss.grade, ss.status
    FROM enrollments e
    JOIN student_subjects ss ON e.student_id = ss.student_id
    WHERE ss.subject_id = ?
    ORDER BY e.last_name, e.first_name
");

$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$students = array();
while ($row = $result->fetch_assoc()) {
    $students[] = array(
        'student_id' => $row['student_id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'grade' => $row['grade'],
        'status' => $row['status']
    );
}

header('Content-Type: application/json');
echo json_encode($students);
?>
