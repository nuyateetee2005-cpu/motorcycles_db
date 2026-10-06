<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Dashboard - Admin</title>

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
    box-shadow:0 4px 10px rgba(0,0,0,.25);
}
.container{
    max-width:900px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
    border:1px solid #D2B48C;
    box-shadow:2px 4px 10px rgba(0,0,0,.2);
    text-align:center;
}
h2{ color:#8B5E3C; }
p{ color:#5d4037; margin-bottom:30px; }

.menu{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:22px;
}
.menu a{
    text-decoration:none;
    background:#FFEBCD;
    padding:26px;
    border-radius:12px;
    font-size:18px;
    font-weight:bold;
    color:#4B2E1E;
    border:1px solid #D2B48C;
    transition:.3s;
}
.menu a:hover{
    background:#8B5E3C;
    color:white;
    transform:translateY(-5px);
}
</style>
</head>

<body>

<header>
    📊 ระบบเช่ารถจักรยานยนต์ (ผู้ดูแลระบบ)
</header>

<div class="container">
    <h2>ยินดีต้อนรับแอดมิน</h2>
    <p>กรุณาเลือกเมนูที่ต้องการจัดการ</p>

    <div class="menu">
        <a href="motorcycles_admin.php">🛵 รายการรถจักรยานยนต์</a>
        <a href="customers_admin.php">👤 ข้อมูลลูกค้า</a>
         <a href="rentals_admin.php">📋 รายการเช่ารถจักรยานยนต์ของลูกค้า</a>
        <a href="rentals_list.php">📄 รายการเช่ารถจักรยานยนต์ทั้งหมด</a>
        <a href="logout.php">🔒 ออกจากระบบ</a>
    </div>
</div>

</body>
</html>