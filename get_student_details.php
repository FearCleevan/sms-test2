<?php
// Include database connection
include("connect.php");

// Get the student ID from the request
$studentID = $_GET['studentID'];

// Initialize an empty response array
$response = array();

// Check if the student ID is provided
if (empty($studentID)) {
    $response['success'] = false;
    $response['message'] = "Student ID is required.";
    echo json_encode($response);
    exit();
}

// Check if the student exists in the enrollments table
$query = "SELECT * FROM enrollments WHERE student_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Prepare the response data
        $response['success'] = true;
        $response['studentID'] = $row['student_id'];
        $response['department'] = $row['department'];
        $middleName = !empty($row['middle_name']) ? ' ' . $row['middle_name'] . ' ' : ' ';
        $response['name'] = $row['first_name'] . $middleName . $row['last_name'];
        $response['email'] = $row['email'];
        $response['phone'] = $row['phone'];
        $response['address'] = $row['address'];
        $response['province'] = $row['province'];

        // Handle the profile image
        if (!empty($row['profile'])) {
            // If the profile column contains a file path, use it directly
            $response['profileImg'] = $row['profile'];
        } else {
            // If no profile image is available, use a default image
            $response['profileImg'] = './uploads/default-profile.jpg';
        }

        $response['course'] = $row['course'];
        $response['year'] = $row['year'];
        $response['semester'] = $row['semester'];
        $response['school_year'] = $row['school_year']; // Include school year in the response
    } else {
        $response['success'] = false;
        $response['message'] = "Student ID not found.";
    }
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "Database error: Unable to prepare the query.";
}

// Close the database connection
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
