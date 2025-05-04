<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
}
include('../php/header.php');
include('../includes/connection.php');
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4 col-3">
                <h4 class="page-title">Employees</h4>
            </div>
            <?php 
            if ($_SESSION['role'] == 1) { ?>
                <div class="col-sm-8 col-9 text-right m-b-20">
                    <a href="add-employee.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Add Employee</a>
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
                        <th>Mobile</th>
                        <th>Join Date</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fetch_query = mysqli_query($connection, "SELECT * FROM tbl_employee");
                    while ($row = mysqli_fetch_array($fetch_query)) {
                        ?>
                        <tr>
                            <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['emailid']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['joining_date']; ?></td>
                            <td>
                                <?php
                                // Handle role display
                                if ($row['role'] == "1") {
                                    echo '<span class="custom-badge status-red">Admin</span>';
                                } elseif ($row['role'] == "2") {
                                    echo '<span class="custom-badge status-blue">Doctor</span>';
                                } elseif ($row['role'] == "3") {
                                    echo '<span class="custom-badge status-green">Nurse</span>';
                                } elseif ($row['role'] == "4") {
                                    echo '<span class="custom-badge status-orange">Accountant</span>';
                                } else {
                                    echo '<span class="custom-badge status-grey">Other</span>';
                                }
                                ?>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="edit-employee.php?id=<?php echo $row['id']; ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-employee" data-id="<?php echo $row['id']; ?>" href="#"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('../php/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('.datatable')) {
        $('.datatable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                lengthMenu: "Show entries _MENU_",
                info: "Showing entries _START_ to _END_ of _TOTAL_",
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
    $('.delete-employee').on('click', function(e) {
        e.preventDefault();
        var employeeId = $(this).data('id'); // Get the employee ID from the data attribute

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
                // Send AJAX request to delete the employee
                $.ajax({
                    url: 'delete-employee.php', // Create a separate PHP file for deletion
                    type: 'POST',
                    data: { id: employeeId },
                    success: function(response) {
                        if (response == 'success') {
                            Swal.fire(
                                'Deleted!',
                                'The employee has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete the employee.',
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
