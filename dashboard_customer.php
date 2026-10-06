<?php
session_start();
include 'db.php';

/* ===== เช็กล็อกอิน ===== */
if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html lang="th">

<head>

<meta charset="UTF-8">

<title>Dashboard ลูกค้า</title>


<!-- ========================================
     เชื่อมระบบเสียง
     ======================================== -->

<script src="voice.js"></script>
<script src="voice_command.js"></script>


<style>

body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(120deg,#FFF5E6,#FFEBCD);
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
    max-width:900px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
    border:1px solid #D2B48C;
}


h2{
    color:#8B5E3C;
}


.card{
    display:grid;
    grid-template-columns:repeat(
        auto-fit,
        minmax(220px,1fr)
    );

    gap:20px;
    margin-top:30px;
}


.box{
    background:#FFEBCD;
    padding:25px;
    border-radius:12px;
    text-align:center;
    border:1px solid #D2B48C;
}


.box a{
    text-decoration:none;
    color:#8B5E3C;
    font-size:18px;
    font-weight:bold;
}


.logout{
    margin-top:30px;
    text-align:center;
}


.logout a{
    color:red;
    font-weight:bold;
    text-decoration:none;
}


/* ========================================
   ปุ่มเสียง
   ======================================== */

.voice-btn{

    margin-top:20px;

    margin-right:10px;

    padding:12px 20px;

    border:none;

    border-radius:10px;

    background:#8B5E3C;

    color:white;

    font-size:16px;

    cursor:pointer;

}


.voice-btn:hover{

    opacity:0.85;

}


/* ========================================
   กล่องคำแนะนำการใช้เสียง
   ======================================== */

.voice-help{

    margin-top:25px;

    padding:18px;

    background:#FFF;

    border:1px solid #D2B48C;

    border-radius:12px;

}


.voice-help h3{

    margin-top:0;

    color:#8B5E3C;

}


.voice-help p{

    margin:8px 0;

    line-height:1.6;

}


.voice-help ul{

    line-height:1.8;

    padding-left:25px;

}


</style>

</head>


<body>


<!-- ========================================
     Header
     ======================================== -->

<header>

    🏍️ ระบบเช่ารถจักรยานยนต์ (ลูกค้า)

</header>



<div class="container">


    <!-- ========================================
         ชื่อลูกค้า
         ======================================== -->

    <h2>
        สวัสดีคุณ <?= htmlspecialchars($fullname); ?>
    </h2>



    <!-- ========================================
         ปุ่มทดสอบระบบเสียง
         ======================================== -->

    <button
        class="voice-btn"
        onclick="speak('ยินดีต้อนรับเข้าสู่ระบบเช่ารถ')"
    >

        🔊 ทดสอบเสียง

    </button>



    <!-- ========================================
         ปุ่มรับคำสั่งเสียง
         ======================================== -->

    <button
        class="voice-btn"
        onclick="startVoiceCommand()"
    >

        🎤 พูดคำสั่ง

    </button>



    <!-- ========================================
         เมนูหลัก
         ======================================== -->

    <div class="card">


        <!-- รายการรถ -->

        <div class="box">

            <a href="motorcycles_customer.php">

                🏍️ รายการรถจักรยานยนต์

            </a>

        </div>



        <!-- เช่ารถ -->

        <div class="box">

            <a href="rentals_customer.php">

                📋 เช่ารถจักรยานยนต์

            </a>

        </div>



        <!-- สรุปการเช่า -->

        <div class="box">

            <a href="rentals_summary.php">

                🧾 สรุปการเช่ารถ

            </a>

        </div>


    </div>



    <!-- ========================================
         คำแนะนำการใช้เสียง
         ======================================== -->

    <div class="voice-help">

        <h3>🎤 ตัวอย่างคำสั่งเสียง</h3>

        <p>
            สามารถกดปุ่ม <b>🎤 พูดคำสั่ง</b>
            แล้วพูดคำสั่งเกี่ยวกับการเช่ารถได้
        </p>


        <ul>

            <li>“รถว่างกี่คัน”</li>

            <li>“มีรถรุ่นอะไรบ้าง”</li>

            <li>“ดูรายการรถ”</li>

            <li>“เช่ารถ”</li>

            <li>“ดูรายการเช่าของฉัน”</li>

            <li>“รถที่ฉันเช่าอยู่”</li>

            <li>“ต้องคืนรถวันไหน”</li>

            <li>“ราคาเช่ารถ”</li>

            <li>“หน้าหลัก”</li>

            <li>“ออกจากระบบ”</li>

        </ul>

    </div>



    <!-- ========================================
         ออกจากระบบ
         ======================================== -->

    <div class="logout">

        <a href="logout.php">

            ออกจากระบบ

        </a>

    </div>


</div>



<!-- ========================================
     ระบบพูดอัตโนมัติเมื่อเข้าสู่ Dashboard
     ======================================== -->

<script>

window.addEventListener('load', function() {

    setTimeout(function() {

        speak(
            'ยินดีต้อนรับเข้าสู่ระบบเช่ารถจักรยานยนต์'
        );

    }, 500);

});

</script>


</body>

</html>