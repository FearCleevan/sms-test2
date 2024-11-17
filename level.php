<?php
// Include database connection file
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_level = null;
    $track = null;
    $course = null;
    $course_level = null;
    $lrn = null;
    $student_id = $_POST['student_id']; // Get generated student ID

    // Handle Grade 7 to Grade 10
    if (isset($_POST['grade_level_course'])) {
        $grade_level = $_POST['grade_level_course']; // Grade 7 to Grade 10
        if (in_array($grade_level, ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'])) {
            $lrn = $_POST['lrn'] ?? null; // Get LRN if available
        }
    }

    // Handle Grade 11 and 12 with Tracks
    if (isset($_POST['track_grade_11']) && $_POST['grade_level_course'] === 'Grade 11') {
        $grade_level = 'Grade 11';
        $track = $_POST['track_grade_11'];
    }

    if (isset($_POST['track_grade_12']) && $_POST['grade_level_course'] === 'Grade 12') {
        $grade_level = 'Grade 12';
        $track = $_POST['track_grade_12'];
    }

    // Handle TVET with Tracks
    if (isset($_POST['track_tvet']) && $_POST['grade_level_course'] === 'TVET') {
        $grade_level = 'TVET';
        $track = $_POST['track_tvet'];
        $lrn = null; // No LRN for TVET
    }

    // Handle College with Course and Year Level
    if (isset($_POST['course_college']) && $_POST['grade_level_course'] === 'COLLEGE') {
        $course = $_POST['course_college'];
        $track = $_POST['course_college'];
        $course_level = $_POST['course_level'];
        $lrn = null; // No LRN for College
    }

    // Handle Profile Image Upload
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        // Get the file info
        $imageName = $_FILES['profile']['name'];
        $imageTmpName = $_FILES['profile']['tmp_name'];
        $imageSize = $_FILES['profile']['size'];
        $imageType = $_FILES['profile']['type'];

        // Read the image file as binary data
        $imageData = file_get_contents($imageTmpName);

        // Ensure the image is a valid file type (optional, you can expand the validation)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imageType, $allowedTypes)) {
            die("Invalid image type. Please upload a JPG, PNG, or GIF image.");
        }

        // You can store the image type or name if you want
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
    } else {
        $imageData = null; // No image selected
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, grade_level, track, course, course_level, lrn, profile) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $student_id, $grade_level, $track, $course, $course_level, $lrn, $imageData);

    if ($stmt->execute()) {
        $message = "Enrollment data saved successfully! Student ID: $student_id";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Form</title>
    <style>
        .enroll-fields {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .enrol-input-fields {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        label {
            width: 80px;
        }

        select,
        input[type="checkbox"] {
            margin-left: 10px;
        }
    </style>
    <script>
        function generateStudentID() {
            const year = new Date().getFullYear().toString().slice(-2); // Get the last two digits of the year
            const gradeLevel = document.querySelector('input[name="grade_level_course"]:checked');
            const courseCollege = document.getElementById('courseCollege').value;
            const track11 = document.getElementById('trackGrade11').value;
            const track12 = document.getElementById('trackGrade12').value;
            const tvetTrack = document.getElementById('trackTvet').value;
            const lrnField = document.getElementById('lrn'); // LRN field

            let prefix = '';
            if (gradeLevel) {
                const value = gradeLevel.value;
                if (value.startsWith('Grade')) {
                    prefix = value === 'Grade 11' ? 'GR11' : value === 'Grade 12' ? 'GR12' : `GR${value.slice(-2)}`;
                } else if (value === 'COLLEGE') {
                    prefix = courseCollege;
                } else if (value === 'TVET') {
                    prefix = 'TVET';
                }
            }

            // Get the last student number dynamically (placeholder, fetch from backend/database)
            const lastNumber = 1; // Replace with the last student number fetched dynamically
            const studentNumber = String(lastNumber).padStart(2, '0'); // Ensure 2 digits (e.g., 01, 02)

            // Generate final ID
            const studentID = `${prefix}${year}${studentNumber}`;
            document.getElementById('studentID').value = studentID;

            // Enable LRN field for Grade 7 to Grade 12, otherwise disable it
            if (gradeLevel && gradeLevel.value.startsWith('Grade')) {
                lrnField.disabled = false;
            } else {
                lrnField.disabled = true;
                lrnField.value = ''; // Clear LRN value when disabled
            }
        }

        function handleCheckboxChange(id) {
            const checkbox = document.getElementById(id);
            const trackField = document.getElementById(`track${id.charAt(0).toUpperCase() + id.slice(1)}`);
            const courseField = document.getElementById('courseCollege');
            const courseLevelField = document.getElementById('courseLevel');
            const lrnField = document.getElementById('lrn'); // LRN field

            // Deselect other checkboxes based on selected one
            const gradeLevels = ['grade7', 'grade8', 'grade9', 'grade10', 'grade11', 'grade12'];
            const tvetAndCollege = ['tvet', 'college'];

            gradeLevels.forEach((item) => {
                if (item !== id) document.getElementById(item).checked = false;
            });
            tvetAndCollege.forEach((item) => {
                if (item !== id) document.getElementById(item).checked = false;
            });

            // Disable all select fields initially
            document.getElementById('trackGrade11').disabled = true;
            document.getElementById('trackGrade12').disabled = true;
            document.getElementById('trackTvet').disabled = true;
            document.getElementById('courseCollege').disabled = true;
            document.getElementById('courseLevel').disabled = true;

            // Enable the LRN field only for grade levels
            if (['grade7', 'grade8', 'grade9', 'grade10', 'grade11', 'grade12'].includes(id)) {
                lrnField.disabled = false;
            } else {
                lrnField.disabled = true;
                lrnField.value = ''; // Clear LRN value when disabled
            }

            // Enable the select field associated with the checked checkbox
            if (id === 'grade11') {
                document.getElementById('trackGrade11').disabled = false;
            } else if (id === 'grade12') {
                document.getElementById('trackGrade12').disabled = false;
            } else if (id === 'tvet') {
                document.getElementById('trackTvet').disabled = false;
            } else if (id === 'college') {
                document.getElementById('courseCollege').disabled = false;
                document.getElementById('courseLevel').disabled = false;
            }

            // Trigger student ID generation
            generateStudentID();
        }
    </script>


</head>

<body>
    <h1>Enrollment Form</h1>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="enroll-fields">
            <div class="enrol-input-fields">
                <label for="grade7">Grade 7</label>
                <input type="checkbox" id="grade7" name="grade_level_course" value="Grade 7" onclick="handleCheckboxChange('grade7')">
            </div>

            <div class="enrol-input-fields">
                <label for="grade8">Grade 8</label>
                <input type="checkbox" id="grade8" name="grade_level_course" value="Grade 8" onclick="handleCheckboxChange('grade8')">
            </div>

            <div class="enrol-input-fields">
                <label for="grade9">Grade 9</label>
                <input type="checkbox" id="grade9" name="grade_level_course" value="Grade 9" onclick="handleCheckboxChange('grade9')">
            </div>

            <div class="enrol-input-fields">
                <label for="grade10">Grade 10</label>
                <input type="checkbox" id="grade10" name="grade_level_course" value="Grade 10" onclick="handleCheckboxChange('grade10')">
            </div>

            <div class="enrol-input-fields">
                <label for="grade11">Grade 11</label>
                <input type="checkbox" id="grade11" name="grade_level_course" value="Grade 11" onclick="handleCheckboxChange('grade11')">
                <select id="trackGrade11" name="track_grade_11" disabled>
                    <option value="">Select Track</option>
                    <option value="GAS">GAS</option>
                    <option value="STEM">STEM</option>
                    <option value="WAS">WAS</option>
                </select>
            </div>

            <div class="enrol-input-fields">
                <label for="grade12">Grade 12</label>
                <input type="checkbox" id="grade12" name="grade_level_course" value="Grade 12" onclick="handleCheckboxChange('grade12')">
                <select id="trackGrade12" name="track_grade_12" disabled>
                    <option value="">Select Track</option>
                    <option value="GAS">GAS</option>
                    <option value="STEM">STEM</option>
                    <option value="WAS">WAS</option>
                </select>
            </div>

            <div class="enrol-input-fields">
                <label for="tvet">TVET</label>
                <input type="checkbox" id="tvet" name="grade_level_course" value="TVET" onclick="handleCheckboxChange('tvet')">
                <select id="trackTvet" name="track_tvet" disabled>
                    <option value="">Select Track</option>
                    <option value="Automotive">Automotive</option>
                    <option value="Front Office">Front Office</option>
                    <option value="Sample">Sample</option>
                </select>
            </div>

            <div class="enrol-input-fields">
                <label for="college">College</label>
                <input type="checkbox" id="college" name="grade_level_course" value="COLLEGE" onclick="handleCheckboxChange('college')">
                <select id="courseCollege" name="course_college" disabled>
                    <option value="">Select Course</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSHM">BSHM</option>
                    <option value="BSBA">BSBA</option>
                    <option value="BSTM">BSTM</option>
                </select>
                <select id="courseLevel" name="course_level" disabled>
                    <option value="">Select Level</option>
                    <option value="1stYear">1st Year</option>
                    <option value="2ndYear">2nd Year</option>
                    <option value="3rdYear">3rd Year</option>
                    <option value="4thYear">4th Year</option>
                </select>
            </div>

            <div class="enrol-input-fields">
                <label for="lrn">LRN: (JHS/SHS only)</label>
                <input type="text" id="lrn" name="lrn" disabled>
            </div>

            <div class="profile-photo-container">
                <span>Profile Image</span>
                <div class="profile-photo">
                    <img id="profileDisplay" src="" alt="Profile Photo" style="display: none;">
                </div>
                <label for="profile" class="upload-label">
                    <span id="uploadText">SELECT NEW PHOTO</span>
                </label>
                <input type="file" id="profile" name="profile" accept="image/*" style="display: none;" onchange="previewImage(event)">
            </div>

            <script>
                // Function to preview the selected profile image
                function previewImage(event) {
                    var reader = new FileReader();
                    reader.onload = function() {
                        var output = document.getElementById('profileDisplay');
                        output.style.display = 'block';
                        output.src = reader.result;
                    }
                    reader.readAsDataURL(event.target.files[0]);
                }
            </script>

            <!-- Student ID Field -->
            <input type="hidden" id="studentID" name="student_id" value="">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>

</html>