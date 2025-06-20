<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}
include('../php/header.php');
include('../includes/connection.php');

if (isset($_REQUEST['add-department'])) {
    $department_name = $_REQUEST['department'];
    $description = $_REQUEST['description'];
    $status = $_REQUEST['status'];

    $insert_query = mysqli_query($connection, "INSERT INTO tbl_department SET department_name='$department_name', description='$description', status='$status'");

    if ($insert_query > 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Department created successfully.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'departments.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to create department.',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="page-title">Add Department</h4>
            </div>
            <div class="col-sm-8 text-right m-b-20">
                <a href="departments.php" class="btn btn-primary btn-rounded float-right">Back</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form method="post">
                    <div class="form-group">
                        <label>Department Name</label>
                        <input class="form-control" type="text" name="department" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea cols="30" rows="4" class="form-control" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="display-block">Department Status</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="product_active" value="1" checked>
                            <label class="form-check-label" for="product_active">Active</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="product_inactive" value="0">
                            <label class="form-check-label" for="product_inactive">Inactive</label>
                        </div>
                    </div>
                    <div class="m-t-20 text-center">
                        <button class="btn btn-primary submit-btn" name="add-department">Create Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../php/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>