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
               CONCAT(address, ' ', province, ' ', city) as address, email, phone, course AS course, course_level AS year, session AS semester, 'Active' AS status 
        FROM college_students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Student</title>
    <link rel="stylesheet" href="college.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./image/apple-touch-icon.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./image/apple-touch-icon.png">

    <!-- Data Table CSS -->
    <link rel="stylesheet" href="./assets/js/datatables.min.css">
</head>

<body>
    <!-- SIDEBAR MENU -->
    <div class="sidebar-menu">
        <h2 id="dashboard-title">College Department</h2>
        <ul class="nav-list">
            <li><a href="./dashboard.php" data-title="Dashboard"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a></li>
            <li><a href="./enroll-student.php" data-title="Enroll a Student"><i class="fa-solid fa-user-plus"></i> <span>Enroll a Student</span></a></li>
            <li><a href="./department.php" data-title="Department" class="active"><i class="fa-solid fa-building"></i> <span>Department</span></a></li>
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
                <i class="fa-solid fa-bars" id="toggle-menu-btn"></i>
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
                            ?>
                        </a>
                    </div>
                    <div class="name-access">
                        <?php
                        if (isset($_SESSION['username'])) {
                            $query = mysqli_query($conn, "SELECT * FROM admin_user WHERE username='$email'");
                            if ($row = mysqli_fetch_assoc($query)) {
                                echo "<p>" . htmlspecialchars($row['firstName']) . " " . htmlspecialchars($row['lastName']) . "</p>";
                                echo "<span>" . htmlspecialchars($row['access'] ?? "Guest") . "</span>";
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
            <div class="enroll-options">
                <button class="enroll-btn">
                    <a href="#" onclick="setActive(this)">
                        College Department
                    </a>
                </button>
                <button class="enroll-btn">
                    <a href="./tvet_dep.php" onclick="setActive(this)">
                        TVET Department
                    </a>
                </button>
                <button class="enroll-btn">
                    <a href="./jhs.php" onclick="setActive(this)">
                        Junior High School Department
                    </a>
                </button>
                <button class="enroll-btn">
                    <a href="./shs.php" onclick="setActive(this)">
                        Senior High School Department
                    </a>
                </button>
            </div>

        </div>

        <div class="main-container-header">
            <div class="sub-main-container-header start">
                <i class="fa-solid fa-bars-progress"></i> <span id="department-name">College Department</span>
            </div>
        </div>

        <div class="main-container-form">

            <div class="table-main-container">
                <div class="table-container">
                    <div class="table-class">

                        <table id="example" class="table table-striped table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
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
                                        echo "<td><img class='profile-image' src='data:image/jpeg;base64," . base64_encode($row['profile']) . "' alt='Profile'></td>";
                                        echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['year']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['semester']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td>
                                                <button title='View'><i class='fa-regular fa-eye'></i></button>
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
                            <tfoot>
                                <tr>
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
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* General container styling */
        .sub-main-container,
        .main-container-form {
            width: 100%;
            margin: 0 auto;
            padding: 1rem;
        }

        .main-crud-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 3px;
        }

        .main-crud-container .filterEntries {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;

        }

        .filterEntries .entries {
            color: var(--secondary-text);
            margin-right: 10px;
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

        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            object-fit: cover;
            object-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-class thead tr th,
        .table-class tfoot tr th {
            font-size: 13px;
            /* Font size for header and footer */
            font-weight: bold;
            /* Bold text */
            padding: 10px;
            /* Consistent padding */
            text-align: left;
            /* Align text to the left */
            color: #000;
            /* Text color */
            background-color: #f4f4f4;
            /* Background color */
            border-bottom: 2px solid #ddd;
            /* Optional: Add a bottom border for design */
        }


        .table-class tbody tr td {
            font-size: 12px !important;
            /* Set the font size */
            padding: 8px;
            /* Optional: Add some padding for better spacing */
            text-align: left;
            /* Optional: Align text to the left */
            color: #333;
            font-weight: 500;
            /* Optional: Text color for consistency */
            vertical-align: middle;
            /* Center vertically */
        }

        tbody tr td:first-child {
            text-align: center;
            /* Horizontal alignment */
            vertical-align: middle;
            /* Vertical alignment */
        }


        .table-main-container {
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;

            /* Adds a soft shadow */
        }


        .table-class {
            padding: 5px;
        }

        .table-class table {
            margin-top: 5px;
            border: 1px solid #ddd;
        }

        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            object-fit: cover;
            object-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dt-length label {
            font-weight: 500;
            font-size: 14px;
        }

        .dt-buttons {
            margin-top: 5px;
            gap: 3px;
        }

        .dt-buttons button {
            color: #fff;
            background: #34495e;
            transition: opacity all ease 0.3s;
        }

        .dt-buttons button span {
            font-size: 13px;
            font-weight: 600;
        }

        .dt-buttons button:hover {
            opacity: 0.8;
        }

        .dt-info {
            margin-top: 5px;
            font-weight: 500;
            font-size: 14px;
        }

        .dt-paging li button {
            margin-top: 5px;
        }

        /* Styling for the action buttons */
        td button {
            background: none;
            /* Remove background */
            border: none;
            /* Remove default button borders */
            padding: 5px 8px;
            /* Add some padding */
            cursor: pointer;
            /* Change cursor to pointer */
            /* Space between buttons */
            transition: all 0.3s ease;
            /* Smooth transition for hover effects */
            color: #333;
            /* Default icon color */
            font-size: 10px;
            background: #34495e;
            border-radius: 3px;
            /* Icon size */
        }

        /* Styling for the buttons on hover */
        td button:hover {
            opacity: 0.7;
            /* Light gray background on hover */
            color: #2980b9;
            /* Change icon color on hover */
            border-radius: 3px;
            /* Round the corners */
        }

        /* Specific icon color for each button */
        td button[title="View"] i {
            color: #fff;
            /* Green for view */
        }

        td button[title="Edit"] i {
            color: #fff;
            /* Orange for edit */
        }

        td button[title="Delete"] i {
            color: #fff;
            /* Red for delete */
        }

        /* Optional: Add some spacing for the icon inside buttons */
        td button i {
            font-size: 15px;
            /* Larger icon size */
        }
    </style>

    <script>
        // Function to set active state and display text
        function setActive(button) {
            // Get all buttons
            const buttons = document.querySelectorAll('.enroll-btn');

            // Remove 'active' class from all buttons
            buttons.forEach(button => {
                button.classList.remove('active');
            });

            // Add 'active' class to the clicked button
            button.classList.add('active');

            // Get the text of the clicked button
            const buttonText = button.innerText;

            // Update the display span with the clicked button's text
            document.getElementById('department-name').innerText = `${buttonText}`;
        }
    </script>

    <!-- MAIN CONTAINER -->

    <script>
        // Sidebar active menu highlighting
        document.querySelectorAll('.sidebar-menu ul li a').forEach(menuItem => {
            menuItem.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-menu ul li a').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('dashboard-title').textContent = this.getAttribute('data-title');
            });
        });

        // Toggle sidebar
        const toggleMenuBtn = document.getElementById('toggle-menu-btn');
        toggleMenuBtn.addEventListener('click', () => {
            document.querySelector('.sidebar-menu').classList.toggle('collapsed');
        });

        // Profile menu toggle
        const profileLink = document.getElementById("profile-link");
        const profileMenu = document.getElementById("profile-menu");
        profileLink.addEventListener("click", function(e) {
            e.preventDefault();
            profileMenu.style.display = profileMenu.style.display === "block" ? "none" : "block";
        });

        // Close profile menu on outside click
        window.addEventListener("click", function(e) {
            if (!profileLink.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.style.display = "none";
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <!-- JQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Data Table JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Bootstrap JS and jQuery (needed for modal) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


    <script src="./assets/js/datatables.min.js"></script>
    <script src="./assets/js/app.js"></script>

</body>


</html>