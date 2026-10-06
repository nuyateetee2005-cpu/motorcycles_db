<?php
session_start();

// กันคนยังไม่ล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Dashboard - N-Forest Peak</title>

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(120deg,#FFF5E6,#FFEBCD);
    min-height:100vh;
}

/* header */
header{
    background:#8B5E3C;
    color:white;
    padding:22px;
    text-align:center;
    font-size:26px;
    font-weight:bold;
    box-shadow:0 4px 10px rgba(0,0,0,.25);
}

/* main box */
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

/* title */
h2{
    color:#8B5E3C;
    margin-bottom:8px;
}

p{
    color:#5d4037;
    margin-bottom:30px;
    font-size:16px;
}

/* menu */
.menu{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:22px;
}

.menu a{
    text-decoration:none;
    background:#FFEBCD;
    padding:26px 20px;
    border-radius:12px;
    font-size:18px;
    font-weight:bold;
    color:#4B2E1E;
    border:1px solid #D2B48C;
    transition:.3s;
    box-shadow:2px 2px 8px rgba(0,0,0,.15);
}

.menu a:hover{
    background:#8B5E3C;
    color:white;
    transform:translateY(-5px);
}

/* footer */
footer{
    text-align:center;
    margin-top:30px;
    color:#4B2E1E;
    font-size:14px;
}
</style>
</head>

<body>

<header>
    📊 ระบบเช่ามอเตอร์ไซค์ (ผู้ดูเเลระบบ)
</header>

<div class="container">
    <h2>ยินดีต้อนรับ</h2>
    <p>กรุณาเลือกเมนูที่ต้องการใช้งาน</p>

    <div class="menu">
        <a href="motorcycles.php">🛵 ข้อมูลรถมอเตอร์ไซค์ </a>
        <a href="customers.php">👤 ข้อมูลลูกค้า</a>
        <a href="rentals.php">📄 รายการเช่ามอเตอร์ไซค์</a>
        <a href="rentals_list.php">📄 รายการเช่ามอเตอร์ไซค์ทั้งหมด</a>
        <a href="logout.php">🔒 ออกจากระบบ</a>
    </div>
</div>

<footer>
    
</footer>

</body>
</html>
receipt.php