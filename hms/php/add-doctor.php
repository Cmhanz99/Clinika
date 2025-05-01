<?php
session_start();
if (empty($_SESSION['name']) || $_SESSION['role'] != 1) {
    header('location:index.php');
    exit();
}
include('../php/header.php');
include('../includes/connection.php');

if (isset($_REQUEST['add-doctor'])) {
    // Escape all input values to prevent SQL injection
    $first_name = mysqli_real_escape_string($connection, $_REQUEST['first_name']);
    $last_name = mysqli_real_escape_string($connection, $_REQUEST['last_name']);
    $username = mysqli_real_escape_string($connection, $_REQUEST['username']);
    $emailid = mysqli_real_escape_string($connection, $_REQUEST['emailid']);
    $pwd = mysqli_real_escape_string($connection, $_REQUEST['pwd']);
    $dob = mysqli_real_escape_string($connection, $_REQUEST['dob']);
    $employee_id = mysqli_real_escape_string($connection, $_REQUEST['employee_id']);
    $joining_date = mysqli_real_escape_string($connection, $_REQUEST['joining_date']);
    $gender = mysqli_real_escape_string($connection, $_REQUEST['gender']);
    $phone = mysqli_real_escape_string($connection, $_REQUEST['phone']);
    $address = mysqli_real_escape_string($connection, $_REQUEST['address']);
    $bio = mysqli_real_escape_string($connection, $_REQUEST['bio']);
    $status = mysqli_real_escape_string($connection, $_REQUEST['status']);
    $role = 2;

    // Use prepared statement instead of direct query
    $query = "INSERT INTO tbl_employee (first_name, last_name, username, emailid, password, dob, employee_id, joining_date, gender, phone, address, bio, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if($stmt = mysqli_prepare($connection, $query)) {
        mysqli_stmt_bind_param($stmt, 'ssssssssssssii', 
            $first_name, 
            $last_name, 
            $username, 
            $emailid, 
            $pwd, 
            $dob, 
            $employee_id, 
            $joining_date, 
            $gender, 
            $phone, 
            $address, 
            $bio, 
            $role, 
            $status
        );
        
        if(mysqli_stmt_execute($stmt)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Doctor added successfully.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'doctors.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to add doctor.',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4 ">
                <h4 class="page-title">Add Doctor</h4>
            </div>
            <div class="col-sm-8  text-right m-b-20">
                <a href="doctors.php" class="btn btn-primary btn-rounded float-right">Back</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form method="post">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input class="form-control" type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="username" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="emailid" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input class="form-control" type="password" name="pwd" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control datetimepicker" name="dob" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Employee ID </label>
                                <input class="form-control" type="text" name="employee_id" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Joining Date</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control datetimepicker" name="joining_date" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone </label>
                                <input class="form-control" type="text" name="phone" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group gender-select">
                                <label class="gen-label">Gender:</label>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" name="gender" class="form-check-input" value="Male">Male
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" name="gender" class="form-check-input" value="Female">Female
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Short Biography</label>
                        <textarea class="form-control" rows="3" cols="30" name="bio" required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="display-block">Status</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="doctor_active" value="1" checked>
                            <label class="form-check-label" for="doctor_active">
                                Active
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="doctor_inactive" value="0">
                            <label class="form-check-label" for="doctor_inactive">
                                Inactive
                            </label>
                        </div>
                    </div>
                    <div class="m-t-20 text-center">
                        <button name="add-doctor" class="btn btn-primary submit-btn">Create Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../php/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>