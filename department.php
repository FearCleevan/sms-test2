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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="depart.css">
    <link rel="icon" type="image/png" href="./image/apple-touch-icon.png">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <title>Department</title>
    <style>
        /* Custom styles for DataTable */
        #students-table {
            font-size: 12px;
        }

        #students-table thead th {
            font-weight: bold;
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

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="./dashboard.php" data-title="Dashboard" class="active"><i class="fa-solid fa-house"></i>
                    <span>Dashboard</span></a></li>
            <li><a href="./sample-details.php" data-title="Enroll a Student"><i class="fa-solid fa-user-plus"></i>
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
    <!-- Sidebar -->

    <!-- MAIN CONTAINER -->
    <div class="main-container">
        <!-- Enroll Options Buttons -->
        <div class="enroll-options">
            <button class="enroll-btn" onclick="fetchStudents('College')">
                College Department
            </button>
            <button class="enroll-btn" onclick="fetchStudents('Tvet')">
                TVET Department
            </button>
            <button class="enroll-btn" onclick="fetchStudents('JHS')">
                Junior High School Department
            </button>
            <button class="enroll-btn" onclick="fetchStudents('SHS')">
                Senior High School Department
            </button>
        </div>

        <!-- Table to Display Enrolled Students -->
        <div class="table-responsive mt-4">
            <h3 id="table-header">Department</h3>
            <table class="table table-bordered table-striped" id="students-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Profile</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <!-- MAIN CONTAINER -->

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#students-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 10, // Default number of entries per page
                lengthMenu: [
                    [10, 20, 30, 50], // Options for the dropdown
                    [10, 20, 30, 50] // Labels for the dropdown
                ],
            });
        });

        // Function to fetch students based on department
        function fetchStudents(department) {
            console.log("Fetching students for department:", department); // Debugging
            const tableHeader = document.getElementById("table-header");
            tableHeader.innerText = `${department} Department`;

            fetch(`fetch_students.php?department=${department}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Data received:", data); // Debugging
                    const table = $('#students-table').DataTable();
                    table.clear();

                    if (data.length > 0) { // Check if data is an array
                        data.forEach(student => {
                            const imageSrc = student.profile || './uploads/default-profile.png';
                            table.row.add([
                                student.student_id,
                                `<img src="${imageSrc}" alt="Profile" width="40" height="40" style="border-radius: 50%; object-fit: cover;">`,
                                `${student.first_name} ${student.middle_name} ${student.last_name}`,
                                student.address,
                                student.email,
                                student.phone,
                                student.department,
                                student.status,
                                `<button class="btn btn-primary btn-sm">Edit</button> <button class="btn btn-danger btn-sm">Delete</button>`
                            ]).draw(false);
                        });
                    } else {
                        table.row.add([
                            'No data available', '', '', '', '', '', '', '', ''
                        ]).draw(false);
                    }
                })
                .catch(error => console.error("Error fetching data:", error));
        }
    </script>


</body>

</html>