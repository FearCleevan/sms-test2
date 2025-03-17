<?php
session_start();
include("connect.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_id = $_POST['student_id'];
    $department = $_POST['department'];
    $lrn = ($department === 'JHS' || $department === 'SHS') ? $_POST['lrn'] : '';
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $province = $_POST['province'];
    $zip_code = $_POST['zip_code'];
    $city = $_POST['city'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_phone = $_POST['emergency_phone'];
    $relation = $_POST['relation'];
    $enroll_date = $_POST['enroll_date'];
    $enroll_time = $_POST['enroll_time'];

    // Handle file upload
    $profilePath = null;
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $imageTmpName = $_FILES['profile']['tmp_name'];
        $imageType = $_FILES['profile']['type'];
        $imageSize = $_FILES['profile']['size']; // Get the file size

        // Check file size (10MB = 10 * 1024 * 1024 bytes)
        if ($imageSize > 10 * 1024 * 1024) {
            die("File size exceeds the maximum limit of 10MB.");
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imageType, $allowedTypes)) {
            die("Invalid image type. Please upload a JPG, PNG, or GIF image.");
        }

        // Generate a unique file name to avoid conflicts
        $fileName = uniqid() . '_' . basename($_FILES['profile']['name']);
        $uploadDir = 'uploads/'; // Folder to store uploaded images
        $profilePath = $uploadDir . $fileName;

        // Move the uploaded file to the uploads folder
        if (!move_uploaded_file($imageTmpName, $profilePath)) {
            die("Failed to upload image.");
        }
    }

    // Insert data into the database
    $sql = "INSERT INTO enrollments (student_id, department, lrn, profile, first_name, middle_name, last_name, email, phone, username, password, address, province, zip_code, city, emergency_name, emergency_phone, relation, enroll_date, enroll_time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "ssssssssssssssssssss",
            $student_id,
            $department,
            $lrn,
            $profilePath, // Store the file path instead of the image data
            $first_name,
            $middle_name,
            $last_name,
            $email,
            $phone,
            $username,
            $password,
            $address,
            $province,
            $zip_code,
            $city,
            $emergency_name,
            $emergency_phone,
            $relation,
            $enroll_date,
            $enroll_time
        );

        if ($stmt->execute()) {
            echo "<script>
                    alert('Student successfully added.');
                    window.location.href = 'sample-details.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: " . $stmt->error . "');
                    window.location.href = 'sample-details.php';
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                alert('Error preparing the statement: " . $conn->error . "');
                window.location.href = 'sample-details.php';
              </script>";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Student</title>
    <link rel="stylesheet" href="sample-details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./image/apple-touch-icon.png">
</head>

