<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['rentals_id'])) {

    $rentals_id  = $_POST['rentals_id'];
    $customer_id = $_SESSION['customer_id'];

    // 🔎 ตรวจสอบสถานะก่อน
    $check = mysqli_query($conn,"
        SELECT rentals_status 
        FROM rentals 
        WHERE rentals_id = '$rentals_id'
        AND customers_id = '$customer_id'
    ");

    if (mysqli_num_rows($check) == 0) {
        header("Location: rentals_customer.php");
        exit();
    }

    $data = mysqli_fetch_assoc($check);

    // ❌ ถ้ากำลังเช่า ห้ามลบ
    if ($data['rentals_status'] == 'กำลังเช่า' || $data['rentals_status'] == 'รอคืนรถ') {
        echo "<script>
            alert('ไม่สามารถลบได้ เนื่องจากอยู่ระหว่างการเช่า');
            window.location='rentals_customer.php';
        </script>";
        exit();
    }

    // ✅ สถานะอื่น ลบได้
    mysqli_query($conn,"
        DELETE FROM rentals 
        WHERE rentals_id = '$rentals_id'
        AND customers_id = '$customer_id'
    ");

    header("Location: rentals_customer.php");
    exit();
}
?>