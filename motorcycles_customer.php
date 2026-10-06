<?php
session_start();
include 'db.php';

/* ===== เช็กล็อกอินลูกค้า ===== */
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

/* ===== ดึงข้อมูลรถที่ว่าง ===== */
/*
    รถที่มีสถานะ
    - รอแอดมินยืนยัน
    - กำลังเช่า
    - รอแอดมินยืนยันคืนรถ

    จะไม่ถือว่าเป็นรถว่าง
*/
$motorcycles = mysqli_query($conn,"
    SELECT * FROM motorcycles
    WHERE motorcycles_id NOT IN (
        SELECT motorcycles_id
        FROM rentals
        WHERE rentals_status IN (
            'รอแอดมินยืนยัน',
            'กำลังเช่า',
            'รอแอดมินยืนยันคืนรถ'
        )
    )
    ORDER BY motorcycles_id DESC
");
?>

<!DOCTYPE html>
<html lang="th">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>เลือกรถมอเตอร์ไซค์</title>


<!-- =========================
     Voice System
========================= -->

<script src="voice.js"></script>

<script src="voice_command.js"></script>


<style>

body{
    margin:0;
    font-family:Arial, sans-serif;
    background:linear-gradient(120deg,#FFF5E6,#FFEBCD);
}

/* ===== Header ===== */

header{
    background:#8B5E3C;
    color:white;
    padding:22px;
    text-align:center;
    font-size:26px;
    font-weight:bold;
}

/* ===== Container ===== */

.container{
    max-width:1200px;
    margin:40px auto;
    padding:20px;
}

/* ===== หัวข้อ ===== */

.title{
    text-align:center;
    color:#8B5E3C;
    margin-bottom:25px;
}

/* ===== Grid ===== */

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:25px;
}

/* ===== Card ===== */

.card{
    background:#FFF8F0;
    border-radius:16px;
    border:1px solid #D2B48C;
    box-shadow:2px 4px 10px rgba(0,0,0,.15);
    overflow:hidden;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

/* ===== Image ===== */

.card img{
    width:100%;
    height:200px;
    object-fit:cover;
}

/* ===== Card Body ===== */

.card-body{
    padding:18px;
    text-align:center;
}

.card-body h3{
    margin:0 0 10px;
    color:#8B5E3C;
}

.price{
    font-weight:bold;
    font-size:16px;
}

/* ===== Voice Box ===== */

.voice-box{
    text-align:center;
    margin-top:30px;
    margin-bottom:20px;
}

/* ===== Voice Button ===== */

.voice-btn{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:12px 25px;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    margin:5px;
}

.voice-btn:hover{
    background:#70482E;
}

/* ===== Voice Help ===== */

.voice-help{
    max-width:700px;
    margin:20px auto 30px;
    background:#FFF8F0;
    border:1px solid #D2B48C;
    border-radius:12px;
    padding:18px;
    color:#8B5E3C;
}

.voice-help h3{
    margin-top:0;
    text-align:center;
}

.voice-help p{
    margin:7px 0;
}

/* ===== Back ===== */

.back{
    text-align:center;
    margin-top:25px;
}

.back a{
    color:#8B5E3C;
    font-weight:bold;
    text-decoration:none;
}

.back a:hover{
    text-decoration:underline;
}

/* ===== กรณีไม่มีรถ ===== */

.no-car{
    text-align:center;
    background:#FFF8F0;
    border:1px solid #D2B48C;
    border-radius:12px;
    padding:30px;
    color:#8B5E3C;
    font-size:18px;
    grid-column:1 / -1;
}


/* =========================
   Mobile
========================= */

@media(max-width:600px){

    header{
        font-size:21px;
        padding:18px;
    }

    .container{
        margin:20px auto;
        padding:15px;
    }

    .voice-btn{
        width:100%;
        margin:5px 0;
    }

}

</style>

</head>

<body>


<header>

    🏍️ รายการมอเตอร์ไซค์ให้เช่า

</header>


<div class="container">


<!-- =========================
     หัวข้อ
========================= -->

<h2 class="title">

    🏍️ รถมอเตอร์ไซค์ที่สามารถเช่าได้

</h2>


<!-- =========================
     รายการรถ
========================= -->

<div class="grid">

<?php

if (mysqli_num_rows($motorcycles) > 0) {

    while($m = mysqli_fetch_assoc($motorcycles)) {

?>

    <div class="card">


        <!-- =========================
             รูปรถ
        ========================= -->

        <?php if (!empty($m['image'])) { ?>

            <img
                src="uploads/<?= htmlspecialchars($m['image']); ?>"
                alt="motorcycle"
            >

        <?php } else { ?>

            <div style="
                height:200px;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#FFEBCD;
                font-size:50px;
            ">

                🏍️

            </div>

        <?php } ?>


        <!-- =========================
             รายละเอียดรถ
        ========================= -->

        <div class="card-body">

            <h3>

                <?= htmlspecialchars($m['brand'].' '.$m['model']); ?>

            </h3>


            <div class="price">

                ราคา
                <?= number_format($m['price_per_day']); ?>
                บาท / วัน

            </div>

        </div>


    </div>

<?php

    }

} else {

?>

    <div class="no-car">

        🏍️ ตอนนี้ไม่มีรถมอเตอร์ไซค์ว่าง

    </div>

<?php

}

?>

</div>


<!-- =========================
     ปุ่มสั่งงานด้วยเสียง
========================= -->

<div class="voice-box">


    <!-- ===== อ่านคำอธิบายหน้า ===== -->

    <button
        class="voice-btn"
        onclick="speak('หน้านี้แสดงรายการรถมอเตอร์ไซค์ที่สามารถเช่าได้ พร้อมราคาเช่าต่อวัน')"
    >

        🔊 อธิบายหน้านี้

    </button>


    <!-- ===== รับคำสั่งเสียง ===== -->

    <button
        class="voice-btn"
        onclick="startVoiceCommand()"
    >

        🎤 พูดคำสั่ง

    </button>


</div>


<!-- =========================
     ตัวอย่างคำสั่งเสียง
========================= -->

<div class="voice-help">

    <h3>
        🎤 ตัวอย่างคำสั่งเสียง
    </h3>

    <p>
        • “มีรถว่างกี่คัน”
    </p>

    <p>
        • “มีรถรุ่นอะไรบ้าง”
    </p>

    <p>
        • “PXC ว่างไหม”
    </p>

    <p>
        • “PXC ว่างกี่คัน”
    </p>

    <p>
        • “PXC ราคาเท่าไหร่”
    </p>

    <p>
        • “มีรถราคาไม่เกิน 200 บาทไหม”
    </p>

    <p>
        • “เช่ารถ”
    </p>

    <p>
        • “กลับหน้าหลัก”
    </p>

</div>


<!-- =========================
     กลับ Dashboard
========================= -->

<div class="back">

    <a href="dashboard_customer.php">

        ⬅ กลับ Dashboard

    </a>

</div>


</div>


<!-- =========================
     พูดอัตโนมัติเมื่อเปิดหน้า
========================= -->

<script>

window.addEventListener('load', function(){

    setTimeout(function(){

        <?php if(mysqli_num_rows($motorcycles) > 0){ ?>

            speak(
                'กำลังแสดงรายการรถมอเตอร์ไซค์ที่สามารถเช่าได้'
            );

        <?php }else{ ?>

            speak(
                'ขณะนี้ไม่มีรถมอเตอร์ไซค์ว่างสำหรับเช่า'
            );

        <?php } ?>

    }, 700);

});

</script>


</body>

</html>
