<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit();
}
include('../php/header.php');
include('../includes/connection.php');

// Fetch patient profiles
$query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, email, dob, gender, phone, address, patient_type, status 
          FROM tbl_patient";
$result = mysqli_query($connection, $query);
?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Patient Profiles</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date of Birth</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                        <td>{$row['name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['dob']}</td>
                                        <td>{$row['gender']}</td>
                                        <td>{$row['phone']}</td>
                                        <td>{$row['address']}</td>
                                        <td>{$row['patient_type']}</td>
                                        <td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../php/footer.php'); ?>