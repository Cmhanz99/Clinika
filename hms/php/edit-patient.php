<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}
include('../php/header.php');
include('../includes/connection.php');

// Get patient ID from the URL
$patient_id = $_GET['id'];

// Fetch patient details
$patient_query = mysqli_query($connection, "SELECT * FROM tbl_patient WHERE id = '$patient_id'");
$patient = mysqli_fetch_assoc($patient_query);

// Fetch lab test details for the patient
$labtest_query = mysqli_query($connection, "SELECT t.test_id, l.test_name, t.result, t.date, t.status 
                                            FROM tbl_lab_tests t 
                                            LEFT JOIN tbl_lab_test_list l ON t.test_id = l.id 
                                            WHERE t.patient_id = '$patient_id'");
$labtest = mysqli_fetch_assoc($labtest_query);

// Fetch billing details for the patient
$bill_query = mysqli_query($connection, "SELECT * FROM tbl_bills WHERE patient_id = '$patient_id'");
$bill = mysqli_fetch_assoc($bill_query);

// Handle form submission for updating lab test result
if (isset($_POST['update-labtest'])) {
    $result = mysqli_real_escape_string($connection, $_POST['result']);

    // Update the lab test result in the database
    $update_query = mysqli_query($connection, "UPDATE tbl_lab_tests SET result = '$result' WHERE patient_id = '$patient_id' AND test_id = '{$labtest['test_id']}'");

    if ($update_query) {
        echo "<script>alert('Lab Test Result updated successfully!'); window.location.href='edit-patient.php?id=$patient_id';</script>";
    } else {
        echo "<script>alert('Error updating Lab Test Result: " . mysqli_error($connection) . "');</script>";
    }
}

// Handle form submission for updating payment status
if (isset($_POST['update-payment-status'])) {
    $status = mysqli_real_escape_string($connection, $_POST['status']);

    // Update the payment status in the database
    $update_bill_query = mysqli_query($connection, "UPDATE tbl_bills SET status = '$status' WHERE patient_id = '$patient_id'");

    if ($update_bill_query) {
        echo "<script>alert('Payment status updated successfully!'); window.location.href='edit-patient.php?id=$patient_id';</script>";
    } else {
        echo "<script>alert('Error updating payment status: " . mysqli_error($connection) . "');</script>";
    }
}
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="page-title">Edit Patient</h4>
            </div>
            <div class="col-sm-8 text-right m-b-20">
                <a href="patients.php" class="btn btn-primary btn-rounded float-right">Back</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form method="post">
                    <!-- Patient Details (Read-Only) -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input class="form-control" type="text" value="<?php echo $patient['first_name']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input class="form-control" type="text" value="<?php echo $patient['last_name']; ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Lab Test Details -->
                    <?php if ($labtest): ?>
                    <h4 class="page-title">Lab Test Details</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Lab Test Type</label>
                                <input class="form-control" type="text" value="<?php echo $labtest['test_name']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Test Date</label>
                                <input class="form-control" type="text" value="<?php echo $labtest['date']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Lab Test Result</label>
                                <textarea class="form-control" name="result" rows="3" required><?php echo $labtest['result']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-20 text-center">
                        <button name="update-labtest" class="btn btn-primary submit-btn">Update Lab Test Result</button>
                    </div>
                    <?php else: ?>
                    <p class="text-center">No lab test found for this patient.</p>
                    <?php endif; ?>

                    <!-- Billing Details -->
                    <?php if ($bill): ?>
                    <h4 class="page-title">Billing Details</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Service Name</label>
                                <input class="form-control" type="text" value="<?php echo $bill['service_name']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Amount</label>
                                <input class="form-control" type="text" value="<?php echo $bill['amount']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input class="form-control" type="text" value="<?php echo $bill['date']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Payment Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="Unpaid" <?php echo ($bill['status'] == 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                                    <option value="Paid" <?php echo ($bill['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-20 text-center">
                        <button name="update-payment-status" class="btn btn-primary submit-btn">Update Payment Status</button>
                    </div>
                    <?php else: ?>
                    <p class="text-center">No billing information found for this patient.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
<script type="text/javascript">
     <?php
        if(isset($msg)) {

              echo 'swal("' . $msg . '");';
          }
     ?>
</script>