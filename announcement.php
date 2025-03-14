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
        .header-value-right:hover .profile-menu {
            display: block;
        }

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
            align-items: flex-start;
            /* Prevent stretching */
            flex-wrap: wrap;
            /* Allow wrapping if needed */
            gap: 20px;
        }

        .left-content {
            flex: 3;
            height: auto !important;
            min-height: fit-content;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .sub-container {
            display: flex;
            flex-direction: column;
            /* Stack elements vertically */
            gap: 10px;
            /* Spacing between top and bottom sections */
            flex: 1.6;
            /* Adjust height as needed */
        }

        .right-content {
            /* Takes available space */
            height: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .right-content-bottom {
            height: auto;
            /* Takes available space */
            background-color: #fff;
            /* Different color for visual separation */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            background-color: #f8f9fa;
            /* Light gray background */
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
        }

        .list-group a {
            display: flex;
            font-size: 13px;
            font-weight: 600;
            align-items: center;
            text-decoration: none;
            color: #333;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .list-group a i {
            font-size: 15px;
            color: #2c3e50;
            /* Blue icon */
            margin-right: 10px;
        }

        .list-group a:hover {
            background-color: #2c3e50;
            /* Blue hover */
            color: #fff;
        }

        .list-group a:hover i {
            color: #fff;
        }


        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0;
            background-color: #2c3e50;
            padding: 15px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 15px;
            color: #fff;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        select,
        input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input {
            width: 200px;
        }

        .add-btn {
            color: #2c3e50;
            background-color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            transition: all ease 0.3s;
        }

        .add-btn:hover {
            background-color: #1abc9c;
            color: #fff;
        }

        /* Modal styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            width: 700px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Modal header */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .modal-header h2 {
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        /* Form fields */
        .form-group {
            margin-top: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group textarea {
            height: 80px;
        }

        .form-group1 {
            margin-top: 10px;
            font-size: 13px;
            font-weight: 500;
        }

        .form-group1 input {
            padding: 0;
        }

        .form-row {
            display: flex;
            gap: 10px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Buttons */
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .cancel-btn {
            background: gray;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .create-btn {
            background: #007bff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel-btn:hover {
            background: darkgray;
        }

        .create-btn:hover {
            background: #0056b3;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            /* Aligns items vertically */
            gap: 15px;
            /* Adds spacing between each row */
            padding: 20px;
            background: #fff;
            border-radius: 10px;
        }

        .sub-body {
            display: flex;
            justify-content: space-between;
            /* Ensures equal spacing */
            align-items: center;
            /* Aligns items vertically */
            padding: 10px 15px;
            border-radius: 8px;
        }

        .sub-body span {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            /* Grey text */
        }

        .view-btn {
            width: 100px;
            height: 35px;
            color: #fff;
            background-color: #2c3e50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            transition: all ease 0.3s;
        }

        .view-btn:hover {
            background-color: #34495e;
        }

        .announcement-container {
            margin-top: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .announcement-container h2 {
            margin-bottom: 10px;
            font-size: 14px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background: #2c3e50;
            color: #fff;
            font-weight: bold;
        }

        td {
            background: #f9f9f9;
            word-wrap: break-word;
            max-width: 150px;
            /* Prevents overflow */
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
        }

        .views-btn {
            background-color: #2c3e50;
            color: white;
        }

        .edit-btn {
            background-color: #3498db;
            color: white;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .search-results h2 {
            margin: 10px 0 10px 15px;
            font-size: 16px;
            color: #333;
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
            <li><a href="./dashboard.php" data-title="Dashboard" class="active"><i class="fa-solid fa-house"></i>
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
                <button class="add-btn" onclick="openModal()">+ Add Announcement</button>
            </div>

            <!-- Filters Section -->
            <div class="filters">
                <div class="filter-group">
                    <label for="department">Department</label>
                    <select id="department">
                        <option>All Departments</option>
                        <option>IT</option>
                        <option>Business</option>
                        <option>Engineering</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="category">Category</label>
                    <select id="category">
                        <option>All Categories</option>
                        <option>General</option>
                        <option>Exams</option>
                        <option>Events</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" placeholder="Search announcements..." onkeyup="filterAnnouncements()">
                </div>
            </div>

            <div class="search-results" id="search-results">
                <h2>Search Results</h2>
                <div id="announcement-display">
                    <!-- Searched/Filtered Announcements will be displayed here -->
                </div>
            </div>

            <!-- Announcements Table -->
            <div class="announcement-container">
                <h2>Announcements</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Category</th>
                            <th>Content</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Attachments</th>
                            <th>Urgent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="announcement-table-body">
                        <!-- Dynamic rows will be added here -->
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            // Dummy Data (Replace with actual input data from the form)
            const announcements = [{
                    title: "Exam Schedule Released",
                    department: "IT",
                    category: "Exams",
                    content: "Final exams schedule for this semester is now available.",
                    startDate: "2025-02-09",
                    endDate: "2025-02-15",
                    attachment: "exam_schedule.pdf",
                    urgent: true
                },
                {
                    title: "New Enrollment Process",
                    department: "Business",
                    category: "General",
                    content: "Enrollment guidelines for the next semester.",
                    startDate: "2025-02-08",
                    endDate: "2025-02-20",
                    attachment: "enrollment_guide.docx",
                    urgent: false
                },
                {
                    title: "New Enrollment Process",
                    department: "Business",
                    category: "General",
                    content: "Enrollment guidelines for the next semester.",
                    startDate: "2025-02-08",
                    endDate: "2025-02-20",
                    attachment: "enrollment_guide.docx",
                    urgent: false
                },
                {
                    title: "New Enrollment Process",
                    department: "Business",
                    category: "General",
                    content: "Enrollment guidelines for the next semester.",
                    startDate: "2025-02-08",
                    endDate: "2025-02-20",
                    attachment: "enrollment_guide.docx",
                    urgent: false
                },
                {
                    title: "New Enrollment Process",
                    department: "Business",
                    category: "General",
                    content: "Enrollment guidelines for the next semester.",
                    startDate: "2025-02-08",
                    endDate: "2025-02-20",
                    attachment: "enrollment_guide.docx",
                    urgent: false
                }
            ];

            // Function to truncate content to 5 words
            function truncateContent(content, wordLimit = 5) {
                const words = content.split(" ");
                return words.length > wordLimit ? words.slice(0, wordLimit).join(" ") + "..." : content;
            }

            // Function to render announcements
            function renderAnnouncements() {
                const tableBody = document.getElementById("announcement-table-body");
                tableBody.innerHTML = ""; // Clear table before rendering

                announcements.forEach((announcement, index) => {
                    const row = `
                                <tr>
                                    <td>${announcement.title}</td>
                                    <td>${announcement.department}</td>
                                    <td>${announcement.category}</td>
                                    <td title="${announcement.content}">${truncateContent(announcement.content)}</td>
                                    <td>${announcement.startDate}</td>
                                    <td>${announcement.endDate}</td>
                                    <td><a href="#">${announcement.attachment}</a></td>
                                    <td>${announcement.urgent ? "✅" : "❌"}</td>
                                    <td>
                                        <button class="action-btn views-btn" onclick="viewAnnouncement(${index})">View</button>
                                        <button class="action-btn edit-btn" onclick="editAnnouncement(${index})">Edit</button>
                                        <button class="action-btn delete-btn" onclick="deleteAnnouncement(${index})">Delete</button>
                                    </td>
                                </tr>
                            `;
                    tableBody.innerHTML += row;
                });
            }
            // Function to delete an announcement
            function deleteAnnouncement(index) {
                announcements.splice(index, 1);
                renderAnnouncements();
            }

            // Function to edit an announcement (Placeholder)
            function editAnnouncement(index) {
                alert(`Edit Announcement: ${announcements[index].title}`);
            }

            // Initial render
            renderAnnouncements();
        </script>

        <div class="sub-container">
            <!-- Right Content Top -->
            <div class="right-content">
                <div class="header">
                    <h1>Statistics</h1>
                </div>
                <div class="card-body">
                    <div class="sub-body">
                        <span>Total Announcements:</span>
                        <strong>0</strong>
                        <button class="view-btn">View</button>
                    </div>
                    <div class="sub-body">
                        <span>Active Announcements:</span>
                        <strong>0</strong>
                        <button class="view-btn">View</button>
                    </div>
                    <div class="sub-body">
                        <span>Scheduled:</span>
                        <strong>0</strong>
                        <button class="view-btn">View</button>
                    </div>
                </div>
            </div>

            <!-- Right Content Bottom -->
            <div class="right-content-bottom">
                <div class="header">
                    <h1>Quick Tools</h1>
                </div>

                <div class="card-body">
                    <div class="list-group">
                        <a href="#">
                            <i class="fas fa-download me-2"></i> Export Announcements
                        </a>
                        <a href="#">
                            <i class="fas fa-print me-2"></i> Print Announcements
                        </a>
                        <a href="#">
                            <i class="fas fa-archive me-2"></i> Archive Old Announcements
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Structure -->
    <div id="announcementModal" class="modal">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2>Create Announcement</h2>
                <button class="close-btn" onclick="closeModal()">✖</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" placeholder="Enter title">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Department</label>
                        <select>
                            <option>Select Department</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select>
                            <option>Select Category</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Content</label>
                    <textarea placeholder="Enter content"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="datetime-local">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="datetime-local">
                    </div>
                </div>

                <div class="form-group">
                    <label>Attachments</label>
                    <input type="file">
                </div>

                <div class="form-group1">
                    <input type="checkbox" id="urgent">
                    <label for="urgent">Mark as Urgent</label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button class="create-btn">Create Announcement</button>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("announcementModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("announcementModal").style.display = "none";
        }

        // Close modal if clicking outside of it
        window.onclick = function(event) {
            let modal = document.getElementById("announcementModal");
            if (event.target === modal) {
                closeModal();
            }
        };
    </script>

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