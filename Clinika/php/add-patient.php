<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:../index.php');
    exit();
}
include('../php/header.php');
include('../includes/connection.php');

// Handle form submission
if (isset($_POST['add-patient'])) {
    // Patient details
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $emailid = $_POST['emailid'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $patient_type = $_POST['patient_type'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    // Lab test details
    $test_ids = $_POST['test_id'];

    // Start transaction
    $connection->begin_transaction();

    try {
        // Insert patient details
        $insert_patient_query = $connection->prepare("INSERT INTO tbl_patient (first_name, last_name, email, dob, gender, patient_type, address, phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_patient_query->bind_param("ssssssssi", $first_name, $last_name, $emailid, $dob, $gender, $patient_type, $address, $phone, $status);
        $insert_patient_query->execute();
        
        $patient_id = $connection->insert_id;

        // Process multiple lab tests
        if (!empty($_POST['test_id']) && is_array($_POST['test_id'])) {
            foreach ($_POST['test_id'] as $test_id) {
                // Fetch the test details
                $lab_test_query = $connection->prepare("SELECT test_name, cost FROM tbl_lab_test_list WHERE id = ?");
                $lab_test_query->bind_param("i", $test_id);
                $lab_test_query->execute();
                $lab_test_result = $lab_test_query->get_result();
                $lab_test = $lab_test_result->fetch_assoc();

                if ($lab_test) {
                    // Insert lab test details
                    $insert_labtest_query = $connection->prepare("INSERT INTO tbl_lab_tests (patient_id, test_id, result, date, status) VALUES (?, ?, 'Pending', CURRENT_TIMESTAMP, 'Pending')");
                    $insert_labtest_query->bind_param("ii", $patient_id, $test_id);
                    $insert_labtest_query->execute();

                    // Insert bill details for each test
                    $insert_bill_query = $connection->prepare("INSERT INTO tbl_bills (patient_id, service_name, amount, date, status) VALUES (?, ?, ?, CURRENT_TIMESTAMP, 'Unpaid')");
                    $insert_bill_query->bind_param("isd", $patient_id, $lab_test['test_name'], $lab_test['cost']);
                    $insert_bill_query->execute();
                }
            }
        } else {
            throw new Exception("Please select at least one lab test.");
        }

        // Commit transaction
        $connection->commit();

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Patient added successfully.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'patients.php';
            });
        </script>";
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $connection->rollback();
        $msg = "Error: " . $e->getMessage();
    }
}
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="page-title">Add Patient</h4>
            </div>
            <div class="col-sm-8 text-right m-b-20">
                <a href="patients.php" class="btn btn-primary btn-rounded float-right">Back</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form method="post">
                    <div class="row">
                        <!-- Patient Details -->
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
                                <label>Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="emailid" required>
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
                                <label>Phone</label>
                                <input class="form-control" type="text" name="phone" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group gender-select">
                                <label class="gen-label">Gender:</label>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" name="gender" class="form-check-input" value="Male" required>Male
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" name="gender" class="form-check-input" value="Female" required>Female
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Patient's Type <span class="text-danger">*</span></label>
                                <select class="form-control select2-single" name="patient_type" required>
                                    <option value="">Select Patient Type</option>
                                    <option value="InPatient">InPatient</option>
                                    <option value="OutPatient">OutPatient</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" name="address" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="display-block">Status</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="patient_active" value="1" checked>
                            <label class="form-check-label" for="patient_active">Active</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="patient_inactive" value="2">
                            <label class="form-check-label" for="patient_inactive">Inactive</label>
                        </div>
                    </div>

                    <!-- Lab Test Details -->
                    <h4 class="page-title">Lab Test Details</h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Select Lab Tests <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="test_id[]" multiple="multiple" required style="width: 100%;">
                                    <?php
                                    $lab_tests_query = $connection->query("SELECT id, test_name, cost FROM tbl_lab_test_list");
                                    while ($lab_test = $lab_tests_query->fetch_assoc()) {
                                        echo "<option value='{$lab_test['id']}'>{$lab_test['test_name']} - ₱ {$lab_test['cost']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-20 text-center">
                        <button name="add-patient" class="btn btn-primary submit-btn">Add Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../php/footer.php'); ?>
<script type="text/javascript">
    function confirmAdd() {
        alert("Patient Added");
        window.location.href = 'patients.php';
        return false;
    }
    <?php
        if(isset($msg)) {
            echo 'swal("' . $msg . '");';
        }
    ?>
</script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select Lab Tests",
        allowClear: true,
        width: '100%',
        dropdownAutoWidth: true,
        multiple: true
    }).on('select2:opening select2:closing', function( event ) {
        var $searchfield = $(this).parent().find('.select2-search__field');
        $searchfield.prop('disabled', true);
    });
});
</script>