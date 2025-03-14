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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_level = null;
    $edu_level = null;
    $track = null;
    $course = null;
    $lrn = null;
    $course_level = '';

    $prefix = "SPC";
    $year = date("Y");
    $yearSuffix = substr($year, -2);

    $lastNumber = 0;
    $table = '';

    if (isset($_POST['grade_level_course'])) {
        $grade_level = $_POST['grade_level_course'];
        if (in_array($grade_level, ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'])) {
            $edu_level = "JHS";
            $table = 'jhs_students';
            $lrn = $_POST['lrn'] ?? null;
        } elseif (in_array($grade_level, ['Grade 11', 'Grade 12'])) {
            $edu_level = "SHS";
            $table = 'shs_students';
            $lrn = $_POST['lrn'] ?? null;
        } elseif ($grade_level === 'TVET') {
            $edu_level = "TVET";
            $table = 'tvet_students';
        } elseif ($grade_level === 'COLLEGE') {
            $edu_level = "College";
            $table = 'college_students';
        }
    }

    if (!$table) {
        echo "<script>alert('Invalid grade level course.');</script>";
        exit();
    }

    $query = $conn->prepare("SELECT student_id FROM jhs_students WHERE student_id LIKE ? UNION SELECT student_id FROM shs_students WHERE student_id LIKE ? UNION SELECT student_id FROM tvet_students WHERE student_id LIKE ? UNION SELECT student_id FROM college_students WHERE student_id LIKE ? ORDER BY student_id DESC LIMIT 1");
    $searchPattern = $prefix . $yearSuffix . "-%";
    $query->bind_param("ssss", $searchPattern, $searchPattern, $searchPattern, $searchPattern);
    $query->execute();
    $result = $query->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $lastNumber = (int)substr($row['student_id'], -4);
    }

    $student_id = $prefix . $yearSuffix . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $province = $_POST['province'];
    $zip_code = $_POST['zip_code'];
    $city = $_POST['city'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_phone = $_POST['emergency_phone'];
    $relation = $_POST['relation'];
    $enroll_date = $_POST['enroll_date'];
    $enroll_time = $_POST['enroll_time'];
    $session = $_POST['session'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($grade_level === 'Grade 11' && isset($_POST['track_grade_11'])) {
        $track = $_POST['track_grade_11'];
    }
    if ($grade_level === 'Grade 12' && isset($_POST['track_grade_12'])) {
        $track = $_POST['track_grade_12'];
    }
    if ($grade_level === 'TVET' && isset($_POST['track_tvet'])) {
        $track = $_POST['track_tvet'];
        $track_level = $_POST['track_level'];
        $lrn = null;
    }
    if ($grade_level === 'COLLEGE' && isset($_POST['course_college'])) {
        $course = $_POST['course_college'];
        $course_level = isset($_POST['course_level']) ? $_POST['course_level'] : '';
        $lrn = null;
    }

    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $imageName = $_FILES['profile']['name'];
        $imageTmpName = $_FILES['profile']['tmp_name'];
        $imageSize = $_FILES['profile']['size'];
        $imageType = $_FILES['profile']['type'];

        $imageData = file_get_contents($imageTmpName);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imageType, $allowedTypes)) {
            die("Invalid image type. Please upload a JPG, PNG, or GIF image.");
        }
    } else {
        $imageData = null;
    }

    if ($table == 'jhs_students') {
        $stmt = $conn->prepare("INSERT INTO jhs_students (student_id, first_name, middle_name, last_name, email, phone, username, password, edu_level, grade_level, lrn, profile, address, province, city, zip_code, emergency_name, emergency_phone, relation, enroll_date, enroll_time, session) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssssssssssssssssssss",
            $student_id,
            $first_name,
            $middle_name,
            $last_name,
            $email,
            $phone,
            $username,
            $hashed_password,
            $edu_level,
            $grade_level,
            $lrn,
            $imageData,
            $address,
            $province,
            $city,
            $zip_code,
            $emergency_name,
            $emergency_phone,
            $relation,
            $enroll_date,
            $enroll_time,
            $session
        );
    } elseif ($table == 'shs_students') {
        $stmt = $conn->prepare("INSERT INTO shs_students (student_id, first_name, middle_name, last_name, email, phone, username, password, edu_level, grade_level, track, lrn, profile, address, province, city, zip_code, emergency_name, emergency_phone, relation, enroll_date, enroll_time, session) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssssssssssssssssssss",
            $student_id,
            $first_name,
            $middle_name,
            $last_name,
            $email,
            $phone,
            $username,
            $hashed_password,
            $edu_level,
            $grade_level,
            $track,
            $lrn,
            $imageData,
            $address,
            $province,
            $city,
            $zip_code,
            $emergency_name,
            $emergency_phone,
            $relation,
            $enroll_date,
            $enroll_time,
            $session
        );
    } elseif ($table == 'tvet_students') {
        $stmt = $conn->prepare("INSERT INTO tvet_students (student_id, first_name, middle_name, last_name, email, phone, username, password, track, track_level, profile, address, province, city, zip_code, emergency_name, emergency_phone, relation, enroll_date, enroll_time, session) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssssssssssssssssss",
            $student_id,
            $first_name,
            $middle_name,
            $last_name,
            $email,
            $phone,
            $username,
            $hashed_password,
            $track,
            $track_level,
            $imageData,
            $address,
            $province,
            $city,
            $zip_code,
            $emergency_name,
            $emergency_phone,
            $relation,
            $enroll_date,
            $enroll_time,
            $session
        );
    } else {
        $stmt = $conn->prepare("INSERT INTO college_students (student_id, first_name, middle_name, last_name, email, phone, username, password, course, course_level, profile, address, province, city, zip_code, emergency_name, emergency_phone, relation, enroll_date, enroll_time, session) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssssssssssssssssss",
            $student_id,
            $first_name,
            $middle_name,
            $last_name,
            $email,
            $phone,
            $username,
            $hashed_password,
            $course,
            $course_level,
            $imageData,
            $address,
            $province,
            $city,
            $zip_code,
            $emergency_name,
            $emergency_phone,
            $relation,
            $enroll_date,
            $enroll_time,
            $session
        );
    }

    if ($stmt->execute()) {
        echo "<script>
                alert('Student successfully added.');
                window.location.href = 'enroll-student.php'; // Redirect back to test.php
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $stmt->error . "');
                window.location.href = 'enroll-student.php'; // Redirect back to test.php
              </script>";
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
        <h2 id="dashboard-title">Enroll a Student</h2>
        <ul class="nav-list">
            <li><a href="./dashboard.php" data-title="Dashboard"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a></li>
            <li><a href="./enroll-student.php" data-title="Enroll a Student" class="active"><i class="fa-solid fa-user-plus"></i> <span>Enroll a Student</span></a></li>
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
                <div class="sub-main-container-header end">
                    <button onclick="showForm('enrollForm')">Enroll</button>
                    <button onclick="showForm('loadingSubjectForm')">Loading Subject</button>
                    <button onclick="showForm('paymentMethodForm')">Payment Method</button>
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
                    <input type="text" id="studentID" name="studentID" placeholder="Enter Student ID" required>
                    <button id="submitExistingID" style="height: 35px; border: none; outline: none; color: white; border-radius: 5px; cursor: pointer; background-color: #2c3e50; transition: opacity ease 0.3s;">
                        Submit
                    </button>
                </div>

                <div id="studentDetails" class="student-details">
                    <h3>Student Details</h3>
                    <div class="profile-container">
                        <img id="studentProfileImage" src="./uploads/C.jpg" alt="Student Profile" class="profile-images">
                    </div>
                    <p><strong>ID:</strong> <span id="studentIDDetails"></span></p>
                    <p><strong>Name:</strong> <span id="studentNameDetails"></span></p>

                    <div id="jhsDetails" class="details-section">
                        <p><strong>Department:</strong> <span id="studentEduLevel"></span></p>
                        <p><strong>Grade Level:</strong> <span id="studentGradeDetails"></span></p>
                        <button class="load-button">Load Subjects</button>
                    </div>

                    <div id="shsDetails" class="details-section">
                        <p><strong>Department:</strong> <span id="studentEdusLevel"></span></p>
                        <p><strong>Grade Level:</strong> <span id="studentGradesDetails"></span></p>
                        <p><strong>Track:</strong> <span id="studentTracksDetails"></span></p>
                        <button class="load-button">Load Subjects</button>
                    </div>

                    <div id="tvetDetails" class="details-section">
                        <p><strong>Department:</strong> <span>COLLEGE</span></p>
                        <p><strong>Course:</strong> <span id="studentTrackDetails"></span></p>
                        <p><strong>Course Level:</strong> <span id="studentTrackLevelDetails"></span></p>
                        <button class="load-button">Load Subjects</button>
                    </div>

                    <div id="collegeDetails" class="details-section">
                        <p><strong>Department:</strong> <span>College</span></p>
                        <p><strong>Course:</strong> <span id="studentCourseDetails"></span></p>
                        <p><strong>Year Level:</strong> <span id="studentYearDetails"></span></p>
                        <button class="load-button">Load Subjects</button>
                    </div>
                </div>

                <style>
                    /* General Container */
                    .student-details {
                        display: none;
                        margin-top: 20px;
                        margin-left: 100px;
                    }

                    /* Profile Section */
                    .profile-container {
                        text-align: center;
                        margin-bottom: 20px;
                    }

                    .profile-images {
                        width: 150px;
                        height: 150px;
                        border-radius: 50%;
                        object-fit: cover;
                        border: 1px solid #2c3e50;
                    }

                    /* Section Details */
                    .details-section {
                        margin-bottom: 20px;
                    }

                    /* Button Styling */
                    .load-button {
                        height: 35px;
                        margin-left: 100px;
                        max-width: 300px;
                        width: 100%;
                        border: none;
                        outline: none;
                        color: white;
                        border-radius: 5px;
                        cursor: pointer;
                        background-color: #2c3e50;
                        transition: opacity 0.3s ease;
                    }

                    .load-button:hover {
                        opacity: 0.8;
                    }
                </style>


                <!-- <style>
                    #studentDetails {
                        display: none;
                        margin-top: 20px;
                        font-family: 'Arial', sans-serif;
                    }

                    #studentDetails h3 {
                        font-size: 1.5em;
                        color: #333;
                        margin-bottom: 15px;
                    }

                    #studentDetails p {
                        font-size: 1em;
                        color: #555;
                        margin: 5px 0;
                    }

                    #studentDetails p strong {
                        color: #4070f4;
                    }

                    #studentDetails div {
                        margin-bottom: 20px;
                    }

                    #studentDetails div button {
                        height: 35px;
                        margin-left: 100px;
                        max-width: 300px;
                        width: 100%;
                        border: none;
                        outline: none;
                        color: white;
                        border-radius: 5px;
                        cursor: pointer;
                        background-color: #4070f4;
                        transition: opacity ease 0.3s, transform 0.3s ease;
                    }

                    #studentDetails div button:hover {
                        opacity: 0.9;
                        transform: translateY(-2px);
                    }

                    /* Styling for Department section */
                    #studentEduLevel,
                    #studentEdusLevel,
                    #studentTrackDetails,
                    #studentCourseDetails {
                        font-weight: bold;
                        color: #333;
                    }

                    /* Styling for subject list section */
                    #assignedSubjects {
                        display: none;
                        margin-top: 20px;
                        background-color: #f4f7fc;
                        padding: 15px;
                        border-radius: 10px;
                    }

                    #assignedSubjects ul {
                        list-style-type: none;
                        padding-left: 0;
                    }

                    #assignedSubjects li {
                        font-size: 1.1em;
                        color: #444;
                        margin: 5px 0;
                    }

                    /* Visibility for subject list when clicked */
                    #assignedSubjects.show {
                        display: block;
                    }

                    /* Styling for each education level details */
                    #jhsDetails,
                    #shsDetails,
                    #tvetDetails,
                    #collegeDetails {
                        display: none;
                        padding: 15px;
                        background-color: #f9f9f9;
                        border-radius: 10px;
                        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
                    }

                    /* Responsive Design for Smaller Screens */
                    @media screen and (max-width: 600px) {
                        #studentDetails {
                            margin-top: 10px;
                        }

                        #studentDetails h3 {
                            font-size: 1.2em;
                        }

                        #studentDetails p {
                            font-size: 0.9em;
                        }

                        #studentDetails div button {
                            margin-left: 0;
                            width: auto;
                            max-width: 100%;
                        }
                    }
                </style> -->


                <script>
                    document.getElementById('enrollNewBtn').addEventListener('click', () => {
                        document.getElementById('enrollNewBtn').style.display = 'none';
                        document.getElementById('enrollExistingBtn').style.display = 'none';
                        document.getElementById('enrollForm').style.display = 'block';
                    });

                    document.getElementById('enrollExistingBtn').addEventListener('click', () => {
                        document.getElementById('enrollNewBtn').style.display = 'none';
                        document.getElementById('enrollExistingBtn').style.display = 'none';
                        document.getElementById('existingStudentPopup').style.display = 'block';
                    });

                    document.getElementById('submitExistingID').addEventListener('click', async () => {
                        const studentID = document.getElementById('studentID').value;

                        try {
                            const response = await fetch(`get_student_details.php?studentID=${studentID}`);
                            const data = await response.json();

                            if (data.success) {
                                document.getElementById('studentProfileImage').src = data.profileImg;
                                document.getElementById('studentIDDetails').textContent = data.studentID;
                                document.getElementById('studentNameDetails').textContent = data.name;

                                document.getElementById('jhsDetails').style.display = 'none';
                                document.getElementById('shsDetails').style.display = 'none';
                                document.getElementById('tvetDetails').style.display = 'none';
                                document.getElementById('collegeDetails').style.display = 'none';

                                if (data.educationLevel === 'JHS') {
                                    document.getElementById('studentEduLevel').textContent = data.edu_level || 'N/A';
                                    document.getElementById('studentGradeDetails').textContent = data.grade_level || 'N/A';
                                    document.getElementById('jhsDetails').style.display = 'block';
                                } else if (data.educationLevel === 'SHS') {
                                    document.getElementById('studentEdusLevel').textContent = data.edu_level || 'N/A';
                                    document.getElementById('studentGradesDetails').textContent = data.grade_level || 'N/A';
                                    document.getElementById('studentTracksDetails').textContent = data.track || 'N/A';
                                    document.getElementById('shsDetails').style.display = 'block';
                                } else if (data.educationLevel === 'TVET') {
                                    document.getElementById('studentTrackDetails').textContent = data.track || 'N/A';
                                    document.getElementById('studentTrackLevelDetails').textContent = data.track_level || 'N/A';
                                    document.getElementById('tvetDetails').style.display = 'block';
                                } else if (data.educationLevel === 'College') {
                                    document.getElementById('studentCourseDetails').textContent = data.course || 'N/A';
                                    document.getElementById('studentYearDetails').textContent = data.course_level || 'N/A';
                                    document.getElementById('collegeDetails').style.display = 'block';
                                }

                                document.getElementById('studentDetails').style.display = 'block';
                            } else {
                                alert(data.message || "Student ID not found.");
                            }
                        } catch (error) {
                            console.error("Error fetching student details:", error);
                            alert("An error occurred while fetching student details.");
                        }
                    });
                </script>


                <form method="POST" action="" enctype="multipart/form-data" class="enrollForm" id="enrollForm" style="display: none;">
                    <div class="enroll-form first">
                        <div class="personal-details personal">
                            <span class="title">Grade Level Course:</span>
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
                                    <select id="trackGrade11" name="track_grade_11" disabled style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;">
                                        <option value="" selected disabled>Select Track</option>
                                        <option value="GAS">GAS</option>
                                        <option value="STEM">STEM</option>
                                        <option value="WAS">WAS</option>
                                    </select>
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="grade12">Grade 12</label>
                                    <input type="checkbox" id="grade12" name="grade_level_course" value="Grade 12" onclick="handleCheckboxChange('grade12')">
                                    <select id="trackGrade12" name="track_grade_12" disabled style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;">
                                        <option value="" selected disabled>Select Track</option>
                                        <option value="GAS">GAS</option>
                                        <option value="STEM">STEM</option>
                                        <option value="WAS">WAS</option>
                                    </select>
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="tvet">TVET</label>
                                    <input type="checkbox" id="tvet" name="grade_level_course" value="TVET" onclick="handleCheckboxChange('tvet')">
                                    <select id="trackTvet" name="track_tvet" disabled style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;">
                                        <option value="" selected disabled>Select Course</option>
                                        <option value="BTVTeD-AT">BTVTeD-AT</option>
                                        <option value="BTVTeD-HVACR TECH">BTVTeD-HVACR TECH</option>
                                        <option value="BTVTeD-FSM">BTVTeD-FSM</option>
                                        <option value="BTVTeD-ET">BTVTeD-ET</option>
                                    </select>
                                    <select id="trackLevel" name="track_level" disabled style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;">
                                        <option value="" selected disabled>Select Level</option>
                                        <option value="1stYear">1st Year</option>
                                        <option value="2ndYear">2nd Year</option>
                                        <option value="3rdYear">3rd Year</option>
                                        <option value="4thYear">4th Year</option>
                                    </select>
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="college">College</label>
                                    <input type="checkbox" id="college" name="grade_level_course" value="COLLEGE" onclick="handleCheckboxChange('college')">
                                    <select id="courseCollege" name="course_college" disabled style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;">
                                        <option value="" selected disabled>Select Course</option>
                                        <option value="BSIT">Bachelor of Science in Information Technology</option>
                                        <option value="BSHM">Bachelor of Science in Hospitality Management</option>
                                        <option value="BSBA">Bachelor of Science in Business Administration (OM)</option>
                                        <option value="BSTM">Bachelor of Science in Tourism Management</option>
                                    </select>
                                    <select id="courseLevel" name="course_level" disabled style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;">
                                        <option value="" selected disabled>Select Level</option>
                                        <option value="1stYear">1st Year</option>
                                        <option value="2ndYear">2nd Year</option>
                                        <option value="3rdYear">3rd Year</option>
                                        <option value="4thYear">4th Year</option>
                                    </select>
                                </div>
                            </div>

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
                                    <label for="Session">Session</label>
                                    <select id="Session" name="session"
                                        style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" required>
                                        <option value="" selected disabled>Select Session</option>
                                        <option value="Morning">Morning - unavailable</option>
                                        <option value="Afternoon">Afternoon</option>
                                    </select>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="lrn">LRN: (JHS/SHS only)</label>
                                    <input type="text" id="lrn" name="lrn" disabled>
                                </div>
                            </div>

                            <span class="title">Personal Data</span>

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

                            <!-- Student ID Field -->
                            <input type="hidden" id="studentID" name="student_id" value="">

                            <button type="submit">Submit</button>

                            <style>
                                button.nextBtn {
                                    background-color: #007bff;
                                    color: white;
                                    padding: 10px 20px;
                                    border: none;
                                    border-radius: 5px;
                                    cursor: not-allowed !important;
                                    opacity: 0.6;
                                    transition: opacity 0.3s ease, cursor 0.3s ease;
                                }

                                button.nextBtn.enabled {
                                    cursor: pointer !important;
                                    opacity: 1;
                                }

                                button.nextBtn.enabled:hover {
                                    opacity: 0.7;
                                }
                            </style>

                            <script>
                                const checkbox = document.getElementById('confirmation');
                                const button = document.getElementById('nextToLoadingSubjectBtn');

                                function toggleButtonState() {
                                    if (checkbox.checked) {
                                        button.disabled = false;
                                        button.classList.add('enabled');
                                    } else {
                                        button.disabled = true;
                                        button.classList.remove('enabled');
                                    }
                                }

                                checkbox.addEventListener('change', toggleButtonState);

                                toggleButtonState();
                            </script>


                        </div>
                    </div>
                </form>

                <!-- Loading Subjects Form -->
                <form action="" class="loadingSubjectForm" id="loadingSubjectForm" style="display: none;">
                    <div class="enroll-form first">
                        <div class="personal-details personal">
                            <span class="title">Loading Subjects</span>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="enrollDate">Date of Enrollment</label>
                                    <input type="date" name="enrollDate" disabled>
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="1stSem">1st SEM</label>
                                    <input type="checkbox" id="1stSem" name="1stSem" onclick="handleLevelChange('1stSem')">
                                    <label for="2ndSem">2nd SEM</label>
                                    <input type="checkbox" id="2ndSem" name="2ndSem" onclick="handleLevelChange('2ndSem')">
                                    <label for="summer">SUMMER</label>
                                    <input type="checkbox" id="summer" name="2ndSem" onclick="handleLevelChange('summer')">
                                </div>

                                <div class="enrol-input-fields">
                                    <span>SY</span>
                                    <label for="enrollDate">20</label>
                                    <input type="text" name="enrollDate" style="width: 30px; height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 5px; font-size: 11px; outline: none;">
                                    <label for="enrollDate">- 20</label>
                                    <input type="text" name="enrollDate" style="width: 30px; height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 5px; font-size: 11px; outline: none;">
                                </div>
                            </div>

                            <span class="title">Status (Please Check)</span>

                            <div class="enroll-fields">
                                <div class="enrol-input-fields">
                                    <label for="new">NEW</label>
                                    <input type="checkbox" id="new" onclick="handleStatusChange('new')">

                                    <label for="old">OLD</label>
                                    <input type="checkbox" id="old" onclick="handleStatusChange('old')">

                                    <label for="transferee">TRANSFEREE</label>
                                    <input type="checkbox" id="transferee" onclick="handleStatusChange('transferee')">

                                    <label for="returnee">RETURNEE</label>
                                    <input type="checkbox" id="returnee" onclick="handleStatusChange('returnee')">

                                    <label for="crossEnrollee">CROSS ENROLLEE</label>
                                    <input type="checkbox" id="crossEnrollee" onclick="handleStatusChange('crossEnrollee')">
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="firstName">First Name</label>
                                    <input type="text" disabled>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="middleName">Middle Name</label>
                                    <input type="text" disabled>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" disabled>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="studentID">Student ID#</label>
                                    <input type="text" id="studentID" disabled>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="course">Course</label>
                                    <input type="text" id="course" placeholder="BSIT / BSBA / BSHM / BSTM" disabled>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="yearLevel">Year Level</label>
                                    <select id="yearLevel" style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px;">
                                        <option value="" disabled selected>Select Year Level</option>
                                        <option value="1stYear">1st Year</option>
                                        <option value="2ndYear">2nd Year</option>
                                        <option value="3rdYear">3rd Year</option>
                                        <option value="4thYear">4th Year</option>
                                    </select>
                                </div>
                            </div>

                            <span class="title">FIRST TERM SUBJECTS</span>

                            <div class="enroll-fields">
                                <div class="enrol-input-fields">
                                    <table class="first_term_subjects">
                                        <thead>
                                            <tr>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Subject Code</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Description</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Days</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Time</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Room No</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Units</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- New rows will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <button type="button" onclick="addRow('first_term_subjects')" style="margin-top: 10px;">Add First Term Subject</button>

                            <span class="title">SECOND TERM SUBJECTS</span>

                            <div class="enroll-fields">
                                <div class="enrol-input-fields">
                                    <table class="second_term_subjects">
                                        <thead>
                                            <tr>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Subject Code</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Description</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Days</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Time</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Room No</th>
                                                <th style="font-size: 12px; color: #2e2e2e; font-weight: 500;">Units</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- New rows will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <button type="button" onclick="addRow('second_term_subjects')" style="margin-top: 10px;">Add Second Term Subject</button>

                            <br>

                            <button title="Next" class="nextBtn" id="paymentMethodForm">
                                <span class="btnText">Proceed to Payment</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>

                        </div>
                    </div>
                </form>
                <!-- Loading Subjects Form -->

                <!-- Payment Method Form -->
                <form action="" class="paymentMethodForm" id="paymentMethodForm" style="display: none;">
                    <div class="payment-method">
                        <h3>Payment Method</h3>
                        <!-- Payment Method Form Fields -->
                        <label for="paymentType">Select Payment Type:</label>
                        <select id="paymentType" required>
                            <option value="">Choose a payment method</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                        </select>
                        <button title="Submit" class="submitBtn">
                            <span class="btnText">Submit Enrollment</span>
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </form>

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
        document.getElementById('nextToLoadingSubjectBtn').addEventListener('click', () => {
            document.getElementById('enrollForm').style.display = 'none';
            document.getElementById('loadingSubjectForm').style.display = 'block';
        });

        document.getElementById('nextToPaymentBtn').addEventListener('click', () => {
            document.getElementById('loadingSubjectForm').style.display = 'none';
            document.getElementById('paymentMethodForm').style.display = 'block';
        });

        function handleLevelChange(selectedId) {
            const levels = ['1stSem', '2ndSem', 'summer']

            levels.forEach(id => {
                if (id !== selectedId) document.getElementById(id).checked = false;
            });
        }

        function handleStatusChange(selectedId) {
            const status = ['old', 'new', 'transferee', 'returnee', 'crossEnrollee']

            status.forEach(id => {
                if (id !== selectedId) document.getElementById(id).checked = false;
            });
        }

        function handleCheckboxChange(id) {
            const checkbox = document.getElementById(id);
            const trackField = document.getElementById(`track${id.charAt(0).toUpperCase() + id.slice(1)}`);
            const courseField = document.getElementById('courseCollege');
            const trackLevelField = document.getElementById('trackLevel');
            const courseLevelField = document.getElementById('courseLevel');
            const lrnField = document.getElementById('lrn');

            const gradeLevels = ['grade7', 'grade8', 'grade9', 'grade10', 'grade11', 'grade12'];
            const tvetAndCollege = ['tvet', 'college'];

            gradeLevels.forEach((item) => {
                if (item !== id) document.getElementById(item).checked = false;
            });
            tvetAndCollege.forEach((item) => {
                if (item !== id) document.getElementById(item).checked = false;
            });

            document.getElementById('trackGrade11').disabled = true;
            document.getElementById('trackGrade12').disabled = true;
            document.getElementById('trackTvet').disabled = true;
            document.getElementById('trackLevel').disabled = true;
            document.getElementById('courseCollege').disabled = true;
            document.getElementById('courseLevel').disabled = true;

            if (['grade7', 'grade8', 'grade9', 'grade10', 'grade11', 'grade12'].includes(id)) {
                lrnField.disabled = false;
            } else {
                lrnField.disabled = true;
                lrnField.value = '';
            }

            if (id === 'grade11') {
                document.getElementById('trackGrade11').disabled = false;
            } else if (id === 'grade12') {
                document.getElementById('trackGrade12').disabled = false;
            } else if (id === 'tvet') {
                document.getElementById('trackTvet').disabled = false;
                document.getElementById('trackLevel').disabled = false;
            } else if (id === 'college') {
                document.getElementById('courseCollege').disabled = false;
                document.getElementById('courseLevel').disabled = false;
            }

            generateStudentID();
        }

        function validateEnrollForm() {
            return true;
        }

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