<?php
session_start();
if (empty($_SESSION['name']) || $_SESSION['role'] != 1) {
    echo 'error';
    exit();
}
include('../includes/connection.php');

if (isset($_POST['id'])) {
    $id = mysqli_real_escape_string($connection, $_POST['id']);

    // Delete the patient
    $delete_query = mysqli_query($connection, "DELETE FROM tbl_patient WHERE id='$id'");

    if ($delete_query) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>