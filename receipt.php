<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

$id = $_GET['id'] ?? 0;

$sql = "
SELECT 
    r.rentals_id,
    r.rent_date,
    r.return_date,
    r.total_date,
    r.total_price,
    r.start_mileage,
    r.return_mileage,
    c.fullname,
    m.model,
    m.price_per_day
FROM rentals r
JOIN customers c ON r.customers_id = c.customer_id
JOIN motorcycles m ON r.motorcycles_id = m.motorcycles_id
WHERE r.rentals_id = '$id'
AND r.rentals_status = 'คืนแล้ว'
";

$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("ไม่พบข้อมูลการเช่า");
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ใบเสร็จการเช่า (Admin)</title>

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(120deg,#FFF5E6,#FFEBCD);
}
header{
    background:#8B5E3C;
    color:white;
    padding:22px;
    text-align:center;
    font-size:26px;
    font-weight:bold;
}
.receipt{
    max-width:700px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
    border:1px solid #D2B48C;
}
h2{
    text-align:center;
    color:#8B5E3C;
}
.info p{
    margin:6px 0;
    font-size:16px;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
th,td{
    border:1px solid #D2B48C;
    padding:10px;
    text-align:center;
}
th{
    background:#8B5E3C;
    color:white;
}
.total{
    text-align:right;
    font-size:18px;
    margin-top:15px;
}
.btn{
    text-align:center;
    margin-top:30px;
}
.btn a, .btn button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:10px 22px;
    border-radius:8px;
    text-decoration:none;
    cursor:pointer;
}
@media print{
    header,.btn{display:none;}
}
</style>
</head>

<body>

<header>🧾 ใบเสร็จการเช่า (Admin)</header>

<div class="receipt">

<h2>ใบเสร็จรับเงิน</h2>

<div class="info">
    <p><b>ลูกค้า:</b> <?= $data['fullname'] ?></p>
    <p><b>รถ:</b> <?= $data['model'] ?></p>
    <p><b>วันที่เช่า:</b> <?= $data['rent_date'] ?></p>
    <p><b>วันที่คืน:</b> <?= $data['return_date'] ?></p>
    <p><b>เลขไมล์ก่อนเช่า:</b> <?= number_format($data['start_mileage']) ?> กม.</p>
    <p><b>เลขไมล์คืน:</b> <?= number_format($data['return_mileage']) ?> กม.</p>
</div>

<table>
<tr>
    <th>จำนวนวัน</th>
    <th>ราคาต่อวัน</th>
    <th>รวม</th>
</tr>
<tr>
    <td><?= $data['total_date'] ?></td>
    <td><?= number_format($data['price_per_day']) ?> บาท</td>
    <td><?= number_format($data['total_price']) ?> บาท</td>
</tr>
</table>

<div class="total">
    <b>ยอดรวมทั้งสิ้น <?= number_format($data['total_price']) ?> บาท</b>
</div>

</div>

<div class="btn">
    <button onclick="window.print()">🖨 พิมพ์ใบเสร็จ</button>
    <br><br>
    <!-- ✅ แก้แล้ว: กลับหน้ารายการเช่าฝั่งแอดมิน -->
    <a href="rentals_list.php">← กลับหน้ารายการเช่า</a>
</div>

</body>
</html>