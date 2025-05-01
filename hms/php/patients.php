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
                <h4 class="page-title">Patients</h4>
            </div>
            <div class="col-sm-8 col-9 text-right m-b-20">
                <a href="add-patient.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Add Patient</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="datatable table table-stripped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM tbl_patient";
                    $result = mysqli_query($connection, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['first_name']} {$row['last_name']}</td>
                            <td>{$row['dob']}</td>
                            <td>{$row['address']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['patient_type']}</td>
                            <td>
                                <a href='../php/patient_profile.php?id={$row['id']}' class='btn btn-info btn-sm'>View Profile</a>
                                <a href='#' class='btn btn-danger btn-sm delete-patient' data-id='{$row['id']}'>Delete</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
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
    $('.delete-patient').on('click', function(e) {
        e.preventDefault();
        var patientId = $(this).data('id'); // Get the patient ID from the data attribute

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
                // Send AJAX request to delete the patient
                $.ajax({
                    url: 'delete-patient.php', // Create a separate PHP file for deletion
                    type: 'POST',
                    data: { id: patientId },
                    success: function(response) {
                        if (response == 'success') {
                            Swal.fire(
                                'Deleted!',
                                'The patient has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete the patient.',
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