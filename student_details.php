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
    $imageData = null;
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $imageTmpName = $_FILES['profile']['tmp_name'];
        $imageType = $_FILES['profile']['type'];

        $imageData = file_get_contents($imageTmpName);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imageType, $allowedTypes)) {
            die("Invalid image type. Please upload a JPG, PNG, or GIF image.");
        }
    }

    // Insert data into the database
    $sql = "INSERT INTO enrollments (student_id, department, lrn, profile, first_name, middle_name, last_name, email, phone, username, password, address, province, zip_code, city, emergency_name, emergency_phone, relation, enroll_date, enroll_time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Use a variable for the profile data
        $profileData = ($imageData !== null) ? $imageData : NULL;

        $stmt->bind_param(
            "sssbssssssssssssssss",
            $student_id,
            $department,
            $lrn,
            $profileData, // Use the variable here
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

        if ($imageData !== null) {
            $stmt->send_long_data(3, $imageData); // Send long data for BLOB
        }

        if ($stmt->execute()) {
            echo "<script>
                    alert('Student successfully added.');
                    window.location.href = 'student_details.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: " . $stmt->error . "');
                    window.location.href = 'student_details.php';
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                alert('Error preparing the statement: " . $conn->error . "');
                window.location.href = 'student_details.php';
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
            <li><a href="./student_details.php" data-title="Enroll a Student" class="active"><i class="fa-solid fa-user-plus"></i> <span>Enroll a Student</span></a></li>
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

                <div id="subject-loading-result"></div>

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
                                document.getElementById('studentNameDetails').textContent = data.name;

                                // Hide all details sections
                                document.getElementById('jhsDetails').style.display = 'none';
                                document.getElementById('shsDetails').style.display = 'none';
                                document.getElementById('tvetDetails').style.display = 'none';
                                document.getElementById('collegeDetails').style.display = 'none';

                                // Show the appropriate details section based on education level
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

                                // Add event listener to the "Load Subjects" button
                                document.querySelectorAll('.load-button').forEach(button => {
                                    button.addEventListener('click', async () => {
                                        try {
                                            const course = data.course || data.track;
                                            const year_level = data.course_level || data.track_level;

                                            console.log("Course:", course); // Debugging: Log the course
                                            console.log("Year Level:", year_level); // Debugging: Log the year level

                                            const subjectResponse = await fetch(`get_student_subjects.php?course=${course}&year_level=${year_level}`);
                                            const subjectData = await subjectResponse.json();

                                            console.log("Subject Data:", subjectData); // Debugging: Log the response from the server

                                            if (subjectData.success) {
                                                const subjectLoadingResult = document.getElementById('subject-loading-result');
                                                subjectLoadingResult.innerHTML = '';

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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${subjectData.subjects.map(subject => `
                                                <tr>
                                                    <td>
                                                        <input type="text" 
                                                            style="width: 120px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" 
                                                            value="${subject.subject_code}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                            style="width: 400px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" 
                                                            value="${subject.description}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                            style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" 
                                                            value="${subject.lec}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                            style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" 
                                                            value="${subject.lab}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                            style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" 
                                                            value="${subject.unit_no}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                            style="width: 80px; height: 30px; border-radius: 3px; border: 1px solid #aaa; padding: 0 15px; font-size: 11px; outline: none;" 
                                                            value="${subject.pre_req}" readonly>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    `;

                                                subjectLoadingResult.appendChild(table);
                                            } else {
                                                alert('No subjects found for this student.');
                                            }
                                        } catch (error) {
                                            console.error("Error fetching student subjects:", error);
                                            alert("An error occurred while fetching student subjects.");
                                        }
                                    });
                                });

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

                    #subject-loading-result {
                        margin-top: 20px;
                        padding: 10px;
                        border: 1px solid #ccc;
                        background-color: #f9f9f9;
                    }
                </style>


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