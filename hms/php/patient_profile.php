<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}
include('../php/header.php');
?>
<style type="text/css" media="print">
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-area,
        #printable-area * {
            visibility: visible !important;
        }
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none;
        }
    }
</style>
<?php
include('../includes/connection.php');

// Get the patient ID from the URL
$patient_id = $_GET['id'];

// Fetch patient details
$patient_query = mysqli_query($connection, "SELECT * FROM tbl_patient WHERE id = '$patient_id'");
$patient = mysqli_fetch_assoc($patient_query);

// Fetch lab test results for the patient
$labtests_query = mysqli_query($connection, "SELECT l.test_name, t.result, t.date, t.status 
                                             FROM tbl_lab_tests t 
                                             LEFT JOIN tbl_lab_test_list l ON t.test_id = l.id 
                                             WHERE t.patient_id = '$patient_id'");

// Fetch billing details for the patient
$bills_query = mysqli_query($connection, "SELECT * FROM tbl_bills WHERE patient_id = '$patient_id'");
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="page-title">Patient Profile</h4>
            </div>
            <div class="col-sm-8 text-right m-b-20 no-print">
                <button onclick="printProfile();" class="btn btn-primary btn-rounded"><i class="fa fa-print"></i> Print Profile</button>
                <a href="patients.php" class="btn btn-secondary btn-rounded">Back to Patients</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2" id="printable-area">
                <div class="card-box">
                    <h3 class="card-title">Personal Information</h3>
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>First Name:</strong> <?php echo $patient['first_name']; ?></p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Last Name:</strong> <?php echo $patient['last_name']; ?></p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Email:</strong> <?php echo $patient['email']; ?></p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Phone:</strong> <?php echo $patient['phone']; ?></p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Date of Birth:</strong> <?php echo $patient['dob']; ?></p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Gender:</strong> <?php echo $patient['gender']; ?></p>
                        </div>
                        <div class="col-sm-12">
                            <p><strong>Address:</strong> <?php echo $patient['address']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <h3 class="card-title">Lab Test Results</h3>
                    <?php if (mysqli_num_rows($labtests_query) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Test Name</th>
                                <th>Result</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($labtest = mysqli_fetch_assoc($labtests_query)): ?>
                            <tr>
                                <td><?php echo $labtest['test_name']; ?></td>
                                <td><?php echo $labtest['result']; ?></td>
                                <td><?php echo $labtest['date']; ?></td>
                                <td><?php echo $labtest['status']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No lab test results found for this patient.</p>
                    <?php endif; ?>
                </div>

                <div class="card-box">
                    <h3 class="card-title">Payment History</h3>
                    <?php if (mysqli_num_rows($bills_query) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($bill = mysqli_fetch_assoc($bills_query)): ?>
                            <tr>
                                <td><?php echo $bill['service_name']; ?></td>
                                <td><?php echo $bill['amount']; ?></td>
                                <td><?php echo $bill['date']; ?></td>
                                <td><?php echo $bill['status']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No billing information found for this patient.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function printProfile() {
    var originalContents = document.body.innerHTML;
    var printContent = document.getElementById('printable-area').innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // Reload the page to restore functionality
}
</script>
<?php include('footer.php'); ?>