<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM motorcycles WHERE motorcycles_id = $id";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("ไม่พบข้อมูลรถ");
}

if (isset($_POST['update'])) {

    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $plate = $_POST['plate_number'];
    $price = $_POST['price_per_day'];

    $image_name = $data['image'];

    if (!empty($_FILES['image']['name'])) {
        $new_image = time().'_'.$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$new_image);
        $image_name = $new_image;
    }

    $update = "UPDATE motorcycles SET
        brand='$brand',
        model='$model',
        plate_number='$plate',
        price_per_day='$price',
        image='$image_name'
        WHERE motorcycles_id=$id";

    mysqli_query($conn, $update);

    header("Location: motorcycles_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขข้อมูลรถ</title>
<style>
body{
    font-family: Arial;
    background:#FFF5E6;
}
.container{
    max-width:600px;
    margin:40px auto;
    background:#FFF8F0;
    padding:30px;
    border-radius:12px;
}
h2{
    text-align:center;
    color:#8B5E3C;
}
input{
    width:100%;
    padding:10px;
    margin:10px 0;
}
button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:12px;
    width:100%;
    border-radius:8px;
    font-size:16px;
}
img{
    width:150px;
    margin-top:10px;
}
a{
    display:block;
    text-align:center;
    margin-top:15px;
}
</style>
</head>

<body>

<div class="container">
<h2>✏️ แก้ไขข้อมูลรถ</h2>

<form method="post" enctype="multipart/form-data">

    <label>ยี่ห้อ</label>
    <input type="text" name="brand" value="<?= $data['brand'] ?>" required>

    <label>รุ่น</label>
    <input type="text" name="model" value="<?= $data['model'] ?>" required>

    <label>ทะเบียน</label>
    <input type="text" name="plate_number" value="<?= $data['plate_number'] ?>" required>

    <label>ราคา / วัน</label>
    <input type="number" name="price_per_day" value="<?= $data['price_per_day'] ?>" required>

    <label>รูปปัจจุบัน</label><br>
    <?php if($data['image']){ ?>
        <img src="uploads/<?= $data['image'] ?>">
    <?php } else { ?>
        ไม่มีรูป
    <?php } ?>

    <br><br>
    <label>อัปโหลดรูปใหม่</label>
    <input type="file" name="image">

    <button type="submit" name="update">💾 บันทึกการแก้ไข</button>
</form>

<a href="motorcycles_admin.php">⬅ กลับหน้ารายการรถ</a>
</div>

</body>
</html>