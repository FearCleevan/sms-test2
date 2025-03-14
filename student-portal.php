<?php
session_start();
include("connect.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student information
$stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Get enrolled subjects
$stmt = $conn->prepare("
    SELECT s.subject_code, s.subject_name, s.units, ss.grade, ss.status
    FROM student_subjects ss
    JOIN subjects s ON ss.subject_id = s.id
    WHERE ss.student_id = ? AND ss.status = 'enrolled'
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$subjects = $stmt->get_result();

// Get payment history
$stmt = $conn->prepare("
    SELECT amount, payment_type, payment_method, payment_date, status, reference_number
    FROM payments
    WHERE student_id = ?
    ORDER BY payment_date DESC
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$payments = $stmt->get_result();

// Calculate GPA
$total_units = 0;
$total_grade_points = 0;
$gpa = 0;

$grades_query = $conn->prepare("
    SELECT s.units, ss.grade
    FROM student_subjects ss
    JOIN subjects s ON ss.subject_id = s.id
    WHERE ss.student_id = ? AND ss.grade IS NOT NULL
");
$grades_query->bind_param("s", $student_id);
$grades_query->execute();
$grades_result = $grades_query->get_result();

while ($grade_row = $grades_result->fetch_assoc()) {
    $total_units += $grade_row['units'];
    $total_grade_points += ($grade_row['grade'] * $grade_row['units']);
}

if ($total_units > 0) {
    $gpa = $total_grade_points / $total_units;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f0f2f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .student-details h1 {
            font-size: 24px;
            color: #1a1a1a;
        }

        .student-details p {
            color: #666;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            color: #1a1a1a;
            margin-bottom: 15px;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-enrolled {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .gpa-display {
            font-size: 24px;
            font-weight: 600;
            color: #1976d2;
            text-align: center;
            margin: 10px 0;
        }

        .payment-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .payment-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .payment-pending {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .payment-failed {
            background-color: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="student-info">
                <img src="<?php echo $student['profile'] ? 'data:image/jpeg;base64,' . base64_encode($student['profile']) : './image/default-profile.png'; ?>" alt="Profile" class="profile-image">
                <div class="student-details">
                    <h1><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h1>
                    <p>Student ID: <?php echo htmlspecialchars($student['student_id']); ?></p>
                    <p>Course: <?php echo htmlspecialchars($student['course'] ?? $student['grade_level']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($student['email']); ?></p>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card">
                <h2>Enrolled Subjects</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject</th>
                            <th>Units</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($subject = $subjects->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($subject['units']); ?></td>
                            <td><?php echo $subject['grade'] ? number_format($subject['grade'], 2) : 'N/A'; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($subject['status']); ?>">
                                    <?php echo ucfirst($subject['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Academic Performance</h2>
                <div class="gpa-display">
                    GPA: <?php echo number_format($gpa, 2); ?>
                </div>
                <!-- Add more academic performance metrics here -->
            </div>

            <div class="card">
                <h2>Payment History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                            <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo ucfirst($payment['payment_type']); ?></td>
                            <td><?php echo ucfirst($payment['payment_method']); ?></td>
                            <td>
                                <span class="payment-status payment-<?php echo strtolower($payment['status']); ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Add any interactive features here
    </script>
</body>
</html>
