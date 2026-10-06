<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$q = mysqli_query($conn,"
    SELECT r.*, c.fullname, m.model
    FROM rentals r
    JOIN customers c ON r.customers_id=c.customer_id
    JOIN motorcycles m ON r.motorcycles_id=m.motorcycles_id
    WHERE r.rentals_id='$id'
");

$r = mysqli_fetch_assoc($q);
if(!$r){ die("ไม่พบข้อมูล"); }

if(isset($_POST['save'])){
    $return_mileage = $_POST['return_mileage'];

    mysqli_query($conn,"
        UPDATE rentals SET
        return_mileage='$return_mileage'
        WHERE rentals_id='$id'
    ");

    header("Location: rentals.php");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขการเช่า</title>
<style>
body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(120deg,#FFF5E6,#FFEBCD);
}
.container{
    max-width:500px;
    margin:80px auto;
    background:#FFF8F0;
    padding:30px;
    border-radius:16px;
    border:1px solid #D2B48C;
}
label{ font-weight:bold; }
input{
    width:100%;
    padding:10px;
    margin:10px 0 20px;
    border-radius:8px;
    border:1px solid #D2B48C;
}
button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:10px 25px;
    border-radius:8px;
}
</style>
</head>

<body>
<div class="container">
<h2>แก้ไขการเช่า</h2>

<p>ลูกค้า: <?= $r['fullname'] ?></p>
<p>รถ: <?= $r['model'] ?></p>

<form method="post">
    <label>เลขไมล์ตอนคืน</label>
    <input type="number" name="return_mileage"
           value="<?= $r['return_mileage'] ?>" required>

    <button name="save">บันทึกการแก้ไข</button>
</form>
</div>
</body>
</html>