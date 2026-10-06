<?php
session_start();
include 'db.php';

/* ====== ต้องล็อกอินลูกค้าก่อน ====== */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* ====== ต้องมี id รถ ====== */
if(!isset($_GET['id'])){
    header("Location: motorcycles_customer.php");
    exit();
}

$motorcycles_id = $_GET['id'];

/* ====== ดึงข้อมูลรถ ====== */
$q = mysqli_query($conn,"
    SELECT * FROM motorcycles 
    WHERE motorcycles_id='$motorcycles_id'
");
$bike = mysqli_fetch_assoc($q);

if(!$bike){
    die("ไม่พบข้อมูลรถ");
}

/* ====== บันทึกการเช่า ====== */
if(isset($_POST['rent'])){
    $rent_date   = $_POST['rent_date'];
    $return_date = $_POST['return_date'];
    $user_id     = $_SESSION['user_id'];

    // คำนวณจำนวนวัน
    $d1 = new DateTime($rent_date);
    $d2 = new DateTime($return_date);
    $total_date = $d1->diff($d2)->days;
    if($total_date <= 0){ $total_date = 1; }

    $total_price = $total_date * $bike['price_per_day'];

    mysqli_query($conn,"
        INSERT INTO rentals
        (customers_id,motorcycles_id,rent_date,return_date,
         start_mileage,total_date,total_price,rentals_status)
        VALUES
        ('$user_id','$motorcycles_id','$rent_date','$return_date',
         '0','$total_date','$total_price','กำลังเช่า')
    ");

    echo "<script>
        alert('เช่ารถสำเร็จ');
        location.href='my_rentals.php';
    </script>";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ยืนยันการเช่า</title>

<style>
body{
    font-family:Arial;
    background:linear-gradient(120deg,#FFF5E6,#FFEBCD);
}
.box{
    max-width:500px;
    margin:60px auto;
    background:#FFF8F0;
    padding:30px;
    border-radius:16px;
    border:1px solid #D2B48C;
}
img{
    width:100%;
    border-radius:12px;
}
h2{ color:#8B5E3C; }
label{ font-weight:bold; }
input{
    width:100%;
    padding:10px;
    margin:8px 0 15px;
    border-radius:8px;
    border:1px solid #D2B48C;
}
button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:10px;
    width:100%;
    border-radius:8px;
    cursor:pointer;
}
.price{
    font-size:18px;
    color:#8B5E3C;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="box">
    <h2>ยืนยันการเช่า</h2>

    <img src="uploads/<?= $bike['image'] ?>">

    <p><b><?= $bike['brand'] ?> <?= $bike['model'] ?></b></p>
    <p class="price">ราคา <?= $bike['price_per_day'] ?> บาท / วัน</p>

    <form method="post">
        <label>วันที่เช่า</label>
        <input type="date" name="rent_date" required>

        <label>วันที่คืน</label>
        <input type="date" name="return_date" required>

        <button name="rent">ยืนยันการเช่า</button>
    </form>
</div>

</body>
</html>