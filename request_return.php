<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['rentals_id'])) {

    $rentals_id = $_POST['rentals_id'];
    $customer_id = $_SESSION['customer_id'];

    // เปลี่ยนสถานะเป็น "รอแอดมินยืนยันคืนรถ"
    mysqli_query($conn,"
        UPDATE rentals 
        SET rentals_status = 'รอแอดมินยืนยัน'
        WHERE rentals_id = '$rentals_id'
        AND customers_id = '$customer_id'
    ");

    header("Location: rentals_customer.php");
    exit();
}
?>