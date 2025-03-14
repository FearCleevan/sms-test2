<?php
session_start();
include("connect.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data as arrays
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $semester = $_POST['semester'];
    $subject_codes = $_POST['subject_code'];
    $subject_names = $_POST['subject_name'];
    $lecs = $_POST['lec'];
    $labs = $_POST['lab'];
    $units = $_POST['units'];
    $pre_reqs = $_POST['pre_req'];

    // Loop through each row of data
    for ($i = 0; $i < count($subject_codes); $i++) {
        $subject_code = $subject_codes[$i];
        $subject_name = $subject_names[$i];
        $lec = $lecs[$i];
        $lab = $labs[$i];
        $unit = $units[$i];
        $pre_req = !empty($pre_reqs[$i]) ? $pre_reqs[$i] : NULL;

        // Check if pre_req exists in the subjects table (only if pre_req is not NULL)
        if ($pre_req !== NULL) {
            $check_pre_req_sql = "SELECT COUNT(*) FROM subjects WHERE subject_code = ?";
            if ($stmt = $conn->prepare($check_pre_req_sql)) {
                $stmt->bind_param("s", $pre_req);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                // If pre_req doesn't exist, show an error and skip this row
                if ($count == 0) {
                    echo "Error: The specified pre-requisite subject code ($pre_req) for subject $subject_code does not exist.<br>";
                    continue; // Skip to the next row
                }
            }
        }

        // Prepare the SQL query to insert the subject
        $sql = "INSERT INTO subjects (subject_code, subject_name, course, year_level, semester, lec, lab, units, pre_req)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind the parameters
            $stmt->bind_param("sssssiiis", $subject_code, $subject_name, $course, $year_level, $semester, $lec, $lab, $unit, $pre_req);

            // Execute the query
            if ($stmt->execute()) {
                echo "Subject $subject_code added successfully!<br>";
            } else {
                echo "Error inserting subject $subject_code: " . $stmt->error . "<br>";
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            echo "Error preparing the SQL statement: " . $conn->error . "<br>";
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Submission Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-6">Submit Subject Information</h2>

        <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-lg">

            <!-- Course Selection -->
            <div class="mb-4">
                <label for="course" class="block text-lg font-medium">Course</label>
                <select id="course" name="course" class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="BSIT">BSIT</option>
                    <option value="BSHM">BSHM</option>
                    <option value="BSBA">BSBA</option>
                    <option value="BSTM">BSTM</option>
                    <option value="BTVTeD-AT">BTVTeD-AT</option>
                    <option value="BTVTeD-HVACR TECH">BTVTeD-HVACR TECH</option>
                    <option value="BTVTeD-FSM">BTVTeD-FSM</option>
                    <option value="BTVTeD-ET">BTVTeD-ET</option>
                </select>
            </div>

            <!-- Year Level Selection -->
            <div class="mb-4">
                <label for="year_level" class="block text-lg font-medium">Year Level</label>
                <select id="year_level" name="year_level" class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="1stYear">1st Year</option>
                    <option value="2ndYear">2nd Year</option>
                    <option value="3rdYear">3rd Year</option>
                    <option value="4thYear">4th Year</option>
                </select>
            </div>

            <!-- Semester Selection -->
            <div class="mb-4">
                <label for="semester" class="block text-lg font-medium">Semester</label>
                <select id="semester" name="semester" class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="1stSem">1st Semester</option>
                    <option value="2ndSem">2nd Semester</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>

            <!-- Table for Subject Information -->
            <!-- Table for Subject Information -->
            <div class="mb-6">
                <table class="min-w-full table-auto" id="subjectTable">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">Subject Code</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Lec</th>
                            <th class="px-4 py-2">Lab</th>
                            <th class="px-4 py-2">Units</th>
                            <th class="px-4 py-2">Pre Requisite</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be dynamically added here -->
                    </tbody>
                </table>
                <!-- Button to add new rows -->
                <button type="button" onclick="addRow()" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg">Add Subject</button>
            </div>

            <script>
                function addRow() {
                    const table = document.querySelector("#subjectTable tbody");
                    const newRow = document.createElement("tr");

                    newRow.innerHTML = `
                        <td class="px-4 py-2">
                            <input type="text" name="subject_code[]" placeholder="Enter Subject Code" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="subject_name[]" placeholder="Enter Description" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="lec[]" placeholder="Enter Lec Hours" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="lab[]" placeholder="Enter Lab Hours" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="units[]" placeholder="Enter Units" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="pre_req[]" placeholder="Enter Pre Requisite (Optional)" class="w-full p-2 border border-gray-300 rounded-lg">
                        </td>
                        <td class="px-4 py-2 text-center">
                            <button type="button" onclick="removeRow(this)" class="px-2 py-1 bg-red-500 text-white rounded-lg">Remove</button>
                        </td>
                    `;

                    table.appendChild(newRow);
                }

                function removeRow(button) {
                    const row = button.closest("tr");
                    row.remove();
                }
            </script>



            <!-- Submit Button -->
            <button type="submit" class="w-full p-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">Submit Subject</button>

        </form>
    </div>

</body>

</html>