<?php
session_start();
include("connect.php");

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['find_subject'])) {
        $subject_id = $_POST['subject_id'];
        $stmt = $conn->prepare("SELECT * FROM student_subject WHERE subject_id = ?");
        $stmt->bind_param("s", $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = $result->fetch_all(MYSQLI_ASSOC);
    } elseif (isset($_POST['update_subject'])) {
        $subject_id = $_POST['subject_id'];
        $subjects = json_decode($_POST['subjects'], true);
        foreach ($subjects as $subject) {
            $stmt = $conn->prepare("UPDATE student_subject SET course = ?, year_level = ?, semester = ?, subjects = ? WHERE subject_id = ?");
            $stmt->bind_param("sssss", $subject['course'], $subject['year_level'], $subject['semester'], json_encode($subject['subjects']), $subject_id);
            $stmt->execute();
        }
        echo "<script>alert('Subjects updated successfully.'); window.location.href = 'add-subjects.php';</script>";
    } elseif (isset($_POST['delete_subject'])) {
        $subject_id = $_POST['subject_id'];
        $stmt = $conn->prepare("DELETE FROM student_subject WHERE subject_id = ?");
        $stmt->bind_param("s", $subject_id);
        $stmt->execute();
        echo "<script>alert('Subject deleted successfully.'); window.location.href = 'add-subjects.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Student</title>
    <link rel="stylesheet" href="enroll-student.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./image/apple-touch-icon.png">
</head>

<body>
    <!-- SIDEBAR MENU -->
    <div class="sidebar-menu">
        <div class="sidebar-logo-container">
            <h3 id="dashboard-title" class="sidebar-title" style="padding-top: 20px; padding-left: 40px;">Subjects</h3>
        </div>
        <ul class="nav-list">
            <li><a href="./dashboard.php" data-title="Dashboard"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a></li>
            <li><a href="./sample-details.php" data-title="Enroll a Student"><i class="fa-solid fa-user-plus"></i> <span>Enroll a Student</span></a></li>
            <li><a href="./department.php" data-title="Department"><i class="fa-solid fa-building"></i> <span>Department</span></a></li>
            <li><a href="#course" data-title="Course"><i class="fa-solid fa-book"></i> <span>Course</span></a></li>
            <li><a href="./add-subjects.php" data-title="Subjects" class="active"><i class="fa-solid fa-book-open"></i> <span>Subjects</span></a></li>
            <li><a href="#payment-management" data-title="Payment Management"><i class="fa-solid fa-credit-card"></i> <span>Payment Management</span></a></li>
            <li><a href="#grading-system" data-title="Grading System"><i class="fa-solid fa-graduation-cap"></i> <span>Grading System</span></a></li>
            <li><a href="#student-attendance" data-title="Student Attendance"><i class="fa-solid fa-calendar-check"></i> <span>Student Attendance</span></a></li>
            <li><a href="#announcement" data-title="Announcement"><i class="fa-solid fa-bullhorn"></i> <span>Announcement</span></a></li>
        </ul>
    </div>
    <!-- SIDEBAR MENU -->

    <!-- HEADER -->
    <div class="admin-header">
        <div class="header-container">
            <div class="sidebar-logo">
                <img src="./image/apple-touch-icon.png" alt="Samson Admin Logo" class="logo-img">
                <span class="sidebar-titles">Samson Admin</span>
                <i class="fa-solid fa-bars" id="toggle-menu-btn" class="toggle-btn"></i>
            </div>
            <div class="header-right">
                <div class="header-value-right">
                    <div class="profile-image">
                        <a href="javascript:void(0);" id="profile-link">
                            <?php
                            if (isset($_SESSION['username'])) {
                                $email = $_SESSION['username'];

                                if ($conn && mysqli_ping($conn)) {
                                    $query = mysqli_query($conn, "SELECT * FROM admin_user WHERE username='$email'");

                                    if ($row = mysqli_fetch_assoc($query)) {
                                        if (!empty($row['profile'])) {
                                            echo '<img src="' . htmlspecialchars($row['profile']) . '" alt="Profile Image">';
                                        } else {
                                            echo '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">';
                                        }
                                    } else {
                                        echo '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">';
                                    }
                                } else {
                                    echo '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">';
                                }
                            } else {
                                echo '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">';
                            }
                            ?>
                        </a>
                    </div>


                    <div class="name-access">
                        <?php
                        if (isset($_SESSION['username'])) {
                            $email = $_SESSION['username'];
                            $query = mysqli_query($conn, "SELECT * FROM admin_user WHERE username='$email'");

                            if ($row = mysqli_fetch_assoc($query)) {
                                echo "<p>" . htmlspecialchars($row['firstName']) . " " . htmlspecialchars($row['lastName']) . "</p>";
                                if (!empty($row['access'])) {
                                    echo "<span>" . htmlspecialchars($row['access']) . "</span>";
                                }
                            } else {
                                echo "<p>Guest</p><span>Guest</span>";
                            }
                        } else {
                            echo "<p>Guest</p><span>Guest</span>";
                        }
                        ?>
                    </div>

                    <!-- Dropdown Menu -->
                    <div class="profile-menu" id="profile-menu">
                        <ul>
                            <li><a href="user-info.php"><i class="fas fa-cogs"></i> Account Settings</a></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                    <!-- Dropdown Menu -->

                </div>
            </div>
        </div>
    </div>
    <!-- HEADER -->

    <!-- MAIN CONTAINER -->
    <div class="main-container">
        <div class="sub-main-container">
            <div class="school-calendar-header">
                <img src="./image/apple-touch-icon.png" alt="College Logo" class="college-logo">
                <h1 class="college-name">SAMSON POLYTECHNIC COLLEGE of DAVAO</h1>
                <p class="college-address">(Formerly Samson Technical Institute)</p>
                <p class="college-address">R. Magsaysay Avenue, 8000 Davao City</p>
                <p class="college-address">Tel. No. (082) 227-2392</p>
                <h2 class="department-title">STUDENT'S INFORMATION SHEET</h2>
            </div>

            <div class="main-container-header">
                <div class="sub-main-container-header start">
                    <i class="fa-solid fa-bars-progress"></i> <span id="department-name">Subjects</span>
                </div>
            </div>

            <div class="main-container-form">

                <div class="enroll-options">
                    <button id="enrollNewBtn" style="height: 35px; margin-left: 100px; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #4070f4; transition: opacity ease 0.3s;">
                        Add Subjects
                    </button>
                    <button id="editSubject" style="height: 35px; margin-left: 100px; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #4070f4; transition: opacity ease 0.3s;">
                        Edit Subjects
                    </button>
                </div>

                <script>
                    document.getElementById('enrollNewBtn').addEventListener('click', () => {
                        document.getElementById('enrollNewBtn').style.display = 'none';
                        document.getElementById('editSubject').style.display = 'none';
                        document.getElementById('enrollForm').style.display = 'block';
                    });

                    document.getElementById('editSubject').addEventListener('click', () => {
                        document.getElementById('editSubject').style.display = 'none';
                        document.getElementById('enrollNewBtn').style.display = 'none';
                        document.getElementById('editForm').style.display = 'block';
                    });
                </script>

                <form method="POST" action="" enctype="multipart/form-data" class="enrollForm" id="enrollForm" style="display: none;">
                    <div class="enroll-form first">
                        <div class="personal-details personal">
                            <span class="title">Subject Loading:</span>
                            <div class="enroll-fields">

                                <div class="enrol-input-fields">
                                    <label for="course">Course</label>
                                    <select id="course" name="course" style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>
                                        <option selected disabled>Enter course:</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSHM">BSHM</option>
                                        <option value="BSBA">BSBA</option>
                                        <option value="BSTM">BSTM</option>
                                        <option value="BTVTeD-AT">BTVTeD-AT</option>
                                        <option value="BTVTeD-HVACR TECH">BTVTeD-HVACR TECH</option>
                                        <option value="BTVTeD-FSM">BTVTeD-FSM</option>
                                        <option value="BTVTeD-ET">BTVTeD-ET</option>
                                    </select>

                                    <label for="year_level" style="margin-left: 20px;">Year Level</label>
                                    <select id="year_level" name="year_level" style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>
                                        <option selected disabled>Enter year:</option>
                                        <option value="1stYear">1st Year</option>
                                        <option value="2ndYear">2nd Year</option>
                                        <option value="3rdYear">3rd Year</option>
                                        <option value="4thYear">4th Year</option>
                                    </select>

                                    <label for="semester" style="margin-left: 20px;">Semester</label>
                                    <select id="semester" name="semester" style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>
                                        <option selected disabled>Enter sem:</option>
                                        <option value="1stSem">1st Semester</option>
                                        <option value="2ndSem">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label>Subject ID:</label>
                                    <input type="text" name="subject_id" style="height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>
                                </div>
                            </div>

                            <span class="title">Subject Loading Details</span>

                            <div class="enroll-fields">
                                <div class="enrol-input-fields">
                                    <table class="subjects">
                                        <thead>
                                            <tr>
                                                <th>Subject Code</th>
                                                <th>Description</th>
                                                <th>Lec</th>
                                                <th>Lab</th>
                                                <th>Units No</th>
                                                <th>Pre Req</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- New rows will be added here -->
                                        </tbody>
                                    </table>

                                    <style>
                                        .enrol-input-fields .subjects {
                                            border: 1px solid #ddd;
                                            padding: 5px;
                                            width: 100%;
                                        }
                                    </style>

                                    <button type="button" style="margin: 20px 0 20px 0;"
                                        onclick="addRow()">Add Another Row</button>
                                </div>
                            </div>

                            <script>
                                // Function to add a new row to the table
                                function addRow() {
                                    const table = document.querySelector("table.subjects tbody");
                                    const newRow = document.createElement('tr');
                                    newRow.innerHTML = `
                                                    <td><input type="text" style="width: 120px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][subject_code]" placeholder="Subject Code"></td>
                                                    <td><input type="text" style="width: 400px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][description]" placeholder="Description"></td>
                                                    <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lec]" placeholder="Lec"></td>
                                                    <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lab]" placeholder="Lab"></td>
                                                    <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][unit_no]" placeholder="Units"></td>
                                                    <td><input type="text" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][pre_req]" placeholder="Pre Req"></td>
                                                    <td>
                                                        <button 
                                                            type="button" 
                                                            style="margin: 0; padding: 10px; background: #ff4d4f; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                            onclick="removeRow(this)" 
                                                            title="Remove">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>


                                                `;
                                    table.appendChild(newRow);
                                }

                                // Function to remove a row
                                function removeRow(button) {
                                    const row = button.closest('tr');
                                    row.remove();
                                }

                                // Ensure the subjects are grouped properly into an array of objects when submitting
                                document.querySelector('form').onsubmit = function() {
                                    const subjects = [];
                                    const rows = document.querySelectorAll('table.subjects tbody tr');

                                    rows.forEach(row => {
                                        const subject = {
                                            subject_code: row.querySelector('input[name="subjects[][subject_code]"]').value,
                                            description: row.querySelector('input[name="subjects[][description]"]').value,
                                            lec: row.querySelector('input[name="subjects[][lec]"]').value,
                                            lab: row.querySelector('input[name="subjects[][lab]"]').value,
                                            unit_no: row.querySelector('input[name="subjects[][unit_no]"]').value,
                                            pre_req: row.querySelector('input[name="subjects[][pre_req]"]').value
                                        };
                                        subjects.push(subject);
                                    });

                                    // Add the subjects array to the form as a hidden input before submitting
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'subjects';
                                    hiddenInput.value = JSON.stringify(subjects); // Ensure it's correctly serialized
                                    document.querySelector('form').appendChild(hiddenInput);
                                }
                            </script>

                            <div class="enroll-fields">
                                <div class="confirmation">
                                    <label for="confirmation">
                                        We <strong>HEREBY CERTIFY</strong> that the above information is true and
                                        correct to the best of our knowledge <a href="#" id="openModal">Privacy
                                            Policy</a>.
                                    </label>
                                    <input title="confirmation" type="checkbox" id="confirmation">
                                </div>
                            </div>

                            <button type="submit">Submit</button>

                        </div>
                    </div>
                </form>

                <form method="POST" action="" enctype="multipart/form-data" class="enrollForm" id="editForm" style="display: none;">
                    <div class="enroll-form first">
                        <div class="personal-details personal">
                            <span class="title">Edit Subjects:</span>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label>Search Subject ID:</label>
                                    <input type="text" id="subject_id" name="subject_id" style="height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>

                                    <button type="button" id="find_subject" style="height: 35px; margin-left: 0; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #4070f4; transition: opacity ease 0.3s;">
                                        Find Subjects ID
                                    </button>
                                </div>
                            </div>

                            <span class="title">Subject Loading Result:</span>

                            <div class="enroll-fields" id="subject-loading-result">
                                <!-- Fetched subjects will be displayed here -->
                            </div>

                            <div class="enroll-fields" id="action-buttons" style="display: none;">
                                <button type="button" id="cancel" style="height: 35px; margin-left: 0; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #ff4d4f; transition: opacity ease 0.3s;">
                                    Cancel
                                </button>
                                <button type="button" id="delete_subject" style="height: 35px; margin-left: 0; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #ff4d4f; transition: opacity ease 0.3s;">
                                    Delete Subject
                                </button>
                                <button type="button" id="update_subject" style="height: 35px; margin-left: 0; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #4070f4; transition: opacity ease 0.3s;">
                                    Update Subject
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <script>
                    document.getElementById('find_subject').addEventListener('click', function() {
                        const subjectId = document.getElementById('subject_id').value;

                        if (!subjectId) {
                            alert('Please enter a Subject ID.');
                            return;
                        }

                        // Send AJAX request to fetch subjects
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'fetch_subjects.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                console.log(xhr.responseText); // Log the response for debugging
                                const response = JSON.parse(xhr.responseText);

                                if (response.success) {
                                    const subjects = response.data;
                                    console.log(subjects); // Log the fetched subjects for debugging
                                    const subjectLoadingResult = document.getElementById('subject-loading-result');
                                    const actionButtons = document.getElementById('action-buttons');

                                    // Clear previous results
                                    subjectLoadingResult.innerHTML = '';

                                    // Display fetched subjects in a table
                                    const table = document.createElement('table');
                                    table.className = 'subjects';
                                    table.innerHTML = `
                                        <thead>
                                            <tr>
                                                <th>Subject Code</th>
                                                <th>Description</th>
                                                <th>Lec</th>
                                                <th>Lab</th>
                                                <th>Units No</th>
                                                <th>Pre Req</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${subjects.map((subject, index) => `
                                                <tr data-index="${index}">
                                                    <td><input type="text" style="width: 120px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][subject_code]" value="${subject.subject_code || ''}"></td>
                                                    <td><input type="text" style="width: 400px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][description]" value="${subject.description || ''}"></td>
                                                    <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lec]" value="${subject.lec || ''}"></td>
                                                    <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lab]" value="${subject.lab || ''}"></td>
                                                    <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][unit_no]" value="${subject.unit_no || ''}"></td>
                                                    <td><input type="text" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][pre_req]" value="${subject.pre_req || ''}"></td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button 
                                                                type="button" 
                                                                style="margin: 0; padding: 10px; background: #ff4d4f; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                                onclick="removeRow(this, '${subject.subject_code}')" 
                                                                title="Remove">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                            <button 
                                                                type="button" 
                                                                style="margin: 5px 0; padding: 10px; background: #4CAF50; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                                onclick="addRowAbove(this)" 
                                                                title="Add Row Above">
                                                                <i class="fa fa-arrow-up"></i>
                                                            </button>
                                                            <button 
                                                                type="button" 
                                                                style="margin: 5px 0; padding: 10px; background: #2196F3; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                                onclick="addRowBelow(this)" 
                                                                title="Add Row Below">
                                                                <i class="fa fa-arrow-down"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    `;

                                    subjectLoadingResult.appendChild(table);
                                    actionButtons.style.display = 'block'; // Show action buttons
                                } else {
                                    alert(response.message || 'No subjects found.');
                                }
                            } else {
                                alert('Error fetching subjects. Please try again.');
                            }
                        };
                        xhr.send(`subject_id=${subjectId}`);
                    });

                    // Function to remove a row
                    function removeRow(button, subjectCode) {
                        if (confirm('Are you sure you want to delete this subject?')) {
                            const row = button.closest('tr'); // Get the row to be deleted
                            const subjectId = document.getElementById('subject_id').value;

                            // Send AJAX request to delete the subject
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'delete_subject_row.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    const response = JSON.parse(xhr.responseText);

                                    if (response.success) {
                                        row.remove(); // Remove the row from the table
                                        alert('Subject deleted successfully.');
                                    } else {
                                        alert(response.message || 'Error deleting subject.');
                                    }
                                } else {
                                    alert('Error deleting subject. Please try again.');
                                }
                            };
                            xhr.send(`subject_id=${subjectId}&subject_code=${subjectCode}`);
                        }
                    }

                    // Function to add a row above a specific row
                    function addRowAbove(button) {
                        const row = button.closest('tr');
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                                        <td><input type="text" style="width: 120px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][subject_code]" placeholder="Subject Code"></td>
                                        <td><input type="text" style="width: 400px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][description]" placeholder="Description"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lec]" placeholder="Lec"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lab]" placeholder="Lab"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][unit_no]" placeholder="Units"></td>
                                        <td><input type="text" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][pre_req]" placeholder="Pre Req"></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button 
                                                    type="button" 
                                                    style="margin: 0; padding: 10px; background: #ff4d4f; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="removeRow(this, '')" 
                                                    title="Remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    style="margin: 5px 0; padding: 10px; background: #4CAF50; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="addRowAbove(this)" 
                                                    title="Add Row Above">
                                                    <i class="fa fa-arrow-up"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    style="margin: 5px 0; padding: 10px; background: #2196F3; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="addRowBelow(this)" 
                                                    title="Add Row Below">
                                                    <i class="fa fa-arrow-down"></i>
                                                </button>
                                            </div>
                                        </td>
                                    `;
                        row.parentNode.insertBefore(newRow, row); // Insert the new row above the current row
                    }

                    // Function to add a row below a specific row
                    function addRowBelow(button) {
                        const row = button.closest('tr');
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                                        <td><input type="text" style="width: 120px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][subject_code]" placeholder="Subject Code"></td>
                                        <td><input type="text" style="width: 400px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][description]" placeholder="Description"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lec]" placeholder="Lec"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lab]" placeholder="Lab"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][unit_no]" placeholder="Units"></td>
                                        <td><input type="text" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][pre_req]" placeholder="Pre Req"></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button 
                                                    type="button" 
                                                    style="margin: 0; padding: 10px; background: #ff4d4f; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="removeRow(this, '')" 
                                                    title="Remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    style="margin: 5px 0; padding: 10px; background: #4CAF50; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="addRowAbove(this)" 
                                                    title="Add Row Above">
                                                    <i class="fa fa-arrow-up"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    style="margin: 5px 0; padding: 10px; background: #2196F3; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="addRowBelow(this)" 
                                                    title="Add Row Below">
                                                    <i class="fa fa-arrow-down"></i>
                                                </button>
                                            </div>
                                        </td>
                                    `;
                        row.parentNode.insertBefore(newRow, row.nextSibling); // Insert the new row below the current row
                    }

                    // Function to add a row at the end of the table
                    function addRowAtEnd() {
                        const tbody = document.querySelector('#subject-loading-result tbody');
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                                        <td><input type="text" style="width: 120px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][subject_code]" placeholder="Subject Code"></td>
                                        <td><input type="text" style="width: 400px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][description]" placeholder="Description"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lec]" placeholder="Lec"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][lab]" placeholder="Lab"></td>
                                        <td><input type="number" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][unit_no]" placeholder="Units"></td>
                                        <td><input type="text" style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" name="subjects[][pre_req]" placeholder="Pre Req"></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button 
                                                    type="button" 
                                                    style="margin: 0; padding: 10px; background: #ff4d4f; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="removeRow(this, '')" 
                                                    title="Remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    style="margin: 5px 0; padding: 10px; background: #4CAF50; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="addRowAbove(this)" 
                                                    title="Add Row Above">
                                                    <i class="fa fa-arrow-up"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    style="margin: 5px 0; padding: 10px; background: #2196F3; border: none; cursor: pointer; color: white; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 50px; height: 30px;" 
                                                    onclick="addRowBelow(this)" 
                                                    title="Add Row Below">
                                                    <i class="fa fa-arrow-down"></i>
                                                </button>
                                            </div>
                                        </td>
                                    `;
                        tbody.appendChild(newRow); // Append the new row at the end of the table
                    }

                    // Function to remove a row
                    function removeRow(button, subjectCode) {
                        if (confirm('Are you sure you want to delete this subject?')) {
                            const row = button.closest('tr'); // Get the row to be deleted
                            const subjectId = document.getElementById('subject_id').value;

                            // Send AJAX request to delete the subject
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'delete_subject_row.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    const response = JSON.parse(xhr.responseText);

                                    if (response.success) {
                                        row.remove(); // Remove the row from the table
                                        alert('Subject deleted successfully.');
                                    } else {
                                        alert(response.message || 'Error deleting subject.');
                                    }
                                } else {
                                    alert('Error deleting subject. Please try again.');
                                }
                            };
                            xhr.send(`subject_id=${subjectId}&subject_code=${subjectCode}`);
                        }
                    }

                    // Cancel button
                    document.getElementById('cancel').addEventListener('click', function() {
                        document.getElementById('subject-loading-result').innerHTML = ''; // Clear results
                        document.getElementById('action-buttons').style.display = 'none'; // Hide buttons
                    });

                    // Delete Subject button
                    document.getElementById('delete_subject').addEventListener('click', function() {
                        const subjectId = document.getElementById('subject_id').value;

                        if (!subjectId) {
                            alert('Please enter a Subject ID.');
                            return;
                        }

                        if (confirm('Are you sure you want to delete this subject?')) {
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'delete_subject.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    const response = JSON.parse(xhr.responseText);

                                    if (response.success) {
                                        alert('Subject deleted successfully.');
                                        document.getElementById('subject-loading-result').innerHTML = ''; // Clear results
                                        document.getElementById('action-buttons').style.display = 'none'; // Hide buttons
                                    } else {
                                        alert(response.message || 'Error deleting subject.');
                                    }
                                } else {
                                    alert('Error deleting subject. Please try again.');
                                }
                            };
                            xhr.send(`subject_id=${subjectId}`);
                        }
                    });

                    // Update Subject button
                    document.getElementById('update_subject').addEventListener('click', function() {
                        // Show confirmation dialog
                        if (confirm('Are you sure you want to update the subjects?')) {
                            const subjectId = document.getElementById('subject_id').value;
                            const subjects = [];

                            // Collect updated subject data
                            document.querySelectorAll('#subject-loading-result tbody tr').forEach(row => {
                                const subject = {
                                    subject_code: row.querySelector('input[name="subjects[][subject_code]"]').value,
                                    description: row.querySelector('input[name="subjects[][description]"]').value,
                                    lec: row.querySelector('input[name="subjects[][lec]"]').value,
                                    lab: row.querySelector('input[name="subjects[][lab]"]').value,
                                    unit_no: row.querySelector('input[name="subjects[][unit_no]"]').value,
                                    pre_req: row.querySelector('input[name="subjects[][pre_req]"]').value,
                                };
                                subjects.push(subject);
                            });

                            // Send AJAX request to update subjects
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'update_subjects.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    try {
                                        const response = JSON.parse(xhr.responseText);

                                        if (response.success) {
                                            alert(response.message || 'Subjects updated successfully.');

                                            // Refresh the page to return to the default state
                                            window.location.href = 'add-subjects.php';
                                        } else {
                                            alert(response.message || 'Error updating subjects.');
                                        }
                                    } catch (e) {
                                        console.error('Invalid JSON response:', xhr.responseText);
                                        alert('An error occurred. Please check the console for details.');
                                    }
                                } else {
                                    alert('Error updating subjects. Please try again.');
                                }
                            };
                            xhr.onerror = function() {
                                alert('An error occurred. Please check your network connection.');
                            };
                            xhr.send(`subject_id=${subjectId}&subjects=${JSON.stringify(subjects)}`);
                        } else {
                            // User clicked "Cancel" in the confirmation dialog
                            alert('Update canceled.');
                        }
                    });
                </script>

            </div>
        </div>
    </div>
    <!-- MAIN CONTAINER -->



    <!-- MODAL FOR PRIVACY POLICY -->
    <div id="privacyModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <div class="privacy-policy-container">
                <h1>Privacy Policy</h1>
                <p>
                    <strong>Effective Date:</strong> [Insert Date]
                </p>
                <p>
                    At <strong>Automating School Operations: A Web-Based Management System</strong>, we value your
                    privacy and are committed to protecting your personal information. This Privacy Policy outlines how
                    we collect, use, and safeguard the data you provide while using our platform.
                </p>
                <h2>1. Information We Collect</h2>
                <ul>
                    <li>
                        <strong>Personal Data:</strong> Full name, email address, student ID, contact information, and
                        other details submitted during enrollment or registration.
                    </li>
                    <li>
                        <strong>System Usage Data:</strong> IP address, browser type, operating system, and usage
                        statistics to improve our services.
                    </li>
                </ul>
                <h2>2. How We Use Your Information</h2>
                <ul>
                    <li>To facilitate and manage school operations such as enrollment, scheduling, and records
                        management.</li>
                    <li>To communicate important updates and notifications.</li>
                    <li>To analyze and improve the platform's functionality and user experience.</li>
                </ul>
                <h2>3. Data Protection</h2>
                <p>
                    We implement appropriate security measures to protect your data from unauthorized access,
                    alteration, or disclosure. However, no system is entirely secure, and we cannot guarantee absolute
                    data security.
                </p>
                <h2>4. Third-Party Sharing</h2>
                <p>
                    We do not sell, trade, or rent your personal data to third parties. Information may be shared with
                    trusted service providers for operational purposes, in compliance with applicable laws.
                </p>
                <h2>5. Your Rights</h2>
                <p>
                    You have the right to access, update, or delete your personal data. Please contact the school
                    administration for any privacy-related inquiries or requests.
                </p>
                <h2>6. Updates to This Policy</h2>
                <p>
                    We reserve the right to update this Privacy Policy as necessary. Changes will be communicated
                    through the platform or school announcements.
                </p>
                <p>
                    By using our platform, you agree to the terms of this Privacy Policy.
                </p>
                <h2>Contact Us</h2>
                <p>
                    If you have questions about this Privacy Policy, please contact us at <strong>[Insert Contact
                        Email]</strong>.
                </p>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        a#openModal {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }

        a#openModal:hover {
            text-decoration: underline;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s;
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover {
            color: #000;
        }

        .privacy-policy-container h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2rem;
        }

        .privacy-policy-container h2 {
            margin-top: 20px;
            color: #34495e;
            font-size: 1.5rem;
        }

        .privacy-policy-container p,
        .privacy-policy-container ul {
            margin: 10px 0;
            font-size: 1rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    <style>
        /* Style for the action buttons container */
        .action-buttons {
            display: flex;
            /* Align buttons horizontally */
            gap: 5px;
            /* Add space between buttons */
            align-items: center;
            /* Vertically center buttons */
        }

        /* Style for individual buttons */
        .action-buttons button {
            margin: 0;
            /* Remove default margins */
            /* Adjust padding for better spacing */
            display: flex;
            /* Center icons inside buttons */
            align-items: center;
            justify-content: center;
        }
    </style>

    <script>
        const modal = document.getElementById('privacyModal');
        const openModalLink = document.getElementById('openModal');
        const closeModalBtn = document.getElementById('closeModal');

        openModalLink.addEventListener('click', (e) => {
            e.preventDefault();
            modal.style.display = 'block';
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
    <!-- MODAL FOR PRIVACY POLICY -->

    <script>
        // ACTIVE MENU
        document.querySelectorAll('.sidebar-menu ul li a').forEach(menuItem => {
            menuItem.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-menu ul li a').forEach(item => item.classList.remove('active'));

                this.classList.add('active');

                document.getElementById('dashboard-title').textContent = this.getAttribute('data-title');
            });
        });

        // HIDE SIDEBAR MENU
        document.getElementById('toggle-menu-btn').addEventListener('click', function() {
            const sidebarMenu = document.querySelector('.sidebar-menu');
            const dashboardContainer = document.querySelector('.dashboard-container');
            const headerContainer = document.querySelector('.header-container');
            const mainContainer = document.querySelector('.main-container');

            sidebarMenu.classList.toggle('collapsed');

            dashboardContainer.classList.toggle('collapsed');
            headerContainer.classList.toggle('collapsed');
            mainContainer.classList.toggle('collapsed');

            const toggleIcon = this.querySelector('i');
            if (sidebarMenu.classList.contains('collapsed')) {
                toggleIcon.classList.replace('fa-chevron-left', 'fa-chevron-right');
            } else {
                toggleIcon.classList.replace('fa-chevron-right', 'fa-chevron-left');
            }
        });

        const profileLink = document.getElementById("profile-link");
        const profileMenu = document.getElementById("profile-menu");

        profileLink.addEventListener("click", function(e) {
            e.preventDefault();
            profileMenu.style.display = profileMenu.style.display === "block" ? "none" : "block";
        });

        window.addEventListener("click", function(e) {
            if (!profileLink.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.style.display = "none";
            }
        });

        // SIDE BAR JS
        document.querySelectorAll(".sidebar-dropdown-btn").forEach(item => {
            item.addEventListener("click", function() {
                const dropdown = this.parentElement;
                dropdown.classList.toggle("active");

                const icon = this.querySelector(".dropdown-icon");
                if (dropdown.classList.contains("active")) {
                    icon.classList.remove("fa-caret-right");
                    icon.classList.add("fa-caret-down");
                } else {
                    icon.classList.remove("fa-caret-down");
                    icon.classList.add("fa-caret-right");
                }
            });
        });
        // SIDE BAR JS

        // SELECTED MENU
        const dashboardTitle = document.getElementById("dashboard-title");

        function updateTitle(event) {
            const newTitle = event.target.getAttribute("data-title");
            if (newTitle) {
                dashboardTitle.textContent = newTitle;
            }
        }

        document.querySelectorAll(".sidebar-menu a").forEach(item => {
            item.addEventListener("click", updateTitle);
        });
        // SELECTED MENU
    </script>

</body>

</html>