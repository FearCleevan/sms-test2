<?php
// Include database connection
include("connect.php");

// Fetch data from the database
$sql = "SELECT * FROM student_subject";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Enrolled Subjects</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .no-records {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }

        .subject-details {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enrolled Subjects</h1>
        
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Loop through each row in the result set
            while ($row = $result->fetch_assoc()) {
                // Decode subjects JSON
                $subjects = json_decode($row['subjects'], true); // Decode JSON subjects
                
                // Retrieve subject details from the database
                $subject_id = isset($row['subject_id']) ? htmlspecialchars($row['subject_id']) : 'N/A';
                $course = isset($row['course']) ? htmlspecialchars($row['course']) : 'N/A';
                $year_level = isset($row['year_level']) ? htmlspecialchars($row['year_level']) : 'N/A';
                $semester = isset($row['semester']) ? htmlspecialchars($row['semester']) : 'N/A';  // Semester from row

                // Check for each course and group the subjects accordingly
                $courses = ['BSIT', 'BSHM', 'BSBA', 'BSTM', 'BTVTeD-AT', 'BTVTeD-HVACR TECH', 'BTVTeD-FSM', 'BTVTeD-ETn'];

                if (in_array($course, $courses)) {
                    // Group subjects by year level and semester
                    $grouped_subjects = [];
                    foreach ($subjects as $subject) {
                        $subject_semester = isset($subject['semester']) ? $subject['semester'] : $semester; // Use decoded semester or default to row's semester
                        $year = isset($subject['year_level']) ? $subject['year_level'] : 'N/A';
                        $grouped_subjects[$year][$subject_semester][] = $subject;
                    }

                    // Print the subject details based on year and semester for each course
                    foreach ($grouped_subjects as $year => $semesters) {
                        foreach ($semesters as $sem => $subjects_in_sem) {
                            echo "<div class='subject-details'>";
                            echo "<h2>Course: $course</h2>";
                            echo "<h2>Year: $year_level</h2>";
                            echo "<h2>Semester: $sem</h2>";  // Use actual semester here

                            // Create the table for each semester
                            echo "<table>
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Description</th>
                                            <th>Lec</th>
                                            <th>Lab</th>
                                            <th>Units</th>
                                            <th>Pre-Req</th>
                                        </tr>
                                    </thead>
                                    <tbody>";

                            // Display the subjects for the current year and semester
                            foreach ($subjects_in_sem as $subject) {
                                // Ensure all keys exist, otherwise set to 'N/A'
                                $subject_code = isset($subject['subject_code']) ? htmlspecialchars($subject['subject_code']) : 'N/A';
                                $description = isset($subject['description']) ? htmlspecialchars($subject['description']) : 'N/A';
                                $lec = isset($subject['lec']) ? htmlspecialchars($subject['lec']) : 'N/A';
                                $lab = isset($subject['lab']) ? htmlspecialchars($subject['lab']) : 'N/A';
                                $units = isset($subject['unit_no']) ? htmlspecialchars($subject['unit_no']) : 'N/A';
                                $pre_req = isset($subject['pre_req']) ? htmlspecialchars($subject['pre_req']) : 'N/A';

                                // Output each subject as a new table row
                                echo "<tr>
                                    <td>$subject_code</td>
                                    <td>$description</td>
                                    <td>$lec</td>
                                    <td>$lab</td>
                                    <td>$units</td>
                                    <td>$pre_req</td>
                                </tr>";
                            }
                            echo "</tbody></table></div>"; // Close the table and div
                        }
                    }
                }
            }
        } else {
            echo "<p class='no-records'>No records found</p>";
        }
        ?>

    </div>
</body>
</html>

<?php
$conn->close(); // Close the connection
?>
