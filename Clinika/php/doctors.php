<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
}
include('../php/header.php');
include('../includes/connection.php');
?>
<link rel="stylesheet" type="text/css" href="../assets/css/dataTables.bootstrap4.min.css">
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/dataTables.bootstrap4.min.js"></script>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4 col-3">
                <h4 class="page-title">Doctors</h4>
            </div>
            <?php 
            // Show Add Doctor button only for role 1
            if ($_SESSION['role'] == 1) { ?>
            <div class="col-sm-8 col-9 text-right m-b-20">
                <a href="../php/add-doctor.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Add Doctor</a>
            </div>
            <?php } ?>
        </div>  

        <div class="table-responsive">
            <table class="datatable table table-stripped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>DOB</th>
                        <th>Phone</th>
                        <th>Bio</th>
                        <?php 
                        // Show Action column only for role 1
                        if ($_SESSION['role'] == 1) { ?>
                        <th>Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch doctors with role 2
                    $fetch_query = mysqli_query($connection, "SELECT * FROM tbl_employee WHERE role=2");
                    while ($row = mysqli_fetch_array($fetch_query)) {
                    ?>
                    <tr>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['emailid']; ?></td>
                        <td><?php echo $row['dob']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['bio']; ?></td>
                        <?php 
                        if ($_SESSION['role'] == 1) { ?>
                        <td class="text-right">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="edit-doctor.php?id=<?php echo $row['id']; ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a class="dropdown-item delete-doctor" data-id="<?php echo $row['id']; ?>" href="#"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                </div>
                            </div>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
include('../php/footer.php');
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('.datatable')) {
        $('.datatable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                lengthMenu: "Show_entries_MENU ",
                info: "Showing_ entries _START_ to _END_ of _TOTAL",
                search: "Search:",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            destroy: true // This allows the table to be reinitialized
        });
    }

    // SweetAlert2 for delete confirmation
    $('.delete-doctor').on('click', function(e) {
        e.preventDefault();
        var doctorId = $(this).data('id'); // Get the doctor ID from the data attribute

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to delete the doctor
                $.ajax({
                    url: 'delete-doctor.php', // Create a separate PHP file for deletion
                    type: 'POST',
                    data: { id: doctorId },
                    success: function(response) {
                        if (response == 'success') {
                            Swal.fire(
                                'Deleted!',
                                'The doctor has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete the doctor.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
    });
});
</script>