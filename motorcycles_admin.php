<?php
session_start();
include 'db.php';

/* ===== กันคนยังไม่ล็อกอิน (แอดมินเท่านั้น) ===== */
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$sql = "SELECT * FROM motorcycles ORDER BY motorcycles_id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ข้อมูลรถมอเตอร์ไซค์</title>

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(120deg,#FFF5E6,#FFEBCD);
}
header{
    background:#8B5E3C;
    color:white;
    padding:20px;
    text-align:center;
    font-size:26px;
}
.container{
    max-width:1100px;
    margin:40px auto;
    padding:20px;
}
h2{
    color:#8B5E3C;
    border-bottom:2px solid #D2B48C;
    padding-bottom:6px;
    margin-bottom:20px;
}
.add-btn{
    display:inline-block;
    background:#8B5E3C;
    color:white;
    padding:10px 22px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    margin-bottom:20px;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#FFF8F0;
}
th,td{
    border:1px solid #8B5E3C;
    padding:12px;
    text-align:center;
}
th{ background:#D2B48C; }
tr:nth-child(even){ background:#FFEBCD; }

img{
    width:120px;
    border-radius:8px;
}

.btn-edit{
    background:#4CAF50;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
}
.btn-delete{
    background:#f44336;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
}
.back{
    margin-top:25px;
}
.back a{
    background:#8B5E3C;
    color:white;
    padding:10px 22px;
    border-radius:6px;
    text-decoration:none;
}
</style>
</head>

<body>

<header>🏍️ จัดการข้อมูลรถมอเตอร์ไซค์</header>

<div class="container">

<h2>รายการรถมอเตอร์ไซค์</h2>

<a href="add_motorcycle.php" class="add-btn">➕ เพิ่มรถ</a>

<table>
<tr>
    <th>ID</th>
    <th>ยี่ห้อ</th>
    <th>รุ่น</th>
    <th>ทะเบียน</th>
    <th>ราคา / วัน</th>
    <th>รูป</th>
    <th>จัดการ</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['motorcycles_id'] ?></td>
    <td><?= $row['brand'] ?></td>
    <td><?= $row['model'] ?></td>
    <td><?= $row['plate_number'] ?></td>
    <td><?= number_format($row['price_per_day']) ?> บาท</td>
    <td>
        <?php if(!empty($row['image'])){ ?>
            <img src="uploads/<?= $row['image'] ?>">
        <?php } else { ?>
            ไม่มีรูป
        <?php } ?>
    </td>
    <td>
        <a href="edit_motorcycles.php?id=<?= $row['motorcycles_id'] ?>" class="btn-edit">✏️ แก้ไข</a>
        <a href="delete_motorcycles.php?id=<?= $row['motorcycles_id'] ?>"
           class="btn-delete"
           onclick="return confirm('ยืนยันการลบรถคันนี้?')">🗑 ลบ</a>
    </td>
</tr>
<?php } ?>

</table>

<div class="back">
    <a href="dashboard_admin.php">⬅ กลับหน้า Dashboard</a>
</div>

</div>
</body>
</html>