<?php
session_start();
include("connect.php");

if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false, 'message' => '');

    try {
        $subject_id = $_POST['subject_id'];
        $date = $_POST['date'];
        $attendance_data = json_decode($_POST['attendance_data'], true);

        // Start transaction
        $conn->begin_transaction();

        foreach ($attendance_data as $record) {
            $student_id = $record['student_id'];
            $status = $record['status'];
            $remarks = $record['remarks'] ?? null;

            // Check if attendance record already exists
            $stmt = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND subject_id = ? AND date = ?");
            $stmt->bind_param("sis", $student_id, $subject_id, $date);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing record
                $stmt = $conn->prepare("UPDATE attendance SET status = ?, remarks = ? WHERE student_id = ? AND subject_id = ? AND date = ?");
                $stmt->bind_param("sssis", $status, $remarks, $student_id, $subject_id, $date);
            } else {
                // Insert new record
                $stmt = $conn->prepare("INSERT INTO attendance (student_id, subject_id, date, status, remarks) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sisss", $student_id, $subject_id, $date, $status, $remarks);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error recording attendance: " . $stmt->error);
            }
        }

        // Commit transaction
        $conn->commit();
        
        $response['success'] = true;
        $response['message'] = "Attendance recorded successfully!";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $response['message'] = $e->getMessage();
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get attendance records
if (isset($_GET['subject_id']) && isset($_GET['date'])) {
    $subject_id = $_GET['subject_id'];
    $date = $_GET['date'];

    $sql = "SELECT a.*, CONCAT(e.first_name, ' ', e.last_name) as student_name 
            FROM attendance a 
            JOIN enrollments e ON a.student_id = e.student_id 
            WHERE a.subject_id = ? AND a.date = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $subject_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attendance_records = array();
    while ($row = $result->fetch_assoc()) {
        $attendance_records[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($attendance_records);
    exit();
}

// Get students enrolled in a subject
if (isset($_GET['get_students']) && isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    $sql = "SELECT e.student_id, CONCAT(e.first_name, ' ', e.last_name) as student_name 
            FROM enrollments e 
            JOIN student_subjects ss ON e.student_id = ss.student_id 
            WHERE ss.subject_id = ? AND ss.status = 'enrolled'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = array();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($students);
    exit();
}
?>
