<?php
// Start session and include database connection
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

// Fetch data from the database
$sql = "SELECT student_id, profile, CONCAT(first_name, ' ', middle_name, ' ', last_name) AS full_name, 
               address, email, phone, course AS course, course_level AS year, session AS semester, 'Active' AS status 
        FROM college_students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TVET Department</title>
    <link rel="stylesheet" href="tvet_dep.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./image/apple-touch-icon.png">
</head>

<body>
    <!-- SIDEBAR MENU -->
    <div class="sidebar-menu">
        <div class="sidebar-logo-container">
            <h3 id="dashboard-title" class="sidebar-title" style="padding-top: 20px; padding-left: 40px;">Department</h3>
        </div>
        <ul class="nav-list">
            <li><a href="#dashboard" data-title="Dashboard"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a></li>
            <li><a href="#enroll-student" data-title="Enroll a Student"><i class="fa-solid fa-user-plus"></i> <span>Enroll a Student</span></a></li>
            <li><a href="./test.php" data-title="Department" class="active"><i class="fa-solid fa-building"></i> <span>Department</span></a></li>
            <li><a href="#course" data-title="Course"><i class="fa-solid fa-book"></i> <span>Course</span></a></li>
            <li><a href="#course" data-title="Subjects"><i class="fa-solid fa-book-open"></i> <span>Subjects</span></a></li>
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
    <di v class="main-container">
        <div class="sub-main-container">
            <div class="enroll-options">
                <button class="enroll-btn">
                    <a href="#" onclick="setActive(this)">
                        College Department
                    </a>
                </button>
                <button class="enroll-btn">
                    <a href="#" onclick="setActive(this)">
                        TVET Department
                    </a>
                </button>
                <button class="enroll-btn">
                    <a href="#" onclick="setActive(this)">
                        Junior High School Department
                    </a>
                </button>
                <button class="enroll-btn">
                    <a href="#" onclick="setActive(this)">
                        Senior High School Department
                    </a>
                </button>
            </div>

        </div>


        <div class="main-container-header">
            <div class="sub-main-container-header start">
                <i class="fa-solid fa-bars-progress"></i> <span id="department-name">Department</span>
            </div>
        </div>

        <div class="main-container-form">

            <div class="main-crud-container">
                <header>
                    <div class="filterEntries">
                        <div class="entries">
                            Show
                            <select title="show" name="" id="table_size">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select> entries
                        </div>

                        <div class="filter">
                            <label for="search">Search:</label>
                            <input type="search" name="" id="search" placeholder="Enter student ID or full name">
                        </div>
                    </div>
                </header>

                <!-- Horizontal scroll wrapper -->
                <div class="table-scroll">
                    <table class="table-class">
                        <thead>
                            <tr class="heading">
                                <th>Student ID</th>
                                <th>Profile</th>
                                <th>Full Name</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Display each student record
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                                    echo "<td><img class='profile-img' src='data:image/jpeg;base64," . base64_encode($row['profile']) . "' alt='Profile'></td>";
                                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['year']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['semester']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td>
                                                <button class='view-btn' data-id='" . htmlspecialchars($row['student_id']) . "' 
                                                        data-name='" . htmlspecialchars($row['full_name']) . "' 
                                                        data-email='" . htmlspecialchars($row['email']) . "' 
                                                        data-phone='" . htmlspecialchars($row['phone']) . "' 
                                                        data-address='" . htmlspecialchars($row['address']) . "' 
                                                        data-course='" . htmlspecialchars($row['course']) . "' 
                                                        data-year='" . htmlspecialchars($row['year']) . "' 
                                                        data-semester='" . htmlspecialchars($row['semester']) . "' 
                                                        data-status='" . htmlspecialchars($row['status']) . "' 
                                                        data-profile='" . base64_encode($row['profile']) . "'>
                                                    <i class='fa-regular fa-eye'></i>
                                                </button>
                                                <button title='Edit'><i class='fa-regular fa-pen-to-square'></i></button>
                                                <button title='Delete'><i class='fa-regular fa-trash-can'></i></button>
                                            </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11'>No data available</td></tr>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>


                <!-- Modal Structure -->
                <div id="viewProfileModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-footer">
                            <h4 class="modal-title">Student Profile</h4>
                            <i class="fa-solid fa-xmark modal-close" style="cursor: pointer;"></i>
                        </div>
                        <div id="profileDetails">
                            <!-- Profile details will be loaded here via JavaScript -->
                        </div>
                    </div>
                </div>



                <footer>
                    <span>Showing 1 to 10 of 50 entries</span>
                    <div class="pagination">
                        <button title="prev" id="prev-btn">Prev</button>
                        <button class="active" id="page-1">1</button>
                        <button id="page-2">2</button>
                        <button id="page-3">3</button>
                        <button id="page-4">4</button>
                        <button id="page-5">5</button>
                        <button title="next" id="next-btn">Next</button>
                    </div>
                </footer>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Get modal and close icon
                    const modal = document.getElementById('viewProfileModal');
                    const closeButton = document.querySelector('.modal-close');

                    // Add event listener for "View" button click
                    const viewButtons = document.querySelectorAll('.view-btn');

                    viewButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const studentId = this.getAttribute('data-id');
                            const fullName = this.getAttribute('data-name');
                            const email = this.getAttribute('data-email');
                            const phone = this.getAttribute('data-phone');
                            const address = this.getAttribute('data-address');
                            const course = this.getAttribute('data-course');
                            const year = this.getAttribute('data-year');
                            const semester = this.getAttribute('data-semester');
                            const status = this.getAttribute('data-status');
                            const profileBase64 = this.getAttribute('data-profile');

                            // Set modal content
                            const profileDetails = document.getElementById('profileDetails');
                            profileDetails.innerHTML = `
                                                        <div><strong>Full Name:</strong> ${fullName}</div>
                                                        <div><strong>Email:</strong> ${email}</div>
                                                        <div><strong>Phone:</strong> ${phone}</div>
                                                        <div><strong>Address:</strong> ${address}</div>
                                                        <div><strong>Course:</strong> ${course}</div>
                                                        <div><strong>Year:</strong> ${year}</div>
                                                        <div><strong>Semester:</strong> ${semester}</div>
                                                        <div><strong>Status:</strong> ${status}</div>
                                                        <div><strong>Profile:</strong><br><img src="data:image/jpeg;base64,${profileBase64}" alt="Profile Image" class="profile-img"></div>
                                                    `;

                            // Show modal
                            modal.style.display = 'block';
                        });
                    });

                    // Close modal when the close icon is clicked
                    closeButton.addEventListener('click', function() {
                        modal.style.display = 'none';
                    });

                    // Close modal when clicking outside of the modal content
                    window.addEventListener('click', function(event) {
                        if (event.target === modal) {
                            modal.style.display = 'none';
                        }
                    });
                });
            </script>

            <style>
                /* Modal styles */
                .modal {
                    display: none;
                    position: fixed;
                    z-index: 1;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.4);
                }

                .modal-content {
                    background-color: #fefefe;
                    margin: 15% auto;
                    padding: 20px;
                    border: 1px solid #888;
                    max-width: 1200px;
                    /* Adjust width as needed */
                }

                .modal-footer {
                    display: flex;
                    justify-content: space-between;
                    /* Align title and close icon */
                    align-items: center;
                    padding: 10px;
                }

                .modal-title {
                    margin: 0;
                }

                .modal-close {
                    font-size: 24px;
                    /* Adjust size of the close icon */
                    color: #ff4d4d;
                    cursor: pointer;
                }

                .modal-close:hover {
                    color: #ff1a1a;
                }

                .profile-img {
                    width: 100px;
                    /* Adjust the profile image size as needed */
                    height: 100px;
                    border-radius: 50%;
                    object-fit: cover;
                }
            </style>


            <script>
                let currentPage = 1;
                let rowsPerPage = 5; // Default rows per page
                let totalRows = 50; // Total rows without search
                let filteredRows = []; // Store filtered rows based on search
                let allRows = []; // Store all rows for reference

                // Function to update the visible rows based on the current page
                function updateTableRows() {
                    const tableSize = parseInt(document.getElementById('table_size').value, 10);
                    const rowsToDisplay = filteredRows.length > 0 ? filteredRows : allRows;
                    const totalEntries = rowsToDisplay.length;
                    const totalPages = Math.ceil(totalEntries / tableSize);

                    // Calculate the start and end indexes for pagination
                    const startIndex = (currentPage - 1) * tableSize;
                    const endIndex = Math.min(startIndex + tableSize, totalEntries);

                    // Hide all rows first
                    allRows.forEach((row) => {
                        row.style.display = "none"; // Hide all rows
                    });

                    // Show the rows for the current page
                    rowsToDisplay.slice(startIndex, endIndex).forEach((row) => {
                        row.style.display = ""; // Show only the rows for the current page
                    });

                    // If no rows to display after search, show "No Data Found"
                    if (filteredRows.length === 0 && document.getElementById('search').value !== "") {
                        const tableBody = document.querySelector(".table-class tbody");
                        // Check if "No Data Found" already exists to avoid duplication
                        if (!document.querySelector(".table-class tbody tr.no-data-row")) {
                            const noDataRow = document.createElement("tr");
                            noDataRow.classList.add("no-data-row");
                            noDataRow.innerHTML = "<td colspan='11' style='text-align:center;'>No Data Found</td>";
                            tableBody.appendChild(noDataRow);
                        }

                        // Hide pagination controls if no data is found
                        document.querySelector('.pagination').style.display = 'none';
                    } else {
                        // Remove the "No Data Found" row if there are search results
                        const noDataRow = document.querySelector(".table-class tbody tr.no-data-row");
                        if (noDataRow) {
                            noDataRow.remove();
                        }

                        // Show pagination controls if there are results
                        document.querySelector('.pagination').style.display = 'block';
                    }

                    // Update footer to show correct page range
                    updateFooter(totalEntries, tableSize, currentPage, Math.ceil(totalEntries / tableSize));
                }

                // Function to update the pagination footer
                function updateFooter(totalEntries, tableSize, currentPage, totalPages) {
                    const footerText = document.querySelector("footer span");
                    const start = (currentPage - 1) * tableSize + 1;
                    const end = Math.min(currentPage * tableSize, totalEntries);
                    footerText.textContent = `Showing ${start} to ${end} of ${totalEntries} entries`;

                    // Update pagination buttons
                    const pageButtons = document.querySelectorAll('.pagination button');
                    pageButtons.forEach(button => button.classList.remove('active'));
                    document.getElementById('page-' + currentPage)?.classList.add('active');
                }

                // Function to handle the next page
                function nextPage() {
                    const rowsToDisplay = filteredRows.length > 0 ? filteredRows : allRows;
                    const totalPages = Math.ceil(rowsToDisplay.length / rowsPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        updateTableRows();
                    }
                }

                // Function to handle the previous page
                function prevPage() {
                    if (currentPage > 1) {
                        currentPage--;
                        updateTableRows();
                    }
                }

                // Function to go to a specific page
                function goToPage(page) {
                    currentPage = page;
                    updateTableRows();
                }

                // Function to change rows per page
                function changeRowsPerPage() {
                    rowsPerPage = parseInt(document.getElementById('table_size').value, 10);
                    currentPage = 1; // Reset to the first page when changing rows per page
                    updateTableRows();
                }

                // Function to handle the search input
                function searchTable() {
                    const searchInput = document.getElementById('search').value.toLowerCase();
                    filteredRows = []; // Reset filtered rows

                    allRows.forEach(row => {
                        const studentId = row.cells[0].textContent.toLowerCase();
                        const fullName = row.cells[2].textContent.toLowerCase();

                        if (studentId.includes(searchInput) || fullName.includes(searchInput)) {
                            filteredRows.push(row);
                        }
                    });

                    currentPage = 1; // Reset to the first page on search
                    updateTableRows(); // Update the rows based on the search
                }

                // Initial setup
                document.addEventListener("DOMContentLoaded", () => {
                    // Store all rows for reference
                    allRows = Array.from(document.querySelectorAll(".table-class tbody tr"));

                    // Set default rows on load
                    updateTableRows();

                    // Event listeners for pagination buttons
                    document.getElementById('prev-btn').addEventListener('click', prevPage);
                    document.getElementById('next-btn').addEventListener('click', nextPage);
                    document.querySelectorAll('.pagination button').forEach(button => {
                        if (button.id.startsWith('page-')) {
                            button.addEventListener('click', () => goToPage(parseInt(button.id.replace('page-', ''), 10)));
                        }
                    });

                    // Event listener for rows per page change
                    document.getElementById("table_size").addEventListener("change", changeRowsPerPage);

                    // Event listener for the search input
                    document.getElementById('search').addEventListener('input', searchTable);
                });
            </script>

        </div>

    </div>

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


            <style>
                :root {
                    /* Primary Colors */
                    --primary-bg: #f8f9fa;
                    --primary-text: #212529;
                    --primary-color-bg: #d8d8d8;
                    --secondary-text: #6c757d;

                    /* Accent Colors */
                    --accent-edit: #007bff;
                    --accent-delete: #dc3545;
                    --accent-hover: #0056b3;

                    /* Table Borders */
                    --table-border: #dee2e6;

                    /* Pagination Colors */
                    --pagination-bg: #ffffff;
                    --pagination-border: #dee2e6;
                    --pagination-active-bg: #007bff;
                    --pagination-active-text: #ffffff;

                    /* Misc */
                    --shadow: rgba(0, 0, 0, 0.1);
                }

                .main-crud-container {
                    margin-left: 260px;
                    padding: 20px;
                    background: var(--primary-bg);
                    border-radius: 3px;
                    margin: 10px;
                    border: 1px solid #aaa;
                }

                .main-crud-container .filterEntries {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                }

                .filterEntries .entries {
                    color: var(--secondary-text);
                }

                .filterEntries .entries select,
                .filterEntries .filter input {
                    padding: 5px 10px;
                    border: 1px solid #aaa;
                    color: var(--secondary-text);
                    background: var(--pagination-active-text);
                    border-radius: 3px;
                    outline: none;
                    transition: 0.3s;
                    cursor: pointer;
                    font-size: 12px;
                }

                .filterEntries .entries select {
                    padding: 5px 10px;
                }

                .filterEntries .filter {
                    display: flex;
                    align-items: center;
                }

                .filter label {
                    color: var(--secondary-text);
                    margin-right: 5px;
                }

                .filter input:focus {
                    border-color: var(--secondary-text);
                }

                .main-crud-container table {
                    border-collapse: collapse;
                    text-align: left;
                    width: 100%;
                }

                table .heading {
                    background: var(--pagination-bg);
                    background: transparent;
                    color: var(--primary-text);
                }

                table .heading th:hover {
                    background: var(--pagination-border);
                    transition: 0.3s;
                }

                table tr th,
                table tr td {
                    padding: 4px 15px;
                    color: var(--secondary-text);
                    background: var(--pagination-bg);
                }

                table tr th {
                    padding: 12px 15px;
                    font-size: 15px;
                }

                table tr td:nth-child(1),
                table tr td:nth-child(2) {
                    text-align: center;
                }

                table tr:hover {
                    cursor: pointer;
                    background: var(--pagination-border);
                }

                table tr td {
                    border-bottom: 1px solid var(--pagination-border);
                }

                table tbody tr:first-child td {
                    border-top: 1px solid var(--pagination-border);
                }

                table tbody tr:nth-child(odd) {
                    background: var(--primary-color-bg);
                }

                table tr td {
                    font-size: 13px;
                }

                table td button {
                    margin: 0 3px;
                    padding: 5px;
                    width: 35px;
                    color: var(--secondary-text);
                    font-size: 14px;
                    cursor: pointer;
                    pointer-events: auto;
                    outline: none;
                    border: 1px solid var(--pagination-border);
                    background: var(--pagination-bg);
                }

                .main-crud-container footer {
                    margin-top: 25px;
                    font-size: 14px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .main-crud-container footer span {
                    color: var(--primary-text);
                }

                footer .pagination {
                    display: flex;
                }

                footer .pagination button {
                    width: 40px;
                    padding: 5px 0;
                    color: var(--primary-text);
                    background: transparent;
                    font-size: 14px;
                    cursor: pointer;
                    pointer-events: auto;
                    outline: none;
                    border: 1px solid var(--secondary-text);
                    margin: 0;
                }

                .pagination button:first-child {
                    width: 85px;
                    border-top-left-radius: 3px;
                    border-bottom-left-radius: 3px;
                    border-left: 1px solid var(--secondary-text);
                    opacity: 0.6;
                    pointer-events: none;
                }

                .pagination button:last-child {
                    width: 85px;
                    border-top-right-radius: 3px;
                    border-bottom-right-radius: 3px;
                    opacity: 0.6;
                    pointer-events: none;
                }

                .pagination button.active,
                .pagination button:hover {
                    background: var(--accent-edit);
                }

                table tr .empty {
                    padding: 10px;
                }

                .table-scroll {
                    overflow-x: auto;
                    /* Enable horizontal scrolling */
                    white-space: nowrap;
                    /* Prevent text wrapping */
                    margin-top: 15px;
                    border: 1px solid var(--table-border);
                    /* Optional: To visually separate the table */
                }

                .table-scroll table {
                    width: 100%;
                    /* Table will adjust to container width */
                }

                /* Profile image styling */
                .profile-img {
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    overflow: hidden;
                    object-fit: cover;
                }
            </style>

        </div>
    </div>
    <!-- MAIN CONTAINER -->

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