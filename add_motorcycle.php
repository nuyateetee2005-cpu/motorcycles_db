<?php
session_start();
include 'db.php';

/* ===== เช็กแอดมิน ===== */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}

/* ===== บันทึกข้อมูล ===== */
if (isset($_POST['save'])) {

    $brand  = mysqli_real_escape_string($conn, $_POST['brand']);
    $model  = mysqli_real_escape_string($conn, $_POST['model']);
    $plate  = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $price  = mysqli_real_escape_string($conn, $_POST['price_per_day']);

    $image_name = "";

    /* ===== อัปโหลดรูป ===== */
    if (!empty($_FILES['image']['name'])) {

        $allow_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allow_ext)) {
            die("อนุญาตเฉพาะไฟล์รูป JPG, PNG, WEBP เท่านั้น");
        }

        $image_name = time() . "_" . rand(1000, 9999) . "." . $ext;

        /* ===== แก้ไขส่วน Path อัปโหลด ===== */
        $upload_dir = __DIR__ . "/uploads/";

        /* ถ้ายังไม่มีโฟลเดอร์ uploads ให้สร้างอัตโนมัติ */
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        /* ตำแหน่งไฟล์จริงที่จะบันทึก */
        $upload_path = $upload_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            die("อัปโหลดรูปไม่สำเร็จ กรุณาตรวจสอบโฟลเดอร์ uploads");
        }
    }

    mysqli_query($conn, "
        INSERT INTO motorcycles
        (brand, model, plate_number, price_per_day, image)
        VALUES
        ('$brand','$model','$plate','$price','$image_name')
    ");

    header("Location: motorcycles_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เพิ่มรถมอเตอร์ไซค์ | Admin</title>

<style>
body{
    font-family:Arial;
    background:linear-gradient(120deg,#FFF5E6,#FFEBCD);
}
.box{
    max-width:520px;
    margin:60px auto;
    background:#FFF8F0;
    padding:30px;
    border-radius:16px;
    border:1px solid #D2B48C;
    box-shadow:0 4px 10px rgba(0,0,0,.2);
}
h2{
    text-align:center;
    color:#8B5E3C;
}
label{
    font-weight:bold;
}
input{
    width:100%;
    padding:10px;
    margin:8px 0 18px;
    border-radius:8px;
    border:1px solid #D2B48C;
}
button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:10px 24px;
    border-radius:8px;
    cursor:pointer;
}
.back{
    text-align:center;
    margin-top:20px;
}
.back a{
    text-decoration:none;
    color:#8B5E3C;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="box">

<h2>➕ เพิ่มรถมอเตอร์ไซค์ (Admin)</h2>

<form method="post" enctype="multipart/form-data">

    <label>ยี่ห้อ</label>
    <input type="text" name="brand" required>

    <label>รุ่น</label>
    <input type="text" name="model" required>

    <label>ทะเบียน</label>
    <input type="text" name="plate_number" required>

    <label>ราคา / วัน</label>
    <input type="number" name="price_per_day" required>

    <label>รูปรถ</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit" name="save">บันทึก</button>

</form>

<div class="back">
    <a href="motorcycles_admin.php">⬅ กลับหน้ารายการรถ</a>
</div>

</div>

</body>
</html>