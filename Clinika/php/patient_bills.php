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

// Handle bill deletion
if (isset($_GET['delete_bill_id'])) {
    $bill_id = $_GET['delete_bill_id'];
    $delete_bill_query = mysqli_query($connection, "DELETE FROM tbl_bills WHERE bill_id = '$bill_id'");

    if ($delete_bill_query) {
        echo "<script>alert('Bill deleted successfully!'); window.location.href='patient_bills.php';</script>";
    } else {
        echo "<script>alert('Error deleting bill: " . mysqli_error($connection) . "'); window.location.href='patient_bills.php';</script>";
    }
}

// Handle mark as paid
if (isset($_POST['mark_paid'])) {
    $patient_id = mysqli_real_escape_string($connection, $_POST['patient_id']);

    // Update the bill status to "Paid"
    $update_bill_query = "UPDATE tbl_bills SET status = 'Paid' WHERE patient_id = '$patient_id'";
    $update_bill_result = mysqli_query($connection, $update_bill_query);

    // Update the lab test results to "Released"
    $update_labtest_query = "UPDATE tbl_lab_tests SET status = 'Released' WHERE patient_id = '$patient_id'";
    $update_labtest_result = mysqli_query($connection, $update_labtest_query);

    if ($update_bill_result && $update_labtest_result) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Bill marked as Paid and lab test results updated to Released.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'patient_bills.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to update bill or lab test results.',
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
                        <h4 class="card-title">Patient Bills</h4>
                        <!-- Search Form -->
                        <form method="GET" action="patient_bills.php" class="form-inline float-right">
                            <input type="text" name="search" class="form-control" placeholder="Search by Patient Name or Service Name" value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary ml-2">Search</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php
                        // Fetch patient bills
                        $query = "SELECT p.id AS patient_id, 
                                         CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
                                         GROUP_CONCAT(b.service_name SEPARATOR ', ') AS services,
                                         SUM(b.amount) AS total_amount,
                                         MIN(b.date) AS earliest_date, -- Use MIN or MAX for a single date
                                         GROUP_CONCAT(DISTINCT b.status SEPARATOR ', ') AS statuses
                                  FROM tbl_bills b 
                                  LEFT JOIN tbl_patient p ON b.patient_id = p.id";

                        // Add search condition if search input is provided
                        if (!empty($search)) {
                            $query .= " WHERE CONCAT(p.first_name, ' ', p.last_name) LIKE '%$search%' 
                                         OR b.service_name LIKE '%$search%'";
                        }

                        $query .= " GROUP BY p.id"; // Group by patient ID

                        $result = mysqli_query($connection, $query);

                        if (mysqli_num_rows($result) > 0) {
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Services</th>
                                    <th>Total Amount</th>
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
                                        <td>{$row['services']}</td>
                                        <td>{$row['total_amount']}</td>
                                        <td>{$row['earliest_date']}</td>
                                        <td>{$row['statuses']}</td>
                                        <td>
                                            <form method='POST' action='patient_bills.php' style='display:inline;'>
                                                <input type='hidden' name='patient_id' value='{$row['patient_id']}'>
                                                <button type='submit' name='mark_paid' class='btn btn-success'>Paid</button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        } else {
                            echo "<p class='text-center'>No bills found.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../php/footer.php'); ?>