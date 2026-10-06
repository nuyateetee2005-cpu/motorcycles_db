<?php
session_start();
include 'db.php';

/* ===== เช็กแอดมิน ===== */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

/* ===== ดึงเฉพาะรายการที่คืนแล้ว ===== */
$sql = "
SELECT r.*, 
       c.fullname, 
       m.model
FROM rentals r
JOIN customers c ON r.customers_id = c.customer_id
JOIN motorcycles m ON r.motorcycles_id = m.motorcycles_id
WHERE r.rentals_status = 'คืนแล้ว'
ORDER BY r.return_date DESC
";
$q = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายการเช่าที่คืนแล้ว</title>

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(120deg,#FFF5E6,#FFEBCD);
    min-height:100vh;
}
header{
    background:#8B5E3C;
    color:white;
    padding:22px;
    text-align:center;
    font-size:26px;
    font-weight:bold;
}
.container{
    max-width:1200px;
    margin:40px auto;
    background:#FFF8F0;
    padding:30px;
    border-radius:16px;
    border:1px solid #D2B48C;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:12px;
    border-bottom:1px solid #D2B48C;
    text-align:center;
}
th{
    background:#FFEBCD;
    color:#4B2E1E;
}
.status-returned{
    color:green;
    font-weight:bold;
}
.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
}
.btn-receipt{
    background:#D2B48C;
    color:#4B2E1E;
}
.back a{
    text-decoration:none;
    font-weight:bold;
    color:#8B5E3C;
}
</style>
</head>

<body>

<header>📋 รายการเช่าทั้งหมด (Admin)</header>

<div class="container">
<table>
<tr>
    <th>ลำดับ</th>
    <th>ลูกค้า</th>
    <th>รถ</th>
    <th>ไมล์ก่อนเช่า</th>
    <th>ไมล์ตอนคืน</th>
    <th>วันที่เช่า</th>
    <th>วันที่คืน</th>
    <th>ราคา</th>
    <th>สถานะ</th>
    <th>ใบเสร็จ</th>
</tr>

<?php $i=1; while($r=mysqli_fetch_assoc($q)){ ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= $r['fullname'] ?></td>
    <td><?= $r['model'] ?></td>
    <td><?= number_format($r['start_mileage']) ?> กม.</td>
    <td><?= number_format($r['return_mileage']) ?> กม.</td>
    <td><?= date('d/m/Y', strtotime($r['rent_date'])) ?></td>
    <td><?= date('d/m/Y', strtotime($r['return_date'])) ?></td>
    <td><?= number_format($r['total_price'],2) ?> บาท</td>
    <td><span class="status-returned">คืนแล้ว</span></td>
    <td>
        <a class="btn btn-receipt"
           href="receipt.php?id=<?= $r['rentals_id'] ?>"
           target="_blank">
           🧾 ใบเสร็จ
        </a>
    </td>
</tr>
<?php } ?>
</table>

<div class="back">
    <br>
    <a href="dashboard_admin.php">⬅ กลับ Dashboard</a>
</div>

</div>
</body>
</html>