<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

/* ===== ยืนยันเริ่มเช่า ===== */
if (isset($_POST['confirm_rent'])) {
    $rentals_id    = $_POST['rentals_id'];
    $start_mileage = $_POST['start_mileage'];

    mysqli_query($conn,"
        UPDATE rentals SET
        start_mileage='$start_mileage',
        rentals_status='กำลังเช่า'
        WHERE rentals_id='$rentals_id'
    ");
}

/* ===== ยืนยันคืนรถ ===== */
if (isset($_POST['confirm_return'])) {
    $rentals_id     = $_POST['rentals_id'];
    $return_mileage = $_POST['return_mileage'];

    $q = mysqli_query($conn,"
        SELECT start_mileage FROM rentals WHERE rentals_id='$rentals_id'
    ");
    $r = mysqli_fetch_assoc($q);

    if ($return_mileage < $r['start_mileage']) {
        echo "<script>alert('เลขไมล์คืนต้องมากกว่าเลขไมล์ก่อนเช่า');</script>";
    } else {
        mysqli_query($conn,"
            UPDATE rentals SET
            return_mileage='$return_mileage',
            rentals_status='คืนแล้ว'
            WHERE rentals_id='$rentals_id'
        ");
    }
}

/* ===== ดึงข้อมูล ===== */
$rentals = mysqli_query($conn,"
    SELECT r.*, c.fullname, m.model
    FROM rentals r
    JOIN customers c ON r.customers_id = c.customer_id
    JOIN motorcycles m ON r.motorcycles_id = m.motorcycles_id
    ORDER BY r.rentals_id ASC
");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการเช่ารถ | Admin</title>
<style>
body{font-family:Arial;background:#FFF5E6;}
header{background:#8B5E3C;color:white;padding:20px;text-align:center;font-size:26px;}
.container{max-width:1300px;margin:30px auto;background:#FFF8F0;padding:30px;border-radius:14px;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid #D2B48C;padding:10px;text-align:center;}
th{background:#FFEBCD;}
input{width:90px;padding:6px;}
button{background:#8B5E3C;color:white;border:none;padding:6px 10px;border-radius:6px;}
.status-rent{color:red;font-weight:bold;}
.status-return{color:green;font-weight:bold;}
</style>
</head>

<body>

<header>🏍️ จัดการเช่ามอเตอร์ไซค์ (Admin)</header>

<div class="container">

<table>
<tr>
    <th>ID</th>
    <th>ลูกค้า</th>
    <th>รถ</th>
    <th>ราคารวม</th>
    <th>ไมล์ก่อนเช่า</th>
    <th>ไมล์คืน</th>
    <th>ยืนยันการเช่า</th>
    <th>สถานะ</th>
    <th>จัดการคืนรถ</th>
</tr>

<?php while($r=mysqli_fetch_assoc($rentals)){ ?>
<tr>
    <td><?= $r['rentals_id'] ?></td>
    <td><?= $r['fullname'] ?></td>
    <td><?= $r['model'] ?></td>
    <td><?= number_format($r['total_price']) ?></td>
    <td><?= $r['start_mileage'] ?: '-' ?></td>
    <td><?= $r['return_mileage'] ?: '-' ?></td>

    <!-- ยืนยันเช่า -->
    <td>
    <?php if(empty($r['start_mileage'])){ ?>
        <form method="post">
            <input type="hidden" name="rentals_id" value="<?= $r['rentals_id'] ?>">
            <input type="number" name="start_mileage" required>
            <button name="confirm_rent">ยืนยัน</button>
        </form>
    <?php } else { echo '-'; } ?>
    </td>

    <!-- สถานะ -->
    <td>
        <?php
        if($r['rentals_status']=='รอเช่า'){
            echo '<span class="status-rent">รอเช่า</span>';
        }elseif($r['rentals_status']=='คืนแล้ว'){
            echo '<span class="status-return">คืนแล้ว</span>';
        }else{
            echo 'รอเช่า';
        }
        ?>
    </td>

    <!-- คืนรถ -->
    <td>
    <?php if($r['rentals_status']=='กำลังเช่า'){ ?>
        <form method="post">
            <input type="hidden" name="rentals_id" value="<?= $r['rentals_id'] ?>">
            <input type="number" name="return_mileage" required>
            <button name="confirm_return">ยืนยันการคืนรถ</button>
        </form>
    <?php } else { echo '-'; } ?>
    </td>
</tr>
<?php } ?>

</table>

<br>
<a href="dashboard_admin.php">⬅ กลับ Dashboard</a>

</div>
</body>
</html>