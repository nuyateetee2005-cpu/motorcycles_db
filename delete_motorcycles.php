<?php
session_start();
include 'db.php';

// กันคนไม่ล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// รับ id
$id = $_GET['id'] ?? 0;

// ดึงชื่อรูปก่อนลบ
$q = mysqli_query($conn,"SELECT image FROM motorcycles WHERE motorcycles_id='$id'");
$row = mysqli_fetch_assoc($q);

if ($row) {
    // ลบรูปถ้ามี
    if (!empty($row['image']) && file_exists("uploads/".$row['image'])) {
        unlink("uploads/".$row['image']);
    }

    // ลบข้อมูลรถ
    mysqli_query($conn,"DELETE FROM motorcycles WHERE motorcycles_id='$id'");
}

// กลับหน้ารายการรถ
header("Location: motorcycles.php");
exit();