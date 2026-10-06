<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

/* ===== ดึงเฉพาะรายการที่คืนแล้ว ===== */
$summary = mysqli_query($conn, "
    SELECT r.*, m.brand, m.model
    FROM rentals r
    JOIN motorcycles m ON r.motorcycles_id = m.motorcycles_id
    WHERE r.customers_id = '$customer_id'
    AND r.rentals_status = 'คืนแล้ว'
    ORDER BY r.rentals_id DESC
");
?>
<!DOCTYPE html>
<html lang="th">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>สรุปการเช่ารถ</title>

<!-- ===== เชื่อมระบบเสียง ===== -->
<script src="voice.js"></script>
<script src="voice_command.js"></script>

<style>

body{
    margin:0;
    font-family:Arial, sans-serif;
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
    max-width:950px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
    border:1px solid #D2B48C;
    box-sizing:border-box;
}

h2{
    color:#8B5E3C;
    text-align:center;
    margin-top:0;
}

/* ===== ปุ่มเสียง ===== */

.voice-area{
    text-align:center;
    margin:20px 0;
}

.voice-btn{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
    margin:5px;
}

.voice-btn:hover{
    opacity:0.85;
}

/* ===== ตาราง ===== */

.table-wrapper{
    width:100%;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
    min-width:700px;
}

th,td{
    border:1px solid #D2B48C;
    padding:10px;
    text-align:center;
}

th{
    background:#FFEBCD;
    color:#8B5E3C;
}

/* ===== สถานะ ===== */

.status{
    background:green;
    color:white;
    padding:6px 12px;
    border-radius:8px;
    font-size:14px;
}

/* ===== ยอดรวม ===== */

.total-box{
    text-align:right;
    margin-top:20px;
    color:#8B5E3C;
}

/* ===== ปุ่มกลับ ===== */

.back{
    margin-top:25px;
    display:inline-block;
    text-decoration:none;
}

.back button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:10px 22px;
    border-radius:8px;
    cursor:pointer;
    font-size:15px;
}

.back button:hover{
    opacity:0.85;
}

/* ===== รองรับมือถือ ===== */

@media(max-width:600px){

    header{
        font-size:21px;
        padding:18px;
    }

    .container{
        margin:20px 10px;
        padding:20px;
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
    📄 สรุปการเช่ารถ (เสร็จสิ้น)
</header>

<div class="container">

    <h2>🧾 ประวัติการเช่ารถของฉัน</h2>

    <!-- ===== ปุ่มระบบเสียง ===== -->

    <div class="voice-area">

        <!-- ปุ่มอ่านข้อมูล -->
        <button
            class="voice-btn"
            onclick="speak('กำลังแสดงสรุปการเช่ารถของคุณ')">
            🔊 อ่านสรุปการเช่า
        </button>

        <!-- ปุ่มรับคำสั่งเสียง -->
        <button
            class="voice-btn"
            onclick="startVoiceCommand()">
            🎤 พูดคำสั่ง
        </button>

    </div>


    <!-- ===== ตารางสรุปการเช่า ===== -->

    <div class="table-wrapper">

    <table>

        <tr>
            <th>ลำดับ</th>
            <th>รถ</th>
            <th>วันที่เช่า</th>
            <th>วันที่คืน</th>
            <th>จำนวนวัน</th>
            <th>ราคารวม</th>
            <th>สถานะ</th>
        </tr>

        <?php

        $no = 1;
        $total_all = 0;

        while($r = mysqli_fetch_assoc($summary)){

            $total_all += $r['total_price'];

        ?>

        <tr>

            <td>
                <?= $no++; ?>
            </td>

            <td>
                <?= htmlspecialchars($r['brand'].' '.$r['model']); ?>
            </td>

            <td>
                <?= htmlspecialchars($r['rent_date']); ?>
            </td>

            <td>
                <?= htmlspecialchars($r['return_date']); ?>
            </td>

            <td>
                <?= htmlspecialchars($r['total_date']); ?> วัน
            </td>

            <td>
                <?= number_format($r['total_price']); ?> บาท
            </td>

            <td>
                <span class="status">
                    คืนรถแล้ว
                </span>
            </td>

        </tr>

        <?php } ?>


        <!-- ===== กรณีไม่มีรายการ ===== -->

        <?php if(mysqli_num_rows($summary)==0){ ?>

        <tr>

            <td colspan="7">
                ยังไม่มีรายการเช่าที่เสร็จสิ้น
            </td>

        </tr>

        <?php } ?>

    </table>

    </div>


    <!-- ===== แสดงยอดรวม ===== -->

    <?php if(mysqli_num_rows($summary)>0){ ?>

    <div class="total-box">

        <h3>
            รวมทั้งหมด:
            <?= number_format($total_all); ?> บาท
        </h3>

    </div>

    <?php } ?>


    <!-- ===== ปุ่มกลับหน้าการเช่า ===== -->

    <a href="rentals_customer.php" class="back">

        <button>
            ← กลับหน้าการเช่า
        </button>

    </a>

</div>


<!-- ===== ระบบพูดอัตโนมัติเมื่อเปิดหน้า ===== -->

<script>

window.addEventListener('load', function(){

    setTimeout(function(){

        <?php if(mysqli_num_rows($summary) > 0){ ?>

        speak(
            'กำลังแสดงสรุปการเช่ารถของคุณ มีรายการเช่าที่เสร็จสิ้นแล้ว'
        );

        <?php } else { ?>

        speak(
            'ขณะนี้ยังไม่มีรายการเช่ารถที่เสร็จสิ้น'
        );

        <?php } ?>

    }, 700);

});

</script>

</body>
</html>