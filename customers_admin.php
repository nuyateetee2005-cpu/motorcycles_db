<?php
session_start();
include 'db.php';

/* ===== เช็กแอดมิน ===== */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

/* ===== ค่าเริ่มต้น ===== */
$edit_mode = false;
$fullname = $id_card = $phone = $address = "";
$edit_id = null;

/* ===== โหมดแก้ไข ===== */
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit']);

    $q = mysqli_query($conn,"SELECT * FROM customers WHERE customer_id=$edit_id");
    $row = mysqli_fetch_assoc($q);

    $fullname = $row['fullname'];
    $id_card  = $row['id_card'];
    $phone    = $row['phone'];
    $address  = $row['address'];
}

/* ===== เพิ่มลูกค้า ===== */
if (isset($_POST['save'])) {
    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
    $id_card  = mysqli_real_escape_string($conn,$_POST['id_card']);
    $phone    = mysqli_real_escape_string($conn,$_POST['phone']);
    $address  = mysqli_real_escape_string($conn,$_POST['address']);

    mysqli_query($conn,"
        INSERT INTO customers(fullname,id_card,phone,address)
        VALUES('$fullname','$id_card','$phone','$address')
    ");
    header("Location: customers_admin.php");
    exit();
}

/* ===== อัปเดตลูกค้า ===== */
if (isset($_POST['update'])) {
    $edit_id = intval($_POST['edit_id']);
    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
    $id_card  = mysqli_real_escape_string($conn,$_POST['id_card']);
    $phone    = mysqli_real_escape_string($conn,$_POST['phone']);
    $address  = mysqli_real_escape_string($conn,$_POST['address']);

    mysqli_query($conn,"
        UPDATE customers SET
        fullname='$fullname',
        id_card='$id_card',
        phone='$phone',
        address='$address'
        WHERE customer_id=$edit_id
    ");
    header("Location: customers_admin.php");
    exit();
}

/* ===== ลบลูกค้า ===== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn,"DELETE FROM customers WHERE customer_id=$id");
    header("Location: customers_admin.php");
    exit();
}

/* ===== ดึงข้อมูล ===== */
$result = mysqli_query($conn,"SELECT * FROM customers ORDER BY customer_id ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการลูกค้า | Admin</title>

<style>
body{
    margin:0;
    font-family:Arial;
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
.container{
    max-width:950px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
}
label{ font-weight:bold; }
input,textarea{
    width:100%;
    padding:10px;
    margin:8px 0 14px;
}
button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:10px 22px;
    border-radius:8px;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
}
th,td{
    border:1px solid #D2B48C;
    padding:10px;
    text-align:center;
}
th{ background:#FFEBCD; }

.btn-edit{
    background:#4CAF50;
    color:white;
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
}
.btn-delete{
    background:#f44336;
    color:white;
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
}
</style>
</head>

<body>

<header>👤 จัดการข้อมูลลูกค้า (Admin)</header>

<div class="container">

<h2><?= $edit_mode ? "✏️ แก้ไขข้อมูลลูกค้า" : "➕ เพิ่มข้อมูลลูกค้า" ?></h2>

<form method="post">
    <?php if($edit_mode){ ?>
        <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
    <?php } ?>

    <label>ชื่อ-สกุล</label>
    <input type="text" name="fullname" value="<?= $fullname ?>" required>

    <label>เลขบัตรประชาชน</label>
    <input type="text" name="id_card" maxlength="13" value="<?= $id_card ?>" required>

    <label>เบอร์โทร</label>
    <input type="text" name="phone" value="<?= $phone ?>" required>

    <label>ที่อยู่</label>
    <textarea name="address" required><?= $address ?></textarea>

    <?php if($edit_mode){ ?>
        <button name="update">อัปเดตข้อมูล</button>
        <a href="customers_admin.php">ยกเลิก</a>
    <?php }else{ ?>
        <button name="save">บันทึกข้อมูล</button>
    <?php } ?>
</form>

<h2>📋 รายชื่อลูกค้า</h2>

<table>
<tr>
    <th>ID</th>
    <th>ชื่อ</th>
    <th>เลขบัตร</th>
    <th>โทร</th>
    <th>ที่อยู่</th>
    <th>จัดการ</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?= $row['customer_id'] ?></td>
    <td><?= $row['fullname'] ?></td>
    <td><?= $row['id_card'] ?></td>
    <td><?= $row['phone'] ?></td>
    <td><?= $row['address'] ?></td>
    <td>
        <a href="?edit=<?= $row['customer_id'] ?>" class="btn-edit">แก้ไข</a>
        <a href="?delete=<?= $row['customer_id'] ?>" class="btn-delete"
           onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
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