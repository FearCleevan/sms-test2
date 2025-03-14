<?php
session_start();
include("connect.php");

if (!isset($_SESSION['username'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

if (!isset($_GET['department_id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Department ID is required');
}

$department_id = $_GET['department_id'];

$stmt = $conn->prepare("
    SELECT id, subject_code, subject_name
    FROM subjects
    WHERE department_id = ?
    ORDER BY subject_code
");

$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = array();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

header('Content-Type: application/json');
echo json_encode($subjects);
?>
