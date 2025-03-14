<?php
// Include database connection
include("connect.php");

// Get the student ID from the request
$studentID = $_GET['studentID'];

// Initialize an empty response array
$response = array();

// First, check if the student exists in the JHS table
// Check if the student exists in the JHS table
$queryJHS = "SELECT * FROM jhs_students WHERE student_id = ?";
if ($stmt = $conn->prepare($queryJHS)) {
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        
        // Check if profile is stored as BLOB (binary) data
        if ($row['profile']) {
            $response['profileImg'] = 'data:image/jpeg;base64,' . base64_encode($row['profile']);
        } else {
            // If profile image path exists, use the file path
            $response['profileImg'] = './uploads/' . $row['profile'];
        }
        
        $response['studentID'] = $row['student_id'];
        $response['name'] = $row['first_name'] . ' ' . $row['last_name'];
        $response['educationLevel'] = 'JHS';
        $response['edu_level'] = $row['edu_level'];
        $response['grade_level'] = $row['grade_level'];
    } else {
        // Same logic for SHS, TVET, and College students
        $querySHS = "SELECT * FROM shs_students WHERE student_id = ?";
        if ($stmt = $conn->prepare($querySHS)) {
            $stmt->bind_param("s", $studentID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $response['success'] = true;
                
                // Check if profile is stored as BLOB (binary) data
                if ($row['profile']) {
                    $response['profileImg'] = 'data:image/jpeg;base64,' . base64_encode($row['profile']);
                } else {
                    // If profile image path exists, use the file path
                    $response['profileImg'] = './uploads/' . $row['profile'];
                }
                
                $response['studentID'] = $row['student_id'];
                $response['name'] = $row['first_name'] . ' ' . $row['last_name'];
                $response['educationLevel'] = 'SHS';
                $response['edu_level'] = $row['edu_level'];
                $response['grade_level'] = $row['grade_level'];
                $response['track'] = $row['track'];
            } else {
                $queryTVET = "SELECT * FROM tvet_students WHERE student_id = ?";
                if ($stmt = $conn->prepare($queryTVET)) {
                    $stmt->bind_param("s", $studentID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $response['success'] = true;
                        
                        // Check if profile is stored as BLOB (binary) data
                        if ($row['profile']) {
                            $response['profileImg'] = 'data:image/jpeg;base64,' . base64_encode($row['profile']);
                        } else {
                            // If profile image path exists, use the file path
                            $response['profileImg'] = './uploads/' . $row['profile'];
                        }
                        
                        $response['studentID'] = $row['student_id'];
                        $response['name'] = $row['first_name'] . ' ' . $row['last_name'];
                        $response['educationLevel'] = 'TVET';
                        $response['track'] = $row['track'];
                        $response['track_level'] = $row['track_level'];
                    } else {
                        $queryCollege = "SELECT * FROM college_students WHERE student_id = ?";
                        if ($stmt = $conn->prepare($queryCollege)) {
                            $stmt->bind_param("s", $studentID);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $response['success'] = true;
                                
                                // Check if profile is stored as BLOB (binary) data
                                if ($row['profile']) {
                                    $response['profileImg'] = 'data:image/jpeg;base64,' . base64_encode($row['profile']);
                                } else {
                                    // If profile image path exists, use the file path
                                    $response['profileImg'] = './uploads/' . $row['profile'];
                                }
                                
                                $response['studentID'] = $row['student_id'];
                                $response['name'] = $row['first_name'] . ' ' . $row['last_name'];
                                $response['educationLevel'] = 'College';
                                $response['course'] = $row['course'];
                                $response['course_level'] = $row['course_level'];
                            } else {
                                $response['success'] = false;
                                $response['message'] = "Student ID not found.";
                            }
                        }
                    }
                }
            }
        }
    }
    $stmt->close();
}

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
