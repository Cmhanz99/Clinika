<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}
include('../php/header.php');
include('../includes/connection.php');

// Initialize search variable
$search = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($connection, $_GET['search']);
}

// Handle mark done action
if (isset($_POST['mark_done'])) {
    $patient_id = mysqli_real_escape_string($connection, $_POST['patient_id']);
    $date = mysqli_real_escape_string($connection, $_POST['date']);

    // Update the status to "Done" and results to "Ready"
    $update_query = "UPDATE tbl_lab_tests 
                     SET status = 'Done', result = 'Ready' 
                     WHERE patient_id = '$patient_id' AND date = '$date'";
    $update_result = mysqli_query($connection, $update_query);

    if ($update_result) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Lab test status updated successfully.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'labtest_results.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to update lab test status.',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Lab Test Results</h4>
                        <!-- Search Form -->
                        <form method="GET" action="labtest_results.php" class="form-inline float-right">
                            <input type="text" name="search" class="form-control" placeholder="Search by Patient Name or Test Name" value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary ml-2">Search</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php
                        // Fetch lab test results with test type
                        $query = "SELECT CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
                                         GROUP_CONCAT(DISTINCT l.test_name SEPARATOR ', ') AS lab_test_types, 
                                         GROUP_CONCAT(DISTINCT t.result SEPARATOR ', ') AS results, 
                                         t.date, 
                                         t.status, 
                                         t.patient_id 
                                  FROM tbl_lab_tests t 
                                  LEFT JOIN tbl_patient p ON t.patient_id = p.id 
                                  LEFT JOIN tbl_lab_test_list l ON t.test_id = l.id";

                        // Add search condition if search input is provided
                        if (!empty($search)) {
                            $query .= " WHERE CONCAT(p.first_name, ' ', p.last_name) LIKE '%$search%' 
                                         OR l.test_name LIKE '%$search%'";
                        }

                        // Group by patient to combine lab tests
                        $query .= " GROUP BY t.patient_id, t.date, t.status";

                        $result = mysqli_query($connection, $query);

                        if (mysqli_num_rows($result) > 0) {
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Lab Test Types</th>
                                    <th>Results</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                        <td>{$row['patient_name']}</td>
                                        <td>{$row['lab_test_types']}</td>
                                        <td>{$row['results']}</td>
                                        <td>{$row['date']}</td>
                                        <td>{$row['status']}</td>
                                        <td>
                                            <form method='POST' action='labtest_results.php' style='display:inline;'>
                                                <input type='hidden' name='patient_id' value='{$row['patient_id']}'>
                                                <input type='hidden' name='date' value='{$row['date']}'>
                                                <button type='submit' name='mark_done' class='btn btn-success btn-sm'>Done</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        } else {
                            echo "<p class='text-center'>No lab test results found.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../php/footer.php'); ?>