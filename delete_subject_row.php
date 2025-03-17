<?php
session_start();
include("connect.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure the response is JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_id = $_POST['subject_id'];
    $subject_code = $_POST['subject_code'];

    try {
        // Fetch the current subjects JSON string
        $stmt = $conn->prepare("SELECT subjects FROM student_subject WHERE subject_id = ?");
        $stmt->bind_param("s", $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $subjects = json_decode($row['subjects'], true);

            // Filter out the subject to be deleted
            $updatedSubjects = array_filter($subjects, function ($subject) use ($subject_code) {
                return $subject['subject_code'] !== $subject_code;
            });

            // Update the subjects JSON string in the database
            $updatedSubjectsJson = json_encode(array_values($updatedSubjects)); // Re-index the array
            $stmt = $conn->prepare("UPDATE student_subject SET subjects = ? WHERE subject_id = ?");
            $stmt->bind_param("ss", $updatedSubjectsJson, $subject_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Subject deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting subject.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No subjects found for the given subject ID.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>