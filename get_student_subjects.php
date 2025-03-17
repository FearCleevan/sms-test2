<?php
// Include database connection
include("connect.php");

// Get the course and year level from the request
$course = $_GET['course'];
$year_level = $_GET['year_level'];

// Initialize an empty response array
$response = array();

// Fetch subjects based on course and year level
$querySubjects = "SELECT subjects FROM student_subject WHERE course = ? AND year_level = ?";
if ($stmt = $conn->prepare($querySubjects)) {
    $stmt->bind_param("ss", $course, $year_level);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $subjects = json_decode($row['subjects'], true); // Decode the JSON array

        if (is_array($subjects)) {
            $response['success'] = true;
            $response['subjects'] = $subjects;
        } else {
            $response['success'] = false;
            $response['message'] = "Invalid subjects data format.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "No subjects found for this course and year level.";
    }
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "Error preparing statement.";
}

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>