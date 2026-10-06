<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    echo "ไม่พบข้อมูลการเช่า";
    exit();
}

$rentals_id = $_GET['id'];

// ดึงข้อมูลการเช่า + รถ
$q = mysqli_query($conn,"
    SELECT r.*, m.model
    FROM rentals r
    JOIN motorcycles m ON r.motorcycles_id = m.motorcycles_id
    WHERE r.rentals_id = '$rentals_id'
");

$rental = mysqli_fetch_assoc($q);

if(!$rental){
    echo "ไม่พบรายการเช่า";
    exit();
}

// กดยืนยันคืนรถ
if(isset($_POST['return'])){
    $return_mileage = $_POST['return_mileage'];

    // ตรวจสอบเลขไมล์
    if($return_mileage < $rental['start_mileage']){
        echo "<script>alert('เลขไมล์ตอนคืนต้องมากกว่าหรือเท่ากับตอนเช่า');</script>";
    }else{

        // อัปเดต rentals (สำคัญมาก)
        mysqli_query($conn,"
            UPDATE rentals SET
                rentals_status = 'คืนแล้ว',
                return_mileage = '$return_mileage'
            WHERE rentals_id = '$rentals_id'
        ");

        // อัปเดตเลขไมล์รถล่าสุด
        mysqli_query($conn,"
            UPDATE motorcycles SET
                mileage = '$return_mileage'
            WHERE motorcycles_id = '{$rental['motorcycles_id']}'
        ");

        header("Location: rentals.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>คืนรถ</title>

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(120deg,#FFF5E6,#FFEBCD);
}
.container{
    width:420px;
    margin:80px auto;
    background:#FFF8F0;
    padding:30px;
    border-radius:14px;
    border:1px solid #D2B48C;
    box-shadow:2px 4px 10px rgba(0,0,0,.2);
}
h2{
    text-align:center;
    color:#8B5E3C;
}
p{
    color:#4B2E1E;
}
label{
    font-weight:bold;
}
input{
    width:100%;
    padding:10px;
    margin-top:8px;
    margin-bottom:20px;
    border-radius:8px;
    border:1px solid #D2B48C;
}
button{
    background:#c0392b;
    color:white;
    border:none;
    padding:12px;
    width:100%;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
}
button:hover{
    background:#922b21;
}
</style>
</head>

<body>

<div class="container">
    <h2>คืนรถ</h2>

    <p><b>รถ:</b> <?= $rental['model'] ?></p>
    <p><b>เลขไมล์ก่อนเช่า:</b> <?= $rental['start_mileage'] ?> กม.</p>

    <form method="post">
        <label>เลขไมล์ตอนคืนรถ</label>
        <input type="number"
               name="return_mileage"
               required
               min="<?= $rental['start_mileage'] ?>">

        <button name="return">ยืนยันคืนรถ</button>
    </form>
</div>

</body>
</html>