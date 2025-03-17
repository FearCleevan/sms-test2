<?php
session_start();
include("connect.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure the response is JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_id = $_POST['subject_id'];
    $subjects = $_POST['subjects']; // This is already a JSON string

    // Validate the JSON data
    if (json_decode($subjects) === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
        exit;
    }

    try {
        // Update the subjects JSON string in the database
        $stmt = $conn->prepare("UPDATE student_subject SET subjects = ? WHERE subject_id = ?");
        $stmt->bind_param("ss", $subjects, $subject_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Subjects updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating subjects.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>