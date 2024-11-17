<?php
session_start();
include("connect.php");

// Prevent caching to ensure the page is not cached and always fresh on each load
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin-login.php");
    exit();
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
            <h3 id="dashboard-title" class="sidebar-title" style="padding-top: 20px; padding-left: 40px;">Enroll a Student</h3>
        </div>
        <ul class="nav-list">
            <li><a href="./admin-dashboard.php" data-title="Dashboard"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a></li>
            <li><a href="#enroll-student" data-title="Enroll a Student" class="active"><i class="fa-solid fa-user-plus"></i> <span>Enroll a Student</span></a></li>

            <li class="sidebar-dropdown">
                <a href="#department" class="sidebar-dropdown-btn" data-title="Department" class="menu-item">
                    <i class="fa-solid fa-building"></i> <span>Department</span>
                    <i class="fa-solid fa-caret-right dropdown-icon"></i>
                </a>
                <ul class="sidebar-dropdown-content">
                    <li><a href="#tvet" data-title="TVET" class="menu-item"><i class="fa-solid fa-tools"></i> <span>TVET</span></a></li>
                    <li><a href="#high-school" data-title="High School" class="menu-item"><i class="fa-solid fa-school"></i> <span>HIGH SCHOOL</span></a></li>
                    <li><a href="#college" data-title="College" class="menu-item"><i class="fa-solid fa-university"></i> <span>COLLEGE</span></a></li>
                </ul>
            </li>

            <li class="sidebar-dropdown">
                <a href="#course" class="sidebar-dropdown-btn" data-title="Course" class="menu-item">
                    <i class="fa-solid fa-book"></i> <span>Course</span>
                    <i class="fa-solid fa-caret-right dropdown-icon"></i>
                </a>
                <ul class="sidebar-dropdown-content">
                    <li><a href="#bsit" data-title="BSIT" class="menu-item"><i class="fa-solid fa-laptop-code"></i> <span>BSIT</span></a></li>
                    <li><a href="#bsba" data-title="BSBA" class="menu-item"><i class="fa-solid fa-chart-line"></i> <span>BSBA</span></a></li>
                    <li><a href="#bshm" data-title="BSHM" class="menu-item"><i class="fa-solid fa-utensils"></i> <span>BSHM</span></a></li>
                    <li><a href="#bstm" data-title="BSTM" class="menu-item"><i class="fa-solid fa-plane"></i> <span>BSTM</span></a></li>
                </ul>
            </li>

            <li><a href="#payment-management" data-title="Payment Management" class="menu-item"><i class="fa-solid fa-credit-card"></i> <span>Payment Management</span></a></li>
            <li><a href="#grading-system" data-title="Grading System" class="menu-item"><i class="fa-solid fa-graduation-cap"></i> <span>Grading System</span></a></li>
            <li><a href="#student-attendance" data-title="Student Attendance" class="menu-item"><i class="fa-solid fa-calendar-check"></i> <span>Student Attendance</span></a></li>
            <li><a href="#announcement" data-title="Announcement" class="menu-item"><i class="fa-solid fa-bullhorn"></i> <span>Announcement</span></a></li>
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
                            // Check if session username is set
                            if (isset($_SESSION['username'])) {
                                $email = $_SESSION['username'];
                                $query = mysqli_query($conn, "SELECT * FROM admin_user WHERE username='$email'");

                                if ($row = mysqli_fetch_assoc($query)) {
                                    // Display profile image if it exists
                                    if (!empty($row['profile'])) {
                                        echo '<img src="' . htmlspecialchars($row['profile']) . '" alt="Profile Image">';
                                    } else {
                                        echo '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">'; // Default image if no profile is found
                                    }
                                } else {
                                    echo '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">'; // Default image for failed query
                                }
                            } else {
                                echo '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">'; // Default image if no session
                            }
                            ?>
                        </a>
                    </div>

                    <div class="name-access">
                        <?php
                        // Check if session username is set
                        if (isset($_SESSION['username'])) {
                            $email = $_SESSION['username'];
                            $query = mysqli_query($conn, "SELECT * FROM admin_user WHERE username='$email'");

                            if ($row = mysqli_fetch_assoc($query)) {
                                // Display the first and last name with a space between them
                                echo "<p>" . htmlspecialchars($row['firstName']) . " " . htmlspecialchars($row['lastName']) . "</p>";

                                // Display the access level (admin or teacher)
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

                        
                <form action="" class="enrollForm" id="enrollForm" style="display: none;">
                    <div class="enroll-form first">
                        <div class="personal-details personal">
                            <span class="title">Grade Level Course:</span>

                            <div class="enroll-fields">
                                <div class="enrol-input-fields">
                                    <label for="grade7">Grade 7</label>
                                    <input type="checkbox" id="grade7" onclick="handleCheckboxChange('grade7')">
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="grade8">Grade 8</label>
                                    <input type="checkbox" id="grade8" onclick="handleCheckboxChange('grade8')">
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="grade9">Grade 9</label>
                                    <input type="checkbox" id="grade9" onclick="handleCheckboxChange('grade9')">
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="grade10">Grade 10</label>
                                    <input type="checkbox" id="grade10" onclick="handleCheckboxChange('grade10')">
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="grade11">Grade 11</label>
                                    <input type="checkbox" id="grade11" onclick="handleCheckboxChange('grade11')">
                                    <select title="select" id="track11" disabled>
                                        <option value="">Select Track</option>
                                        <option value="GAS">GAS</option>
                                        <option value="STEM">STEM</option>
                                        <option value="WAS">WAS</option>
                                    </select>
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="grade12">Grade 12</label>
                                    <input type="checkbox" id="grade12" onclick="handleCheckboxChange('grade12')">
                                    <select title="select" id="track12" disabled>
                                        <option value="">Select Track</option>
                                        <option value="GAS">GAS</option>
                                        <option value="STEM">STEM</option>
                                        <option value="WAS">WAS</option>
                                    </select>
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="tvet">TVET</label>
                                    <input type="checkbox" id="tvet" onclick="handleCheckboxChange('tvet')">
                                    <select title="select" id="trackTvet" disabled>
                                        <option value="">Select Track</option>
                                        <option value="Automotive">Automotive</option>
                                        <option value="Front Office">Front Office</option>
                                        <option value="Sample">Sample</option>
                                    </select>
                                </div>

                                <div class="enrol-input-fields">
                                    <label for="college">COLLEGE</label>
                                    <input type="checkbox" id="college" onclick="handleCheckboxChange('college')" required>
                                    <select title="select" id="courseCollege" disabled>
                                        <option value="">Select Course</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSHM">BSHM</option>
                                        <option value="BSBA">BSBA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="studentID">Student ID#</label>
                                    <input type="text" id="studentID" placeholder="system generated" disabled>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="Session">Session</label>
                                    <select id="Session" style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px;">
                                        <option value="" selected disabled>Select Session</option>
                                        <option value="Morning">Morning - unavailable</option>
                                        <option value="Afternoon">Afternoon</option>
                                    </select>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="lrn">LRN: (JHS/SHS only)</label>
                                    <input type="text" id="lrn" disabled>
                                </div>
                            </div>

                            <span class="title">Personal Data</span>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="firstName">First Name</label>
                                    <input type="text" placeholder="Enter your First Name">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="middleName">Middle Name</label>
                                    <input type="text" placeholder="Enter your First Name">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" placeholder="Enter your First Name">
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="email">Email Address</label>
                                    <input title="email" type="email">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="phone">Cellphone Number</label>
                                    <input type="text" id="phone" name="phone" placeholder="0912-345-6789"
                                        oninput="formatPhoneNumber(this)">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="username">Username</label>
                                    <input title="email" type="email">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="password">Password</label>
                                    <input title="password" type="password">
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="address">Present Address</label>
                                    <input title="address" type="text">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="address">Province</label>
                                    <input title="address" type="text">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="address">ZIP CODE:</label>
                                    <input title="address" type="text">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="address">City:</label>
                                    <input title="address" type="text">
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="enroll-input-fields">
                                    <label for="emergencyName">Incase of Emergency</label>
                                    <input title="emergencyName" type="text">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="phone">Cellphone Number:</label>
                                    <input type="text" id="phone" name="phone" placeholder="0912-345-6789"
                                        oninput="formatPhoneNumber(this)">
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="relation">Relation</label>
                                    <select id="relation" style="height: 30px; border-radius: 3px; border: none; border: 1px solid #aaa; padding: 0 15px; font-size: 11px;">
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
                                    <input type="date" id="enrollDate" name="enrollDate" disabled>
                                </div>

                                <div class="enroll-input-fields">
                                    <label for="enrollTime">Enrollment Time</label>
                                    <input type="time" id="enrollTime" name="enrollTime" disabled>
                                </div>
                            </div>

                            <div class="enroll-fields">
                                <div class="confirmation">
                                    <label for="confirmation">
                                        We <strong>HEREBY CERTIFY</strong> that the above information is true and correct to the best of our knowledge <a href="#" id="openModal">Privacy Policy</a>.
                                    </label>
                                    <input title="confirmation" type="checkbox" id="confirmation">
                                </div>
                            </div>

                            <button title="Next" class="nextBtn" id="proceedToLoading" disabled>
                                <span class="btnText">Proceed to Loading Subjects</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>

                            <style>
                                button.nextBtn {
                                    background-color: #007bff;
                                    color: white;
                                    padding: 10px 20px;
                                    border: none;
                                    border-radius: 5px;
                                    cursor: not-allowed !important;
                                    /* Default cursor */
                                    opacity: 0.6;
                                    /* Default look for disabled */
                                    transition: opacity 0.3s ease, cursor 0.3s ease;
                                }

                                button.nextBtn.enabled {
                                    cursor: pointer !important;
                                    /* Enabled state cursor */
                                    opacity: 1;
                                    /* Normal look for enabled */
                                }

                                button.nextBtn.enabled:hover {
                                    opacity: 0.7;
                                }
                            </style>

                            <script>
                                // Select the checkbox and button elements
                                const checkbox = document.getElementById('confirmation');
                                const button = document.getElementById('proceedToLoading');

                                // Function to toggle the button state
                                function toggleButtonState() {
                                    if (checkbox.checked) {
                                        button.disabled = false; // Enable button
                                        button.classList.add('enabled'); // Add enabled class for styling
                                    } else {
                                        button.disabled = true; // Disable button
                                        button.classList.remove('enabled'); // Remove enabled class
                                    }
                                }

                                // Add an event listener to the checkbox
                                checkbox.addEventListener('change', toggleButtonState);

                                // Initial state (in case the checkbox starts unchecked)
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
        /* styles.css */

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        /* Trigger Link */
        a#openModal {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }

        a#openModal:hover {
            text-decoration: underline;
        }

        /* Modal Styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }

        /* Modal Content */
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s;
        }

        /* Close Button */
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

        /* Privacy Policy Styles */
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
        // script.js

        // Get modal and trigger elements
        const modal = document.getElementById('privacyModal');
        const openModalLink = document.getElementById('openModal');
        const closeModalBtn = document.getElementById('closeModal');

        // Open modal when clicking the link
        openModalLink.addEventListener('click', (e) => {
            e.preventDefault();
            modal.style.display = 'block';
        });

        // Close modal when clicking the close button
        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Close modal when clicking outside of the modal content
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
    <!-- MODAL FOR PRIVACY POLICY -->

    <style>
        /* Default input styles */
        .subject-code,
        .description,
        .days,
        .time,
        .room-no,
        .units {
            height: 30px;
            border-radius: 3px;
            border: 1px solid #aaa;
            padding: 0 15px;
            font-size: 11px;
            outline: none;
        }

        /* Specific widths for each column */
        .subject-code {
            width: 100px;
        }

        .description {
            width: 600px;
        }

        .days {
            width: 100px;
        }

        .time {
            width: 140px;
        }

        .room-no {
            width: 80px;
        }

        .units {
            width: 80px;
        }

        /* Media query for laptop screens (1024px to 1366px) */
        @media (min-width: 1024px) and (max-width: 1366px) {
            .subject-code {
                width: 100px;
                /* Resize subject code for laptops */
            }

            .description {
                width: 300px;
                /* Reduce description width for laptops */
            }

            .days {
                width: 100px;
                /* Resize days for laptops */
            }

            .time {
                width: 140px;
                /* Resize time for laptops */
            }

            .room-no {
                width: 80px;
                /* Resize room number for laptops */
            }

            .units {
                width: 100px;
                /* Resize units for laptops */
            }
        }

        /* Media query for smaller screens (max-width: 768px) */
        @media (max-width: 768px) {
            .subject-code {
                width: 80px;
                /* Resize subject code */
            }

            .description {
                width: 100%;
                /* Make description take full width */
            }

            .days {
                width: 80px;
                /* Resize days */
            }

            .time {
                width: 120px;
                /* Resize time */
            }

            .room-no {
                width: 100px;
                /* Resize room number */
            }

            .units {
                width: 100px;
                /* Resize units */
            }
        }

        /* Media query for very small screens (max-width: 480px) */
        @media (max-width: 480px) {
            .subject-code {
                width: 70px;
                /* Resize subject code further */
            }

            .description {
                width: 100%;
                /* Full width for description */
            }

            .days {
                width: 70px;
                /* Resize days further */
            }

            .time {
                width: 100px;
                /* Resize time further */
            }

            .room-no {
                width: 70px;
                /* Resize room number */
            }

            .units {
                width: 70px;
                /* Resize units */
            }
        }
    </style>


    <script>
        // Predefined data for subjects
        const subjectData = {
            "CS101": {
                description: "Introduction to Computer Science",
                days: "Mon-Wed-Fri",
                time: "9:00 AM - 10:30 AM",
                room_no: "101",
                units: "3"
            },
            "MATH201": {
                description: "Calculus I",
                days: "Tue-Thu",
                time: "10:00 AM - 11:30 AM",
                room_no: "102",
                units: "4"
            },
            "PHY301": {
                description: "Physics I",
                days: "Mon-Wed",
                time: "2:00 PM - 3:30 PM",
                room_no: "103",
                units: "3"
            },
            "ENG101": {
                description: "English Composition",
                days: "Mon-Wed-Fri",
                time: "8:00 AM - 9:00 AM",
                room_no: "104",
                units: "3"
            },
            "HIST202": {
                description: "World History",
                days: "Tue-Thu",
                time: "11:00 AM - 12:30 PM",
                room_no: "105",
                units: "3"
            },
            "CHEM101": {
                description: "General Chemistry",
                days: "Mon-Wed",
                time: "1:00 PM - 2:30 PM",
                room_no: "106",
                units: "4"
            },
            "BIO201": {
                description: "Biology II",
                days: "Tue-Thu",
                time: "3:00 PM - 4:30 PM",
                room_no: "107",
                units: "3"
            },
            "ECON101": {
                description: "Principles of Economics",
                days: "Mon-Wed-Fri",
                time: "10:30 AM - 11:30 AM",
                room_no: "108",
                units: "3"
            },
            "PSYCH101": {
                description: "Introduction to Psychology",
                days: "Tue-Thu",
                time: "9:00 AM - 10:30 AM",
                room_no: "109",
                units: "3"
            },
            "ART101": {
                description: "Fundamentals of Art",
                days: "Mon-Wed",
                time: "2:30 PM - 4:00 PM",
                room_no: "110",
                units: "2"
            },
            "PHIL201": {
                description: "Philosophy and Ethics",
                days: "Tue-Thu",
                time: "1:00 PM - 2:30 PM",
                room_no: "111",
                units: "3"
            },
            "STAT101": {
                description: "Introduction to Statistics",
                days: "Mon-Wed-Fri",
                time: "3:30 PM - 4:30 PM",
                room_no: "112",
                units: "3"
            },
            "BUS101": {
                description: "Introduction to Business",
                days: "Tue-Thu",
                time: "11:30 AM - 1:00 PM",
                room_no: "113",
                units: "3"
            }
        };


        // Function to add a new row to the table
        function addRow(term) {
            const table = document.querySelector(`table.${term} tbody`);
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                    <td><input type="text" class="subject-code" placeholder="Enter Subject Code" name="${term}[][subject_code]"></td>
                    <td><input type="text" class="description" readonly name="${term}[][description]"></td>
                    <td><input type="text" class="days" readonly name="${term}[][days]"></td>
                    <td><input type="text" class="time" readonly name="${term}[][time]"></td>
                    <td><input type="text" class="room-no" readonly name="${term}[][room_no]"></td>
                    <td><input type="text" class="units" readonly name="${term}[][units]"></td>
                `;
            table.appendChild(newRow);

            // Add event listener for dynamic filling
            const subjectCodeInput = newRow.querySelector('.subject-code');
            subjectCodeInput.addEventListener('input', function() {
                const subjectCode = subjectCodeInput.value.trim().toUpperCase();
                const subjectInfo = subjectData[subjectCode];

                if (subjectInfo) {
                    newRow.querySelector('.description').value = subjectInfo.description;
                    newRow.querySelector('.days').value = subjectInfo.days;
                    newRow.querySelector('.time').value = subjectInfo.time;
                    newRow.querySelector('.room-no').value = subjectInfo.room_no;
                    newRow.querySelector('.units').value = subjectInfo.units;
                } else {
                    // Clear fields if subject code is invalid
                    newRow.querySelector('.description').value = 'NO VALUE';
                    newRow.querySelector('.days').value = 'NO VALUE';
                    newRow.querySelector('.time').value = 'NO VALUE';
                    newRow.querySelector('.room-no').value = 'NO VALUE';
                    newRow.querySelector('.units').value = 'NO VALUE';
                }
            });
        }


        // ACTIVE MENU
        document.querySelectorAll('.sidebar-menu ul li a').forEach(menuItem => {
            menuItem.addEventListener('click', function() {
                // Remove active class from all menu items
                document.querySelectorAll('.sidebar-menu ul li a').forEach(item => item.classList.remove('active'));

                // Add active class to clicked menu item
                this.classList.add('active');

                // Update dashboard title based on clicked menu item
                document.getElementById('dashboard-title').textContent = this.getAttribute('data-title');
            });
        });

        // HIDE SIDEBAR MENU
        document.getElementById('toggle-menu-btn').addEventListener('click', function() {
            const sidebarMenu = document.querySelector('.sidebar-menu');
            const dashboardContainer = document.querySelector('.dashboard-container');
            const headerContainer = document.querySelector('.header-container');
            const mainContainer = document.querySelector('.main-container');

            // Toggle the 'collapsed' class on the sidebar
            sidebarMenu.classList.toggle('collapsed');

            // Toggle a class to adjust the margin of the dashboard container
            dashboardContainer.classList.toggle('collapsed');
            headerContainer.classList.toggle('collapsed');
            mainContainer.classList.toggle('collapsed');

            // Toggle icon direction
            const toggleIcon = this.querySelector('i');
            if (sidebarMenu.classList.contains('collapsed')) {
                toggleIcon.classList.replace('fa-chevron-left', 'fa-chevron-right');
            } else {
                toggleIcon.classList.replace('fa-chevron-right', 'fa-chevron-left');
            }
        });

        // Get elements
        const profileLink = document.getElementById("profile-link");
        const profileMenu = document.getElementById("profile-menu");

        // Toggle the visibility of the profile menu when the profile image is clicked
        profileLink.addEventListener("click", function(e) {
            e.preventDefault(); // Prevent default link behavior
            profileMenu.style.display = profileMenu.style.display === "block" ? "none" : "block";
        });

        // Close the profile menu if clicking anywhere outside of the menu or the profile image
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

                // Toggle icon between right caret and down caret
                const icon = this.querySelector(".dropdown-icon");
                if (dropdown.classList.contains("active")) {
                    icon.classList.remove("fa-caret-right");
                    icon.classList.add("fa-caret-down"); // Change to down caret when expanded
                } else {
                    icon.classList.remove("fa-caret-down");
                    icon.classList.add("fa-caret-right"); // Change to right caret when collapsed
                }
            });
        });
        // SIDE BAR JS

        // SELECTED MENU
        const dashboardTitle = document.getElementById("dashboard-title");

        // Function to update the title
        function updateTitle(event) {
            const newTitle = event.target.getAttribute("data-title");
            if (newTitle) {
                dashboardTitle.textContent = newTitle;
            }
        }

        // Attach click event listeners to each menu item
        document.querySelectorAll(".sidebar-menu a").forEach(item => {
            item.addEventListener("click", updateTitle);
        });
        // SELECTED MENU

        // JavaScript Function to Show/Hide Forms
        function showForm(formId) {
            // Hide all forms
            const forms = document.querySelectorAll('.form');
            forms.forEach(form => {
                form.style.display = 'none';
            });

            // Show the selected form
            const formToShow = document.getElementById(formId);
            if (formToShow) {
                formToShow.style.display = 'block';
            }
        }

        function validateEnrollForm() {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;

            if (firstName && lastName && email && phone && password) {

                showForm('loadingSubjectForm');
                return false; 
            } else {

                alert("Please fill in all the fields.");
                return false;
            }
        }

        // Make the enrollment form show by default when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            showForm('enrollForm'); // Set 'enrollForm' to show first
        });
        // JavaScript Function to Show/Hide Forms

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

        function handleCheckboxChange(selectedId) {
            const gradeLevels = ['grade7', 'grade8', 'grade9', 'grade10', 'grade11', 'grade12'];
            const tvetAndCollege = ['tvet', 'college'];

            // Deselect other checkboxes
            gradeLevels.forEach(id => {
                if (id !== selectedId) document.getElementById(id).checked = false;
            });

            tvetAndCollege.forEach(id => {
                if (id !== selectedId) document.getElementById(id).checked = false;
            });

            // Disable all select fields initially
            document.getElementById('track11').disabled = true;
            document.getElementById('track12').disabled = true;
            document.getElementById('trackTvet').disabled = true;
            document.getElementById('courseCollege').disabled = true;

            // Enable the LRN field only for grade levels
            if (gradeLevels.includes(selectedId)) {
                document.getElementById('lrn').disabled = false; // Enable LRN field
            } else {
                document.getElementById('lrn').disabled = true; // Disable LRN field
            }

            // Enable only the select field associated with the checked checkbox
            if (selectedId === 'grade11') {
                document.getElementById('track11').disabled = false;
            } else if (selectedId === 'grade12') {
                document.getElementById('track12').disabled = false;
            } else if (selectedId === 'tvet') {
                document.getElementById('trackTvet').disabled = false;
            } else if (selectedId === 'college') {
                document.getElementById('courseCollege').disabled = false;
            }

            // Generate a unique Student ID
            const studentID = generateStudentID();
            document.getElementById('studentID').value = studentID;
        }

        function generateStudentID() {
            return 'ID' + Math.floor(Math.random() * 10000).toString().padStart(4, '0');
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

        function showForm(formId) {
            document.querySelectorAll('.main-container-form form').forEach(form => {
                form.style.display = 'none';
            });
            document.getElementById(formId).style.display = 'block';
        }

        function validateEnrollmentForm() {
            const enrollForm = document.getElementById('enrollForm');
            const inputs = enrollForm.querySelectorAll('input[required], select[required]');
            for (let input of inputs) {
                if (!input.value) {
                    alert('Please fill all required fields in the enrollment form.');
                    return false;
                }
            }
            return true;
        }

        document.getElementById('proceedToLoading').addEventListener('click', function(event) {
            event.preventDefault();
            if (validateEnrollmentForm()) {
                showForm('loadingSubjectForm');
            }
        });

        function validateLoadingSubjectsForm() {
            const loadingForm = document.getElementById('loadingSubjectForm');
            const inputs = loadingForm.querySelectorAll('input[required]');
            for (let input of inputs) {
                if (!input.value) {
                    alert('Please fill all required fields in the loading subjects form.');
                    return false;
                }
            }
            return true;
        }

        document.getElementById('proceedToPayment').addEventListener('click', function(event) {
            event.preventDefault();
            if (validateLoadingSubjectsForm()) {
                showForm('paymentMethodForm');
            }
        });
    </script>

</body>

</html>