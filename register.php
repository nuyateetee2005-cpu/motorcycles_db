<?php
include 'db.php';

if(isset($_POST['register'])){
    $fullname = $_POST['fullname'];
    $id_card  = $_POST['id_card'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];
    $password = $_POST['password'];

    $check = mysqli_query($conn,"SELECT * FROM customers WHERE fullname='$fullname'");
    if(mysqli_num_rows($check) > 0){
        $error = "มีผู้ใช้นี้แล้ว";
    }else{
        mysqli_query($conn,"
            INSERT INTO customers(fullname,id_card,phone,address,password)
            VALUES('$fullname','$id_card','$phone','$address','$password')
        ");
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Register</title>

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(120deg,#FFF5E6,#FFEBCD);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}
.card{
    background:#FFF8F0;
    width:400px;
    padding:30px;
    border-radius:14px;
    box-shadow:2px 4px 10px rgba(0,0,0,.2);
    border:1px solid #D2B48C;
}
h2{
    text-align:center;
    color:#8B5E3C;
}
input,textarea{
    width:100%;
    padding:10px;
    margin:8px 0 14px;
    border-radius:6px;
    border:1px solid #8B5E3C;
    font-size:14px;
}
button{
    width:100%;
    background:#8B5E3C;
    color:white;
    border:none;
    padding:12px;
    border-radius:6px;
    font-size:16px;
}
.error{
    color:red;
    text-align:center;
}
.link{
    text-align:center;
    margin-top:15px;
}
a{
    color:#8B5E3C;
    font-weight:bold;
    text-decoration:none;
}
</style>
</head>

<body>
<div class="card">
<h2>Register ลูกค้า</h2>

<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="post">
    <input name="fullname" placeholder="ชื่อ-นามสกุล" required>
    <input name="id_card" placeholder="เลขบัตรประชาชน" required>
    <input name="phone" placeholder="เบอร์โทร" required>
    <textarea name="address" placeholder="ที่อยู่" required></textarea>
    <input type="password" name="password" placeholder="รหัสผ่าน" required>
    <button name="register">สมัครสมาชิก</button>
</form>

<div class="link">
    <a href="login.php">มีบัญชีแล้ว? เข้าสู่ระบบ</a>
</div>
</div>
</body>
</html>