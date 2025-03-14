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

// Set a general title for the page (no file name)
$pageTitle = "Admin Dashboard - Samson Management System"; // You can modify this title as needed

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f9;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        body.dark-mode .admin-header {
            background-color: #333;
            color: #ffffff;
        }

        body.dark-mode .sidebar {
            background-color: #222;
            color: #ffffff;
        }

        body.dark-mode .card {
            background-color: #333;
            color: #ffffff;
        }

        body.dark-mode .student-table th {
            background-color: #444;
            color: #ffffff;
        }

        body.dark-mode .student-table tr:hover {
            background-color: #555;
        }

        body.dark-mode .profile-menu {
            background-color: #333;
            border-color: #444;
        }

        body.dark-mode .profile-menu ul li a {
            color: #ffffff;
        }

        body.dark-mode .profile-menu ul li a:hover {
            background-color: #444;
            color: #1abc9c;
        }

        /* HEADER MENU */
        .admin-header {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #fff;
            z-index: 1001;
            /* Higher z-index so it stays on top of the sidebar */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px 10px;
            margin-bottom: 100px;
        }

        /* Container for the header contents */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            transition: transform 0.5s;
        }

        /* Left side of the header */

        .header-left .button-toggle {
            border: none;
        }

        /* Sidebar Logo Styles */
        .sidebar {
            margin-top: 80px;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
        }

        .logo-img {
            width: 50px;
            /* Set image size to 50px */
            height: 50px;
            /* Maintain aspect ratio */
            border-radius: 50%;
            /* Optionally make the image circular */
        }

        .sidebar-titles {
            margin-top: 0;
            margin-left: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #34495e;
            /* Adjust color as needed */
        }

        .sidebar-logo i {
            font-size: 20px;
            margin-left: 10px;
            cursor: pointer;
            margin-top: 0;
            color: #34495e;
        }

        /* Additional Styling if needed */
        .sidebar-logo:hover .toggle-btn {
            color: #2c3e50;
            /* Darken the color when hovered */
        }


        .dashboard-title {
            font-size: 16px;
            /* Adjust font size for the text */
            color: white;
            /* Adjust the color as needed */
            margin: 0;
            /* Remove any default margin */
            text-align: center;
        }


        .header-right {
            margin-right: 40px;
        }

        /* Right side of the header */
        .header-right .header-value-right {
            display: flex;
            align-items: center;
            position: relative;
        }

        /* Profile image styles */
        .profile-image img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            overflow: hidden;
        }

        /* Name and access styling */
        .name-access {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-left: 10px;
        }

        .name-access p {
            margin: 0;
            font-weight: bold;
            font-size: 13px;
            /* Adjusted font size */
            color: #333;
        }

        .name-access span {
            font-size: 11px;
            padding: 0;
            color: gray;
            margin-top: 5px;
            /* Space between name and access level */
        }

        /* Profile menu styles */
        .profile-menu {
            display: none;
            /* Hidden by default */
            position: absolute;
            top: 100%;
            /* Position below the profile image */
            right: 0;
            /* Align to the right */
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            width: 200px;
            /* Adjust width as needed */
        }

        /* Show the dropdown when the parent container is hovered */

        /* List and links in the dropdown */
        .profile-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .profile-menu ul li {
            padding: 3px;
            display: flex;
            align-items: center;
            font-size: 12px;
            color: #333;
        }

        /* Styling for icons */
        .profile-menu ul li a i {
            margin-right: 10px;
            margin-left: 10px;
            font-size: 16px;
            color: #333;
        }

        /* Links inside the menu */
        .profile-menu ul li a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            padding: 8px 0;
            transition: background-color 0.3s ease, color 0.3s ease;
            width: 100%;
        }

        /* Hover effect */
        .profile-menu ul li a:hover {
            background-color: #f4f4f4;
            color: #007bff;
        }



        /* HEADER MENU */

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 30px 0 20px 10px;

        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li a:hover {
            color: #1abc9c;
        }

        /* Main Content */
        .main-content {
            margin-top: 80px;
            margin-left: 250px;
            flex: 1;
            padding: 20px;
            display: flex;
            gap: 20px;
        }

        .left-content {
            flex: 3;
        }

        .right-content {
            flex: 1.3;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #2c3e50;
        }

        /* Overview Cards */
        .overview-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            font-size: 15px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .card p {
            font-size: 20px;
            font-weight: bold;
            color: #1abc9c;
        }

        /* Recent Activities */
        .recent-activities {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .recent-activities h2 {
            font-size: 15px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .recent-activities ul {
            list-style: none;
        }

        .recent-activities ul li {
            padding: 10px 0;
            font-size: 12px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .recent-activities ul li:last-child {
            border-bottom: none;
        }

        .recent-activities ul li span {
            font-size: 12px;
            color: #888;
        }

        /* Charts */
        .charts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chart h2 {
            font-size: 15px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        /* Table */
        .student-table {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .student-table h2 {
            font-size: 15px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .student-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-table th,
        .student-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .student-table th {
            background-color: #2c3e50;
            color: #fff;
        }

        .student-table tr:hover {
            background-color: #f9f9f9;
        }

        /* Calendar Section */
        .calendar {
            margin-bottom: 30px;
        }

        .calendar h2 {
            font-size: 15px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .calendar .current-date {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1abc9c;
        }

        .calendar .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar .calendar-grid div {
            padding: 10px;
            text-align: center;
            border: 1px solid #eee;
            border-radius: 5px;
            cursor: pointer;
        }

        .calendar .calendar-grid div.event {
            background-color: #1abc9c;
            color: #fff;
        }

        .calendar .calendar-grid div.today {
            background-color: #2c3e50;
            color: #fff;
        }

        .calendar .notification {
            margin-top: 10px;
            font-size: 12px;
            color: #e74c3c;
        }

        /* School Calendar */
        .calendar-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: 0 auto;
        }

        .school-calendar-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .college-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }

        .college-name {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }

        .college-address {
            font-size: 11px;
            color: #666;
        }

        .department-title {
            font-size: 13px;
            font-weight: bold;
            color: #1abc9c;
            margin-top: 10px;
        }

        .semester-details {
            font-size: 12px;
            color: #888;
        }

        .calendar-title {
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .calendar-table th,
        .calendar-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 11px;
            text-align: center;
        }

        .calendar-table th {
            background-color: #2c3e50;
            text-align: center;
            color: #fff;
            font-size: 13px;
        }

        .calendar-table tbody {
            font-size: 15px;
        }

        .calendar-table tr:nth-child(even) {
            background-color: #f9f9f9;

        }

        .calendar-table tr:hover {
            background-color: #f1f1f1;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .navigation-buttons button {
            padding: 10px 20px;
            font-size: 13px;
            border: none;
            border-radius: 5px;
            background-color: #1abc9c;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .navigation-buttons button:hover {
            background-color: #16a085;
        }

        .navigation-buttons button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .sidebar {
                width: 200px;
                /* Reduce sidebar width */
            }

            .main-content {
                margin-left: 200px;
                /* Adjust main content margin */
            }

            .overview-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                /* Adjust card grid */
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 60px;
                /* Collapse sidebar to icons only */
                padding: 10px;
            }

            .sidebar h2 {
                display: none;
                /* Hide sidebar title */
            }

            .sidebar ul li a span {
                display: none;
                /* Hide sidebar text */
            }

            .sidebar ul li a i {
                margin-right: 0;
                /* Center icons */
            }

            .main-content {
                margin-left: 60px;
                /* Adjust main content margin */
            }

            .header-container {
                flex-direction: column;
                /* Stack header content vertically */
                align-items: flex-start;
            }

            .header-right {
                margin-top: 10px;
                /* Add spacing for stacked header */
            }

            .overview-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                /* Adjust card grid */
            }

            .charts {
                grid-template-columns: 1fr;
                /* Stack charts vertically */
            }

            .student-table table {
                display: block;
                overflow-x: auto;
                /* Add horizontal scroll for tables */
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                /* Hide sidebar off-screen */
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
                /* Show sidebar when active */
            }

            .main-content {
                margin-left: 0;
                /* Remove sidebar margin */
            }

            .header-container {
                padding: 10px;
                /* Reduce header padding */
            }

            .header h1 {
                font-size: 16px;
                /* Reduce header font size */
            }

            .overview-cards {
                grid-template-columns: 1fr;
                /* Stack cards vertically */
            }

            .calendar-container {
                padding: 10px;
                /* Reduce calendar padding */
            }

            .calendar-table th,
            .calendar-table td {
                padding: 8px;
                /* Reduce table padding */
            }
        }

        @media (max-width: 576px) {
            .header h1 {
                font-size: 14px;
                /* Further reduce header font size */
            }

            .card h3 {
                font-size: 13px;
                /* Reduce card title font size */
            }

            .card p {
                font-size: 18px;
                /* Reduce card value font size */
            }

            .recent-activities h2,
            .student-table h2,
            .calendar h2 {
                font-size: 14px;
                /* Reduce section title font size */
            }

            .calendar-grid {
                grid-template-columns: repeat(5, 1fr);
                /* Adjust calendar grid for small screens */
            }

            .navigation-buttons button {
                padding: 8px 16px;
                /* Reduce button padding */
                font-size: 12px;
                /* Reduce button font size */
            }
        }
    </style>
</head>

<body>

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
                        <a href="javascript:void(0);" id="profile-link" onclick="toggleProfileMenu()">
                            <?php
                            if (isset($_SESSION['username'])) {
                                $email = $_SESSION['username'];
                                $query = mysqli_query($conn, "SELECT * FROM admin_user WHERE username='$email'");

                                if ($row = mysqli_fetch_assoc($query)) {
                                    echo !empty($row['profile'])
                                        ? '<img src="' . htmlspecialchars($row['profile']) . '" alt="Profile Image">'
                                        : '<img src="./uploads/default-profile.jpg" alt="Default Profile Image">';
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
                                echo !empty($row['access']) ? "<span>" . htmlspecialchars($row['access']) . "</span>" : "";
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
                            <li><a href="javascript:void(0);" onclick="toggleDarkMode()"><i class="fas fa-moon"></i> Dark Mode</a></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- HEADER -->

    <script>
        function toggleProfileMenu() {
            var profileMenu = document.getElementById("profile-menu");
            profileMenu.style.display = (profileMenu.style.display === "block") ? "none" : "block";
        }

        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
        }

        // Load saved theme preference
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }

        // Close the profile menu when clicking outside
        window.onclick = function(event) {
            var profileMenu = document.getElementById("profile-menu");
            var profileLink = document.getElementById("profile-link");

            if (!profileLink.contains(event.target) && !profileMenu.contains(event.target)) {
                profileMenu.style.display = "none";
            }
        };
    </script>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="#dashboard" data-title="Dashboard" class="active"><i class="fa-solid fa-house"></i>
                    <span>Dashboard</span></a></li>
            <li><a href="./enroll-student.php" data-title="Enroll a Student"><i class="fa-solid fa-user-plus"></i>
                    <span>Enroll a Student</span></a></li>
            <li><a href="./department.php" data-title="Department"><i class="fa-solid fa-building"></i>
                    <span>Department</span></a></li>
            <li><a href="#course" data-title="Course"><i class="fa-solid fa-book"></i> <span>Course</span></a></li>
            <li><a href="./add-subjects.php" data-title="Subjects"><i class="fa-solid fa-book-open"></i>
                    <span>Subjects</span></a></li>
            <li><a href="#payment-management" data-title="Payment Management"><i class="fa-solid fa-credit-card"></i>
                    <span>Payment Management</span></a></li>
            <li><a href="#grading-system" data-title="Grading System"><i class="fa-solid fa-graduation-cap"></i>
                    <span>Grading System</span></a></li>
            <li><a href="#student-attendance" data-title="Student Attendance"><i class="fa-solid fa-calendar-check"></i>
                    <span>Student Attendance</span></a></li>
            <li><a href="./announcement.php" data-title="Announcement"><i class="fa-solid fa-bullhorn"></i>
                    <span>Announcement</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Left Content -->
        <div class="left-content">
            <!-- Header -->
            <div class="header">
                <h1>Student Management System</h1>
            </div>

            <!-- Overview Cards -->
            <div class="overview-cards">
                <div class="card">
                    <h3>Total Students</h3>
                    <p>1,250</p>
                </div>
                <div class="card">
                    <h3>Total Teachers</h3>
                    <p>1,000</p>
                </div>
                <div class="card">
                    <h3>Total Courses</h3>
                    <p>25</p>
                </div>
                <div class="card">
                    <h3>Total Department</h3>
                    <p>5</p>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="recent-activities">
                <h2>Recent Activities</h2>
                <ul>
                    <li>New student enrolled <span>2 hours ago</span></li>
                    <li>Course updated <span>5 hours ago</span></li>
                    <li>Payment received <span>1 day ago</span></li>
                    <li>New course added <span>2 days ago</span></li>
                </ul>
            </div>

            <!-- Charts -->
            <div class="charts">
                <div class="chart">
                    <h2>Student Enrollment Trend</h2>
                    <canvas id="enrollmentChart"></canvas>
                </div>
                <div class="chart">
                    <h2>Course Popularity</h2>
                    <canvas id="courseChart"></canvas>
                </div>
            </div>

            <!-- Student Table -->
            <div class="student-table">
                <h2>Student List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>john@example.com</td>
                            <td>Computer Science</td>
                            <td>Active</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>jane@example.com</td>
                            <td>Business Administration</td>
                            <td>Active</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Alice Johnson</td>
                            <td>alice@example.com</td>
                            <td>Engineering</td>
                            <td>Inactive</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Content -->
        <div class="right-content">
            <!-- Calendar -->
            <div class="calendar">
                <h2>Calendar</h2>
                <div class="current-date" id="currentDate"></div>
                <div class="calendar-grid" id="calendarGrid"></div>
                <div class="notification" id="calendarNotification"></div>
            </div>

            <!-- School Calendar -->
            <div class="calendar-container">
                <!-- School Calendar Header -->
                <div class="school-calendar-header">
                    <img src="./image/apple-touch-icon.png" alt="College Logo" class="college-logo">
                    <h1 class="college-name">SAMSON POLYTECHNIC COLLEGE of DAVAO</h1>
                    <p class="college-address">R. Magsaysay Avenue, 8000 Davao City</p>
                    <h2 class="department-title" id="departmentTitle">COLLEGE DEPARTMENT</h2>
                    <p class="semester-details">First Semester, Academic Year 2024-2025</p>
                </div>

                <!-- School Calendar -->
                <div class="school-calendar">
                    <h2 class="calendar-title">SCHOOL CALENDAR</h2>
                    <table class="calendar-table" id="calendarTable">
                        <thead>
                            <tr>
                                <th>Date & Day</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Calendar data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Navigation Buttons -->
                <div class="navigation-buttons">
                    <button id="prevButton" disabled>Previous</button>
                    <button id="nextButton">Next</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // School Calendar Data for Each Department
        const calendars = {
            college: [{
                    date: "August 19, 2024, Monday",
                    activity: "Start of 1<sup>st</sup> Term Classes"
                },
                {
                    date: "September 6-7, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "September 20-21, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "October 4-5, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "October 25-26, 2024, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "October 28 - Nov. 2, 2024",
                    activity: "1<sup>st</sup> Term Break"
                },
                {
                    date: "November 4, 2024, Monday",
                    activity: "Start of 2<sup>nd</sup> Term Classes"
                },
                {
                    date: "November 22-23, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "December 6-7, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "December 20-21, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "December 23, 2024, Monday until January 4, 2025, Saturday",
                    activity: "Christmas Break"
                },
                {
                    date: "January 6, 2025, Monday",
                    activity: "Back to School"
                },
                {
                    date: "January 17-18, 2025, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "January 20-25, 2025",
                    activity: "Semestral Break/Enrollment for 2<sup>nd</sup> Semester"
                },
                {
                    date: "January 27, 2025, Monday",
                    activity: "2<sup>nd</sup> Semester Opening of Classes"
                },
            ],
            tvet: [{
                    date: "August 19, 2024, Monday",
                    activity: "Start of 1<sup>st</sup> Term Classes"
                },
                {
                    date: "September 6-7, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "September 20-21, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "October 4-5, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "October 25-26, 2024, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "October 28 - Nov. 2, 2024",
                    activity: "1<sup>st</sup> Term Break"
                },
                {
                    date: "November 4, 2024, Monday",
                    activity: "Start of 2<sup>nd</sup> Term Classes"
                },
                {
                    date: "November 22-23, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "December 6-7, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "December 20-21, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "December 23, 2024, Monday until January 4, 2025, Saturday",
                    activity: "Christmas Break"
                },
                {
                    date: "January 6, 2025, Monday",
                    activity: "Back to School"
                },
                {
                    date: "January 17-18, 2025, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "January 20-25, 2025",
                    activity: "Semestral Break/Enrollment for 2<sup>nd</sup> Semester"
                },
                {
                    date: "January 27, 2025, Monday",
                    activity: "2<sup>nd</sup> Semester Opening of Classes"
                },
            ],
            shs: [{
                    date: "August 19, 2024, Monday",
                    activity: "Start of 1<sup>st</sup> Term Classes"
                },
                {
                    date: "September 6-7, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "September 20-21, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "October 4-5, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "October 25-26, 2024, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "October 28 - Nov. 2, 2024",
                    activity: "1<sup>st</sup> Term Break"
                },
                {
                    date: "November 4, 2024, Monday",
                    activity: "Start of 2<sup>nd</sup> Term Classes"
                },
                {
                    date: "November 22-23, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "December 6-7, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "December 20-21, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "December 23, 2024, Monday until January 4, 2025, Saturday",
                    activity: "Christmas Break"
                },
                {
                    date: "January 6, 2025, Monday",
                    activity: "Back to School"
                },
                {
                    date: "January 17-18, 2025, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "January 20-25, 2025",
                    activity: "Semestral Break/Enrollment for 2<sup>nd</sup> Semester"
                },
                {
                    date: "January 27, 2025, Monday",
                    activity: "2<sup>nd</sup> Semester Opening of Classes"
                },
            ],
            jhs: [{
                    date: "August 19, 2024, Monday",
                    activity: "Start of 1<sup>st</sup> Term Classes"
                },
                {
                    date: "September 6-7, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "September 20-21, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "October 4-5, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "October 25-26, 2024, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "October 28 - Nov. 2, 2024",
                    activity: "1<sup>st</sup> Term Break"
                },
                {
                    date: "November 4, 2024, Monday",
                    activity: "Start of 2<sup>nd</sup> Term Classes"
                },
                {
                    date: "November 22-23, 2024, Fri-Sat",
                    activity: "PRELIM Examination"
                },
                {
                    date: "December 6-7, 2024, Fri-Sat",
                    activity: "MIDTERM Examination"
                },
                {
                    date: "December 20-21, 2024, Fri-Sat",
                    activity: "SEMI-FINAL Examination"
                },
                {
                    date: "December 23, 2024, Monday until January 4, 2025, Saturday",
                    activity: "Christmas Break"
                },
                {
                    date: "January 6, 2025, Monday",
                    activity: "Back to School"
                },
                {
                    date: "January 17-18, 2025, Fri-Sat",
                    activity: "FINAL Examination"
                },
                {
                    date: "January 20-25, 2025",
                    activity: "Semestral Break/Enrollment for 2<sup>nd</sup> Semester"
                },
                {
                    date: "January 27, 2025, Monday",
                    activity: "2<sup>nd</sup> Semester Opening of Classes"
                },
            ],
        };

        const departmentTitles = {
            college: "COLLEGE DEPARTMENT",
            tvet: "TVET DEPARTMENT",
            shs: "SENIOR HIGH SCHOOL DEPARTMENT",
            jhs: "JUNIOR HIGH SCHOOL DEPARTMENT",
        };

        // Navigation Logic
        const departments = Object.keys(calendars);
        let currentIndex = 0;

        const departmentTitle = document.getElementById("departmentTitle");
        const calendarTable = document.getElementById("calendarTable").getElementsByTagName("tbody")[0];
        const prevButton = document.getElementById("prevButton");
        const nextButton = document.getElementById("nextButton");

        // Function to Load Calendar Data
        function loadCalendar(department) {
            // Update Department Title
            departmentTitle.textContent = departmentTitles[department];

            // Clear Existing Table Rows
            calendarTable.innerHTML = "";

            // Populate Table with Calendar Data
            calendars[department].forEach((event) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${event.date}</td>
                    <td>${event.activity}</td>
                `;
                calendarTable.appendChild(row);
            });
        }

        // Initial Load
        loadCalendar(departments[currentIndex]);

        // Navigation Buttons
        prevButton.addEventListener("click", () => {
            if (currentIndex > 0) {
                currentIndex--;
                loadCalendar(departments[currentIndex]);
                nextButton.disabled = false;
            }
            if (currentIndex === 0) {
                prevButton.disabled = true;
            }
        });

        nextButton.addEventListener("click", () => {
            if (currentIndex < departments.length - 1) {
                currentIndex++;
                loadCalendar(departments[currentIndex]);
                prevButton.disabled = false;
            }
            if (currentIndex === departments.length - 1) {
                nextButton.disabled = true;
            }
        });
    </script>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
        const enrollmentChart = new Chart(enrollmentCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Enrollments',
                    data: [100, 150, 200, 250, 300, 350],
                    borderColor: '#1abc9c',
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Course Popularity Chart
        const courseCtx = document.getElementById('courseChart').getContext('2d');
        const courseChart = new Chart(courseCtx, {
            type: 'bar',
            data: {
                labels: ['CS101', 'BA202', 'ENG303', 'MATH404'],
                datasets: [{
                    label: 'Enrollments',
                    data: [120, 90, 80, 60],
                    backgroundColor: '#2c3e50',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Calendar Logic
        const currentDate = document.getElementById('currentDate');
        const calendarGrid = document.getElementById('calendarGrid');
        const calendarNotification = document.getElementById('calendarNotification');

        const today = new Date();
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();

        const events = {
            '2024-05-15': 'Final Exams',
            '2024-05-20': 'Graduation Day',
        };

        // Display Current Date
        currentDate.textContent = today.toDateString();

        // Generate Calendar Grid
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay();

        let calendarHTML = '';

        // Fill empty cells for the first week
        for (let i = 0; i < firstDayOfMonth; i++) {
            calendarHTML += '<div></div>';
        }

        // Fill days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = day === today.getDate() && currentMonth === today.getMonth();
            const hasEvent = events[date];

            calendarHTML += `
                <div class="${isToday ? 'today' : ''} ${hasEvent ? 'event' : ''}" data-date="${date}">
                    ${day}
                </div>
            `;
        }

        calendarGrid.innerHTML = calendarHTML;

        // Add Event Listeners for Calendar Days
        calendarGrid.querySelectorAll('div').forEach(day => {
            day.addEventListener('click', () => {
                const date = day.getAttribute('data-date');
                if (events[date]) {
                    calendarNotification.textContent = `Event: ${events[date]}`;
                } else {
                    calendarNotification.textContent = 'No events for this date.';
                }
            });
        });
    </script>

    <script>
        // Toggle Sidebar on Smaller Screens
        const toggleMenuBtn = document.getElementById('toggle-menu-btn');
        const sidebar = document.querySelector('.sidebar');

        toggleMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    </script>
</body>

</html>