<body>
    <!-- SIDEBAR MENU -->
    <div class="sidebar-menu">
        <h2 id="dashboard-title">Enroll a Student</h2>
        <ul class="nav-list">
            <li><a href="./dashboard.php" data-title="Dashboard"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a></li>
            <li><a href="./sample-details.php" data-title="Enroll a Student" class="active"><i class="fa-solid fa-user-plus"></i> <span>Enroll a Student</span></a></li>
            <li><a href="./department.php" data-title="Department"><i class="fa-solid fa-building"></i> <span>Department</span></a></li>
            <li><a href="#course" data-title="Course"><i class="fa-solid fa-book"></i> <span>Course</span></a></li>
            <li><a href="./add-subjects.php" data-title="Subjects"><i class="fa-solid fa-book-open"></i> <span>Subjects</span></a></li>
            <li><a href="#payment-management" data-title="Payment Management"><i class="fa-solid fa-credit-card"></i> <span>Payment Management</span></a></li>
            <li><a href="#grading-system" data-title="Grading System"><i class="fa-solid fa-graduation-cap"></i> <span>Grading System</span></a></li>
            <li><a href="#student-attendance" data-title="Student Attendance"><i class="fa-solid fa-calendar-check"></i> <span>Student Attendance</span></a></li>
            <li><a href="./announcement.php" data-title="Announcement"><i class="fa-solid fa-bullhorn"></i> <span>Announcement</span></a></li>
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
                    <i class="fa-solid fa-bars-progress"></i> <span> Student's Information Sheet</span>
                </div>
            </div>
            <div class="main-container-form">

                <div class="enroll-options">
                    <button id="enrollNewBtn" style="height: 35px; margin-left: 100px; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #2c3e50; transition: opacity ease 0.3s;">
                        Enroll New Student
                    </button>
                    <button id="enrollExistingBtn" style="height: 35px; margin-left: 100px; max-width: 300px; width: 100%; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #2c3e50; transition: opacity ease 0.3s;">
                        Enroll Existing Student
                    </button>
                </div>

                <div id="existingStudentPopup" style="display: none; margin-top: 20px;">
                    <label for="studentID">Enter Student ID:</label>
                    <input type="text" style="width: 200px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" id="studentID" name="studentID" placeholder="Enter Student ID" required>
                    <button id="submitExistingID" class="submit-btn" style="height: 31px; width: 100px; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #2c3e50; transition: opacity ease 0.3s;">
                        Submit
                    </button>
                </div>

                <div id="studentDetails" class="student-details" style="display: none;">
                    <h3>Student Details</h3>
                    <div class="left-section">
                        <div class="profile-container">
                            <img id="studentProfileImage" src="./uploads/default-profile.jpg" alt="Student Profile" class="profile-images">
                        </div>

                        <div class="dropdowns">
                            <label for="course">Course</label>
                            <select id="course" name="course">
                                <option selected disabled>Enter course:</option>
                                <!-- Courses will be dynamically populated based on department -->
                            </select>

                            <label for="year_level">Year Level</label>
                            <select id="year_level" name="year_level">
                                <option selected disabled>Enter year:</option>
                                <option value="1stYear">1st Year</option>
                                <option value="2ndYear">2nd Year</option>
                                <option value="3rdYear">3rd Year</option>
                                <option value="4thYear">4th Year</option>
                            </select>


                            <label for="semester">Semester</label>
                            <select id="semester" name="semester">
                                <option selected disabled>Enter sem:</option>
                                <option value="1stSem">1st Semester</option>
                                <option value="2ndSem">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>

                            <!-- School Year Input -->
                            <label for="school_year_start">School Year</label>
                            <div class="school-year-input">
                                <input type="text" id="school_year_start" name="school_year_start" placeholder="20XX" maxlength="4" style="width: 60px; text-align: center;">
                                <span> - </span>
                                <input type="text" id="school_year_end" name="school_year_end" placeholder="20XX" maxlength="4" style="width: 60px; text-align: center;">
                            </div>
                        </div>
                        <button id="updateStudentBtn" class="submit-button">Submit</button>
                    </div>

                    <div class="right-section">
                        <div class="student_details">
                            <p><strong>ID:</strong> <span id="studentIDDetails"></span></p>
                            <p><strong>Department:</strong> <span id="studentDepartmentDetails"></span></p>
                            <p><strong>Course:</strong> <span id="studentCourseDetails" style="display: none;"></span></p>
                            <p><strong>Year Level:</strong> <span id="studentYearDetails" style="display: none;"></span></p>
                            <p><strong>Semester:</strong> <span id="studentSemesterDetails" style="display: none;"></span></p>
                            <p><strong>School Year:</strong> <span id="studentSchoolYearDetails" style="display: none;"></span></p>
                            <p><strong>Full Name:</strong> <span id="studentNameDetails"></span></p>
                            <p><strong>Email:</strong> <span id="studentEmailDetails"></span></p>
                            <p><strong>Phone:</strong> <span id="studentPhoneDetails"></span></p>
                            <p><strong>Address:</strong> <span id="studentAddressDetails"></span></p>
                            <p><strong>Province:</strong> <span id="studentProvinceDetails"></span></p>
                        </div>
                        <div class="action-btns">
                            <button id="loadSubjectsBtn">Load Subjects</button>
                            <button id="enrollStudentBtn" disabled>Enroll Student</button>
                        </div>
                        <!-- Container to display subjects -->
                        <div id="subjectsContainer" style="margin-top: 20px;"></div>
                    </div>
                </div>

                <div id="subject-loading-result"></div>

                <!-- Enrollment Confirmation Modal -->
                <div id="enrollmentModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3>Student Enrolled Successfully!</h3>
                        <p>The student has been successfully enrolled.</p>
                        <button id="printDetailsBtn">Print Details</button>
                    </div>
                </div>

                <!-- Modal Styles -->
                <style>
                    /* Modal Styles */
                    .modal {
                        display: none;
                        position: fixed;
                        z-index: 1000;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        background-color: rgba(0, 0, 0, 0.5);
                    }

                    .modal-content {
                        background-color: #fff;
                        margin: 15% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                        max-width: 500px;
                        text-align: center;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    }

                    .close {
                        color: #aaa;
                        float: right;
                        font-size: 28px;
                        font-weight: bold;
                        cursor: pointer;
                    }

                    .close:hover {
                        color: #000;
                    }

                    #printDetailsBtn {
                        background-color: #34495e;
                        color: #fff;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        cursor: pointer;
                        margin-top: 20px;
                    }

                    #printDetailsBtn:hover {
                        background-color: #2c3e50;
                    }
                </style>

                <style>
                    /* Container for the student details */
                    .student-details {
                        display: flex;
                        gap: 20px;
                        margin-top: 20px;
                        padding: 20px;
                        background-color: #f9f9f9;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }

                    /* Left section styling */
                    .left-section {
                        width: 30%;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 20px;
                    }

                    /* Profile image styling */
                    .profile-container img {
                        width: 150px;
                        height: 150px;
                        border-radius: 50%;
                        object-fit: cover;
                        border: 2px solid #2c3e50;
                    }

                    /* Dropdowns styling */
                    .dropdowns {
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        flex-direction: column;
                        gap: 10px;
                    }

                    .dropdowns label {
                        font-weight: 500;
                        color: #333;
                    }

                    .dropdowns select {
                        width: 70%;
                        padding: 10px;
                        border-radius: 5px;
                        border: 1px solid #ccc;
                        font-size: 12px;
                        outline: none;
                        background-color: #fff;
                        transition: border-color 0.3s ease;
                    }

                    .dropdowns select:focus {
                        border-color: #2c3e50;
                    }

                    /* School Year Input Styling */
                    .school-year-input {
                        display: flex;
                        align-items: center;
                        gap: 5px;
                    }

                    .school-year-input input {
                        padding: 10px;
                        border-radius: 5px;
                        border: 1px solid #ccc;
                        font-size: 12px;
                        outline: none;
                        transition: border-color 0.3s ease;
                    }

                    .school-year-input input:focus {
                        border-color: #2c3e50;
                    }

                    .school-year-input span {
                        font-size: 14px;
                        color: #333;
                    }

                    /* Submit button styling */
                    .submit-button {
                        width: 70%;
                        padding: 10px;
                        background-color: #2c3e50;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        font-size: 14px;
                        cursor: pointer;
                        transition: background-color 0.3s ease;
                    }

                    .submit-button:hover {
                        background-color: #1a252f;
                    }

                    /* Right section styling */
                    .right-section {
                        width: 70%;
                        display: flex;
                        flex-direction: column;
                        gap: 20px;
                    }

                    /* Student details styling */
                    .student_details {
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }

                    .student_details p {
                        margin: 10px 0;
                        font-size: 14px;
                        color: #333;
                    }

                    .student_details p strong {
                        color: #2c3e50;
                    }

                    /* Action buttons styling */
                    .action-btns {
                        display: flex;
                        gap: 10px;
                        margin-top: 20px;
                    }

                    .action-btns button {
                        padding: 10px 20px;
                        background-color: #2c3e50;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        font-size: 14px;
                        cursor: pointer;
                        transition: background-color 0.3s ease;
                    }

                    .action-btns button:hover {
                        background-color: #1a252f;
                    }
                </style>

                <script>
                    document.getElementById('loadSubjectsBtn').addEventListener('click', function() {
                        // Get the student's course, year, and semester
                        const course = document.getElementById('studentCourseDetails').innerText;
                        const year = document.getElementById('studentYearDetails').innerText;
                        const semester = document.getElementById('studentSemesterDetails').innerText;

                        // Check if course, year, and semester are available
                        if (!course || !year || !semester) {
                            alert("Student's course, year, or semester is missing.");
                            return;
                        }

                        // Fetch subjects from the server using the course, year, and semester
                        fetch('fetch_subjects.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `course=${course}&year=${year}&semester=${semester}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log("Fetched data:", data); // Debugging
                                const subjectsContainer = document.getElementById('subjectsContainer');
                                subjectsContainer.innerHTML = ''; // Clear previous content

                                if (data.success && data.data) {
                                    // Create a table to display the subjects
                                    const table = document.createElement('table');
                                    table.className = 'styled-table'; // Add a class for styling
                                    table.innerHTML = `
                                                        <thead>
                                                            <tr>
                                                                <th>Subject Code</th>
                                                                <th>Description</th>
                                                                <th>Lec</th>
                                                                <th>Lab</th>
                                                                <th>Units</th>
                                                                <th>Prerequisites</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ${data.data.map(subject => `
                                                                <tr>
                                                                    <td>${subject.subject_code}</td>
                                                                    <td>${subject.description}</td>
                                                                    <td>${subject.lec}</td>
                                                                    <td>${subject.lab}</td>
                                                                    <td>${subject.unit_no}</td>
                                                                    <td>${subject.pre_req}</td>
                                                                </tr>
                                                            `).join('')}
                                                        </tbody>
                                                    `;
                                    subjectsContainer.appendChild(table);
                                } else {
                                    subjectsContainer.innerHTML = '<p>No subjects found for this course, year, and semester.</p>';
                                }
                            })
                            .catch(error => {
                                console.error("Error fetching subjects:", error);
                                alert("An error occurred while fetching subjects.");
                            });
                    });
                </script>

                <style>
                    /* General table styling */
                    .styled-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 25px 0;
                        font-size: 12px;
                        font-family: 'Poppins', sans-serif;
                        min-width: 400px;
                        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
                    }

                    /* Table header styling */
                    .styled-table thead tr {
                        background-color: #34495e;
                        /* Green header */
                        color: #ffffff;
                        text-align: left;
                    }

                    /* Table header and cell padding */
                    .styled-table th,
                    .styled-table td {
                        padding: 12px 15px;
                    }

                    /* Table row border */
                    .styled-table tbody tr {
                        border-bottom: 1px solid #dddddd;
                    }

                    /* Alternate row background color */
                    .styled-table tbody tr:nth-of-type(even) {
                        background-color: #f3f3f3;
                    }

                    /* Hover effect on rows */
                    .styled-table tbody tr:hover {
                        background-color: #f1f1f1;
                        cursor: pointer;
                    }

                    /* Bottom border for the last row */
                    .styled-table tbody tr:last-of-type {
                        border-bottom: 2px solid #34495e;
                    }

                    /* Add some spacing and alignment */
                    .styled-table th,
                    .styled-table td {
                        text-align: center;
                    }

                    /* Add rounded corners to the table */
                    .styled-table {
                        border-radius: 8px;
                        overflow: hidden;
                    }

                    /* Add a subtle animation on hover */
                    .styled-table tbody tr {
                        transition: background-color 0.3s ease;
                    }
                </style>

                <script>
                    // Handle "Enroll New Student" button click
                    document.getElementById('enrollNewBtn').addEventListener('click', () => {
                        document.getElementById('enrollNewBtn').style.display = 'none';
                        document.getElementById('enrollExistingBtn').style.display = 'none';
                        document.getElementById('enrollForm').style.display = 'block';
                    });

                    // Handle "Enroll Existing Student" button click
                    document.getElementById('enrollExistingBtn').addEventListener('click', () => {
                        document.getElementById('enrollNewBtn').style.display = 'none';
                        document.getElementById('enrollExistingBtn').style.display = 'none';
                        document.getElementById('existingStudentPopup').style.display = 'block';
                    });

                    // Handle "Submit" button click for existing student
                    document.getElementById('submitExistingID').addEventListener('click', async () => {
                        const studentID = document.getElementById('studentID').value;

                        try {
                            const response = await fetch(`get_student_details.php?studentID=${studentID}`);
                            const data = await response.json();

                            if (data.success) {
                                // Display student details
                                document.getElementById('studentProfileImage').src = data.profileImg;
                                document.getElementById('studentIDDetails').textContent = data.studentID;
                                document.getElementById('studentDepartmentDetails').textContent = data.department;
                                document.getElementById('studentNameDetails').textContent = data.name;
                                document.getElementById('studentEmailDetails').textContent = data.email;
                                document.getElementById('studentPhoneDetails').textContent = data.phone;
                                document.getElementById('studentAddressDetails').textContent = data.address;
                                document.getElementById('studentProvinceDetails').textContent = data.province;

                                // Set course, year, and semester dropdowns
                                document.getElementById('course').value = data.course;
                                document.getElementById('year_level').value = data.year;
                                document.getElementById('semester').value = data.semester;

                                // Set school year
                                if (data.school_year) {
                                    const [startYear, endYear] = data.school_year.split(" - ");
                                    document.getElementById('school_year_start').value = startYear;
                                    document.getElementById('school_year_end').value = endYear;
                                } else {
                                    document.getElementById('school_year_start').value = '';
                                    document.getElementById('school_year_end').value = '';
                                }

                                // Populate courses based on department and set the selected course
                                populateCourses(data.department, data.course);

                                // Display existing data in the right section
                                document.getElementById('studentCourseDetails').textContent = data.course;
                                document.getElementById('studentYearDetails').textContent = data.year;
                                document.getElementById('studentSemesterDetails').textContent = data.semester;
                                document.getElementById('studentSchoolYearDetails').textContent = data.school_year;

                                // Make the elements visible
                                document.getElementById('studentCourseDetails').style.display = 'inline';
                                document.getElementById('studentYearDetails').style.display = 'inline';
                                document.getElementById('studentSemesterDetails').style.display = 'inline';
                                document.getElementById('studentSchoolYearDetails').style.display = 'inline';

                                // Show the student details section
                                document.getElementById('studentDetails').style.display = 'flex';
                            } else {
                                alert(data.message || "Student ID not found.");
                            }
                        } catch (error) {
                            console.error("Error fetching student details:", error);
                            alert("An error occurred while fetching student details.");
                        }
                    });

                    // Function to populate courses based on department and set the selected course
                    function populateCourses(department, selectedCourse) {
                        const courseDropdown = document.getElementById('course');
                        courseDropdown.innerHTML = '<option selected disabled>Enter course:</option>';

                        const collegeCourses = [{
                                value: "BSIT",
                                text: "BSIT"
                            },
                            {
                                value: "BSHM",
                                text: "BSHM"
                            },
                            {
                                value: "BSBA",
                                text: "BSBA"
                            },
                            {
                                value: "BSTM",
                                text: "BSTM"
                            }
                        ];

                        const tvetCourses = [{
                                value: "BTVTeD-AT",
                                text: "BTVTeD-AT"
                            },
                            {
                                value: "BTVTeD-HVACR TECH",
                                text: "BTVTeD-HVACR TECH"
                            },
                            {
                                value: "BTVTeD-FSM",
                                text: "BTVTeD-FSM"
                            },
                            {
                                value: "BTVTeD-ET",
                                text: "BTVTeD-ET"
                            }
                        ];

                        const courses = department === "College" ? collegeCourses : tvetCourses;

                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.value;
                            option.textContent = course.text;

                            // Set the selected option if it matches the student's existing course
                            if (course.value === selectedCourse) {
                                option.selected = true;
                            }

                            courseDropdown.appendChild(option);
                        });
                    }

                    // Handle "Update Student" button click
                    document.getElementById('updateStudentBtn').addEventListener('click', async () => {
                        const studentID = document.getElementById('studentID').value;
                        const course = document.getElementById('course').value;
                        const yearLevel = document.getElementById('year_level').value;
                        const semester = document.getElementById('semester').value;
                        const schoolYearStart = document.getElementById('school_year_start').value;
                        const schoolYearEnd = document.getElementById('school_year_end').value;

                        if (!course || !yearLevel || !semester || !schoolYearStart || !schoolYearEnd) {
                            alert("Please fill out all fields.");
                            return;
                        }

                        const schoolYear = `${schoolYearStart} - ${schoolYearEnd}`;

                        try {
                            const response = await fetch('update_student.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    studentID,
                                    course,
                                    yearLevel,
                                    semester,
                                    schoolYear
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                alert("Student details updated successfully.");

                                // Refresh displayed details
                                document.getElementById('studentCourseDetails').textContent = course;
                                document.getElementById('studentYearDetails').textContent = yearLevel;
                                document.getElementById('studentSemesterDetails').textContent = semester;
                                document.getElementById('studentSchoolYearDetails').textContent = schoolYear;
                            } else {
                                alert(data.message || "Failed to update student details.");
                            }
                        } catch (error) {
                            console.error("Error updating student details:", error);
                            alert("An error occurred while updating student details.");
                        }
                    });
                </script>

                <script>
                    document.getElementById('enrollStudentBtn').addEventListener('click', async () => {
                        const studentID = document.getElementById('studentID').value;
                        const course = document.getElementById('course').value;
                        const yearLevel = document.getElementById('year_level').value;
                        const semester = document.getElementById('semester').value;

                        // Fetch the subject_id from the student_subject table
                        try {
                            const response = await fetch('get_subject_id.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    course,
                                    yearLevel,
                                    semester
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                const subject_id = data.subject_id;

                                // Update the enrollments table with subject_id and status
                                const updateResponse = await fetch('update_enrollments.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        studentID,
                                        subject_id,
                                        status: 'Enrolled'
                                    })
                                });

                                const updateData = await updateResponse.json();

                                if (updateData.success) {
                                    // Show the enrollment confirmation modal
                                    const modal = document.getElementById('enrollmentModal');
                                    modal.style.display = 'block';

                                    // Close the modal when the close button is clicked
                                    const closeBtn = document.querySelector('.close');
                                    closeBtn.addEventListener('click', () => {
                                        modal.style.display = 'none';
                                    });

                                    // Close the modal when clicking outside of it
                                    window.addEventListener('click', (event) => {
                                        if (event.target === modal) {
                                            modal.style.display = 'none';
                                        }
                                    });
                                } else {
                                    alert(updateData.message || "Failed to enroll student.");
                                }
                            } else {
                                alert(data.message || "Failed to fetch subject ID.");
                            }
                        } catch (error) {
                            console.error("Error enrolling student:", error);
                            alert("An error occurred while enrolling the student.");
                        }
                    });
                </script>

                <script>
                    document.getElementById('printDetailsBtn').addEventListener('click', () => {
                        console.log("Print button clicked"); // Debugging

                        // Get the student details
                        const studentID = document.getElementById('studentIDDetails').textContent;
                        const fullName = document.getElementById('studentNameDetails').textContent;
                        const course = document.getElementById('studentCourseDetails').textContent;
                        const year = document.getElementById('studentYearDetails').textContent;
                        const semester = document.getElementById('studentSemesterDetails').textContent;
                        const status = "Enrolled"; // Default status

                        // Get the subjects table
                        const subjectsTable = document.getElementById('subjectsContainer').innerHTML;

                        // Create a new window for printing
                        const printWindow = window.open('', '', 'width=800,height=600');
                        if (!printWindow) {
                            console.error("Failed to open print window"); // Debugging
                            alert("Failed to open print window. Please allow pop-ups for this site.");
                            return;
                        }

                        // Write the printable content to the new window
                        printWindow.document.write(`
                                <html>
                                    <head>
                                        <title>Student Enrollment Details</title>
                                        <style>
                                            body {
                                                font-family: Arial, sans-serif;
                                                margin: 20px;
                                                font-size: 12px;
                                            }
                                            header {
                                                display: flex;
                                                justify-content: space-between;
                                                align-items: center;
                                                padding: 10px 20px;
                                                border-bottom: 2px solid #000;
                                            }
                                            header img {
                                                height: 60px;
                                            }
                                            header .school-info {
                                                text-align: right;
                                            }
                                            header .school-info h1 {
                                                margin: 0;
                                                font-size: 16px;
                                            }
                                            header .school-info p {
                                                margin: 0;
                                                font-size: 12px;
                                            }
                                            .form-container {
                                                padding: 20px;
                                            }
                                            .form-container h2 {
                                                text-align: center;
                                                margin-bottom: 20px;
                                            }
                                            .form-container .form-detail {
                                                margin-bottom: 20px;
                                            }
                                            .form-container .form-detail p {
                                                margin: 5px 0;
                                            }
                                            table {
                                                width: 100%;
                                                border-collapse: collapse;
                                                margin-bottom: 20px;
                                            }
                                            table th, table td {
                                                border: 1px solid #000;
                                                font-size: 12px;
                                                padding: 6px;
                                                text-align: left;
                                            }
                                            table th {
                                                background-color:#f2f2f2;
                                                color: #000;
                                            }
                                            table tr:nth-child(even) {
                                                background-color: #f2f2f2;
                                            }
                                            footer {
                                                display: flex;
                                                justify-content: space-between;
                                                padding: 10px 20px;
                                                border-top: 2px solid #000;
                                                margin-top: 20px;
                                            }
                                            footer .signature-box {
                                                width: 23%;
                                                text-align: center;
                                            }
                                            footer .signature-box p {
                                                margin: 5px 0;
                                            }
                                            .duplicated {
                                                border-top: 2px dashed #000;
                                                margin-top: 20px;
                                                padding-top: 20px;
                                            }
                                            .print-button-container {
                                                text-align: center;
                                                margin: 20px 0;
                                            }
                                            .print-button-container button {
                                                padding: 10px 20px;
                                                font-size: 14px;
                                                background-color: #34495e;
                                                color: #fff;
                                                border: none;
                                                border-radius: 5px;
                                                cursor: pointer;
                                            }
                                            .print-button-container button:hover {
                                                background-color: #2c3e50;
                                            }
                                            @media print {
                                                .print-button-container {
                                                    display: none;
                                                }
                                            }
                                        </style>
                                    </head>
                                    <body>
                                        <!-- First Copy (Student's Copy) -->
                                        <header>
                                            <img src="./uploads/Samson-Logo.png" alt="School Logo">
                                            <div class="school-info">
                                                <h1>Samson Polytechnic College of Davao</h1>
                                                <p>Magsaysay Avenue corner Chavez Street, Davao City</p>
                                            </div>
                                        </header>
                                        <div class="form-container">
                                            <h2>Enrollment Form</h2>
                                            <div class="form-detail">
                                                <p>Date of Submission: <span id="submission-date">${new Date().toLocaleDateString()}</span></p>
                                                <p>Student ID: ${studentID}</p>
                                                <p>Full Name: ${fullName}</p>
                                                <p>Course: ${course}</p>
                                                <p>Year: ${year}</p>
                                                <p>Semester: ${semester}</p>
                                                <p>Status: ${status}</p>
                                            </div>
                                            <table>
                                                <tbody>
                                                    ${subjectsTable}
                                                </tbody>
                                            </table>
                                        </div>
                                        <footer>
                                            <div class="signature-box">
                                                <p>Confirmed By:</p>
                                                <p>____________________</p>
                                                <p>Date: _______________</p>
                                            </div>
                                            <div class="signature-box">
                                                <p>Approved By:</p>
                                                <p>____________________</p>
                                                <p>Date: _______________</p>
                                            </div>
                                            <div class="signature-box">
                                                <p>Assessed By:</p>
                                                <p>____________________</p>
                                                <p>Date: _______________</p>
                                            </div>
                                            <div class="signature-box">
                                                <p>Copy Received By:</p>
                                                <p>____________________</p>
                                                <p>Date: _______________</p>
                                            </div>
                                        </footer>

                                        <!-- Second Copy (Registrar's Copy) -->
                                        <div class="duplicated">
                                            <header>
                                                <img src="./uploads/Samson-Logo.png" alt="School Logo">
                                                <div class="school-info">
                                                    <h1>Samson Polytechnic College of Davao</h1>
                                                    <p>Magsaysay Avenue corner Chavez Street, Davao City</p>
                                                </div>
                                            </header>
                                            <div class="form-container">
                                                <h2>Enrollment Form</h2>
                                                <div class="form-detail">
                                                    <p>Date of Submission: <span id="submission-date-duplicate">${new Date().toLocaleDateString()}</span></p>
                                                    <p>Student ID: ${studentID}</p>
                                                    <p>Full Name: ${fullName}</p>
                                                    <p>Course: ${course}</p>
                                                    <p>Year: ${year}</p>
                                                    <p>Semester: ${semester}</p>
                                                    <p>Status: ${status}</p>
                                                </div>
                                                <table>
                                                    <tbody>
                                                        ${subjectsTable}
                                                    </tbody>
                                                </table>
                                            </div>
                                            <footer>
                                                <div class="signature-box">
                                                    <p>Confirmed By:</p>
                                                    <p>____________________</p>
                                                    <p>Date: _______________</p>
                                                </div>
                                                <div class="signature-box">
                                                    <p>Approved By:</p>
                                                    <p>____________________</p>
                                                    <p>Date: _______________</p>
                                                </div>
                                                <div class="signature-box">
                                                    <p>Assessed By:</p>
                                                    <p>____________________</p>
                                                    <p>Date: _______________</p>
                                                </div>
                                                <div class="signature-box">
                                                    <p>Copy Received By:</p>
                                                    <p>____________________</p>
                                                    <p>Date: _______________</p>
                                                </div>
                                            </footer>
                                        </div>

                                        <!-- Print Button -->
                                        <div class="print-button-container">
                                            <button class="print-button" onclick="window.print()">Print</button>
                                        </div>
                                    </body>
                                </html>
                            `);
                        printWindow.document.close();

                        console.log("Print window content written"); // Debugging
                    });
                </script>

                <script>
                    // Function to check if all fields are filled
                    function checkFields() {
                        const course = document.getElementById('course').value;
                        const yearLevel = document.getElementById('year_level').value;
                        const semester = document.getElementById('semester').value;
                        const schoolYearStart = document.getElementById('school_year_start').value;
                        const schoolYearEnd = document.getElementById('school_year_end').value;
                        const subjectsContainer = document.getElementById('subjectsContainer');

                        // Enable the button if all fields are filled and subjects are loaded
                        if (course && yearLevel && semester && schoolYearStart && schoolYearEnd && subjectsContainer.innerHTML.trim() !== '') {
                            document.getElementById('enrollStudentBtn').disabled = false;
                        } else {
                            document.getElementById('enrollStudentBtn').disabled = true;
                        }
                    }

                    // Add event listeners to check fields on change
                    document.getElementById('course').addEventListener('change', checkFields);
                    document.getElementById('year_level').addEventListener('change', checkFields);
                    document.getElementById('semester').addEventListener('change', checkFields);
                    document.getElementById('school_year_start').addEventListener('input', checkFields);
                    document.getElementById('school_year_end').addEventListener('input', checkFields);

                    // Check fields after subjects are loaded
                    document.getElementById('loadSubjectsBtn').addEventListener('click', function() {
                        // After subjects are loaded, check fields again
                        setTimeout(checkFields, 500); // Delay to ensure subjects are loaded
                    });
                </script>


                <form method="POST" action="" enctype="multipart/form-data" class="enrollForm" id="enrollForm" style="display: none;">
                    <div class="enroll-form first">
                        <div class="personal-details personal">
                            <span class="title">Department</span>


                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="student_id_input">Student ID</label>
                                    <input
                                        type="text"
                                        id="student_id_input"
                                        name="student_id"
                                        placeholder="system generated"
                                        readonly
                                        style="height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" />
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="Department">Department:</label>
                                    <select id="department" name="department"
                                        style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>
                                        <option value="" selected disabled>Select Department</option>
                                        <option value="College">College</option>
                                        <option value="Tvet">Tvet</option>
                                        <option value="JHS">JHS</option>
                                        <option value="SHS">SHS</option>
                                    </select>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="lrn">LRN: (JHS/SHS only)</label>
                                    <input type="text" id="lrn" name="lrn" disabled>
                                </div>
                            </div>

                            <span class="title">Personal Details:</span>

                            <div class="profile-photo-container" style="text-align: center;">
                                <span style="color: #333; font-weight: 500; font-size: 14px;">Profile Image</span>
                                <div class="profile-photo">
                                    <img id="profileDisplay" src="" alt="" style="display: none;">
                                </div>
                                <label for="profile" class="upload-label">
                                    <span id="uploadText" style="display: inline-block;">
                                        SELECT NEW PHOTO
                                    </span>
                                </label>
                                <input type="file" id="profile" name="profile" accept="image/*" style="display: none;" onchange="previewImage(event)" required>
                            </div>

                            <style>
                                .upload-label {
                                    cursor: pointer;
                                    color: white;
                                    font-size: 14px;
                                    display: inline-block;
                                    margin-top: 10px;
                                    transition: opacity 0.3s ease;
                                }

                                .upload-label:hover {
                                    opacity: 0.7;
                                }

                                .profile-photo-container {
                                    font-family: Arial, sans-serif;
                                }

                                .profile-photo img {
                                    display: none;
                                    width: 150px;
                                    height: 150px;
                                    border-radius: 50%;
                                    overflow: hidden;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    background-color: #f0f0f0;
                                    border: 1px solid #ddd;
                                    margin: 0 auto 10px;
                                }
                            </style>

                            <script>
                                function previewImage(event) {
                                    const file = event.target.files[0];
                                    const profileDisplay = document.getElementById('profileDisplay');
                                    const uploadText = document.getElementById('uploadText');

                                    if (file) {
                                        const reader = new FileReader();

                                        reader.onload = function() {
                                            profileDisplay.src = reader.result;
                                            profileDisplay.style.display = 'block';
                                            uploadText.textContent = 'CHANGE PHOTO';
                                        };

                                        reader.readAsDataURL(file);
                                    }
                                }
                            </script>

                            <script>
                                document.getElementById('department').addEventListener('change', function() {
                                    var lrnField = document.getElementById('lrn');
                                    if (this.value === 'JHS' || this.value === 'SHS') {
                                        lrnField.disabled = false;
                                    } else {
                                        lrnField.disabled = true;
                                        lrnField.value = ''; // Clear the LRN field if disabled
                                    }
                                });
                            </script>


                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="first_name"
                                        placeholder="Enter your First Name" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="middleName">Middle Name</label>
                                    <input type="text" id="middleName" name="middle_name"
                                        placeholder="Enter your Middle Name" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="last_name"
                                        placeholder="Enter your Last Name" required>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="email">Email Address</label>
                                    <input title="email" type="email" id="email" name="email" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="phone">Cellphone Number</label>
                                    <input type="text" id="phone" name="phone" placeholder="0912-345-6789"
                                        oninput="formatPhoneNumber(this)" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="username">Username</label>
                                    <input title="email" type="email" id="username" name="username" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="password">Password</label>
                                    <input title="password" type="password" id="password" name="password" required>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="address">Present Address</label>
                                    <input title="address" type="text" id="address" name="address" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="province">Province</label>
                                    <input title="province" type="text" id="province" name="province" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="zip_code">ZIP CODE:</label>
                                    <input title="zip_code" type="text" id="zip_code" name="zip_code" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="city">City:</label>
                                    <input title="city" type="text" id="city" name="city" required>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="emergencyName">Incase of Emergency</label>
                                    <input title="emergencyName" type="text" id="emergencyName" name="emergency_name" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="emergencyPhone">Cellphone Number:</label>
                                    <input type="text" id="emergencyPhone" name="emergency_phone"
                                        placeholder="0912-345-6789" oninput="formatPhoneNumber(this)" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="relation">Relation</label>
                                    <select id="relation" name="relation"
                                        style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>
                                        <option value="" selected disabled>Select Relation</option>
                                        <option value="Mother">Mother</option>
                                        <option value="Father">Father</option>
                                        <option value="Sister">Sister</option>
                                        <option value="Brother">Brother</option>
                                    </select>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="enrollDate">Enrollment Date</label>
                                    <input type="date" id="enrollDate" name="enroll_date"
                                        placeholder="Enter enrollment date" style="cursor: pointer;" required>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="enrollTime">Enrollment Time</label>
                                    <input type="time" id="enrollTime" name="enroll_time" style="cursor: pointer;" required>
                                </div>
                            </div>

                            <button type="submit">Submit</button>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
    <!-- MAIN CONTAINER -->

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const profileDisplay = document.getElementById('profileDisplay');
            const uploadText = document.getElementById('uploadText');

            if (file) {
                // Check file size (10MB = 10 * 1024 * 1024 bytes)
                if (file.size > 10 * 1024 * 1024) {
                    alert("File size exceeds the maximum limit of 10MB.");
                    event.target.value = ''; // Clear the file input
                    return;
                }

                const reader = new FileReader();

                reader.onload = function() {
                    profileDisplay.src = reader.result;
                    profileDisplay.style.display = 'block';
                    uploadText.textContent = 'CHANGE PHOTO';
                };

                reader.readAsDataURL(file);
            }
        }
    </script>


    <script>
        // Function to generate the Student ID
        function generateStudentID() {
            // School name
            const schoolName = "SPC";

            // Get the last two digits of the current year
            const currentYear = new Date().getFullYear();
            const lastTwoDigits = String(currentYear).slice(-2);

            // Retrieve the count of enrolled students (this can be fetched from a database or stored locally)
            let studentCount = localStorage.getItem("studentCount") || 0; // Example: Using localStorage to store the count
            studentCount = parseInt(studentCount) + 1; // Increment the count

            // Format the count to be 4 digits (e.g., 0001, 0002, etc.)
            const formattedCount = String(studentCount).padStart(4, "0");

            // Generate the Student ID
            const studentID = `${schoolName}${lastTwoDigits}-${formattedCount}`;

            // Update the Student ID input field
            document.getElementById("student_id_input").value = studentID;

            // Save the updated count (for demonstration purposes, using localStorage)
            localStorage.setItem("studentCount", studentCount);
        }

        // Attach the function to the form's submit event
        document.getElementById("enrollForm").addEventListener("submit", function(event) {
            // Prevent the form from submitting immediately
            event.preventDefault();

            // Generate the Student ID
            generateStudentID();

            // Submit the form programmatically
            this.submit();
        });
    </script>


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

    <script>
        function setCurrentDateTime() {
            const dateField = document.getElementById('enrollDate');
            const timeField = document.getElementById('enrollTime');

            const now = new Date();

            dateField.value = now.toISOString().split('T')[0];

            timeField.value = now.toTimeString().slice(0, 5);
        }

        window.onload = setCurrentDateTime;

        function formatPhoneNumber(input) {

            let value = input.value.replace(/\D/g, '');

            if (value.length > 3 && value.length <= 7) {
                value = value.replace(/(\d{4})(\d{1,3})/, '$1-$2');
            } else if (value.length > 7) {
                value = value.replace(/(\d{4})(\d{3})(\d{1,4})/, '$1-$2-$3');
            }

            input.value = value;
        }
    </script>

</body>

</html>