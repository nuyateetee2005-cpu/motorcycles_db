<?php
session_start();
include 'db.php';

/* ===== เช็กล็อกอินลูกค้า ===== */

if (!isset($_SESSION['customer_id'])) {

    header("Location: login.php");

    exit();

}

$customer_id = $_SESSION['customer_id'];


/* ==============================
   ข้อมูลลูกค้า
============================== */

$q_customer = mysqli_query($conn,"
    SELECT fullname
    FROM customers
    WHERE customer_id = '$customer_id'
");

$customer = mysqli_fetch_assoc($q_customer);


/* ==============================
   บันทึกการเช่า
============================== */

if (isset($_POST['save'])) {

    $motorcycles_id = $_POST['motorcycles_id'];

    $rent_date = $_POST['rent_date'];

    $return_date = $_POST['return_date'];


    /* ===== คำนวณจำนวนวัน ===== */

    $d1 = new DateTime($rent_date);

    $d2 = new DateTime($return_date);

    $total_date = $d1->diff($d2)->days;


    if ($total_date <= 0) {

        $total_date = 1;

    }


    /* ===== ดึงราคาต่อวัน ===== */

    $q_price = mysqli_query($conn,"
        SELECT price_per_day
        FROM motorcycles
        WHERE motorcycles_id='$motorcycles_id'
    ");

    $price_data = mysqli_fetch_assoc($q_price);

    $price = $price_data['price_per_day'];


    /* ===== คำนวณราคารวม ===== */

    $total_price = $total_date * $price;


    /* ===== บันทึกการเช่า ===== */

    mysqli_query($conn,"
        INSERT INTO rentals
        (
            customers_id,
            motorcycles_id,
            rent_date,
            return_date,
            start_mileage,
            total_date,
            total_price,
            rentals_status
        )

        VALUES
        (
            '$customer_id',
            '$motorcycles_id',
            '$rent_date',
            '$return_date',
            0,
            '$total_date',
            '$total_price',
            'รอแอดมินยืนยัน'
        )
    ");


    echo "
    <script>

        alert('ส่งคำขอเช่าเรียบร้อย');

        location.href='rentals_customer.php';

    </script>
    ";

    exit();

}


/* ==============================
   รถที่ว่าง
============================== */

/*
    รถที่มีสถานะ
    - รอแอดมินยืนยัน
    - กำลังเช่า
    - รอแอดมินยืนยันคืนรถ

    จะไม่แสดงในรายการรถว่าง
*/

$motorcycles = mysqli_query($conn,"
    SELECT *
    FROM motorcycles
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


/* ==============================
   รายการเช่าของลูกค้า
============================== */

$rentals = mysqli_query($conn,"
    SELECT
        r.*,
        m.brand,
        m.model

    FROM rentals r

    JOIN motorcycles m
        ON r.motorcycles_id = m.motorcycles_id

    WHERE r.customers_id = '$customer_id'

    ORDER BY r.rentals_id DESC
");

?>

<!DOCTYPE html>

<html lang="th">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>เช่ามอเตอร์ไซค์</title>


<!-- ===== ระบบเสียง ===== -->

<script src="voice.js"></script>
<script src="voice_command.js"></script>


<style>

body{

    margin:0;

    font-family:Arial, sans-serif;

    background:linear-gradient(
        120deg,
        #FFF5E6,
        #FFEBCD
    );

}


/* =========================
   Header
========================= */

header{

    background:#8B5E3C;

    color:white;

    padding:22px;

    text-align:center;

    font-size:26px;

    font-weight:bold;

}


/* =========================
   Container
========================= */

.container{

    max-width:1100px;

    margin:40px auto;

    background:#FFF8F0;

    padding:35px;

    border-radius:16px;

    border:1px solid #D2B48C;

}


/* =========================
   หัวข้อ
========================= */

h2{

    color:#8B5E3C;

}

h3{

    color:#8B5E3C;

}


/* =========================
   Form
========================= */

form.rent-box{

    background:#FFF;

    padding:20px;

    border-radius:12px;

    border:1px solid #D2B48C;

    margin-bottom:40px;

}


label{

    font-weight:bold;

}


input,
select{

    padding:8px;

    width:100%;

    margin-top:6px;

    margin-bottom:15px;

    box-sizing:border-box;

}


button{

    background:#8B5E3C;

    color:white;

    border:none;

    padding:8px 16px;

    border-radius:6px;

    cursor:pointer;

}


button:hover{

    opacity:0.9;

}


/* =========================
   Table
========================= */

.table-wrapper{

    width:100%;

    overflow-x:auto;

}


table{

    width:100%;

    border-collapse:collapse;

    margin-top:20px;

    min-width:900px;

}


th,
td{

    border:1px solid #D2B48C;

    padding:10px;

    text-align:center;

}


th{

    background:#FFEBCD;

    color:#8B5E3C;

}


/* =========================
   Delete Button
========================= */

.btn-delete{

    background:#C0392B;

}


/* =========================
   Return Button
========================= */

.btn-return{

    background:#D68910;

}


/* =========================
   Status
========================= */

.status{

    display:inline-block;

    padding:6px 10px;

    border-radius:8px;

    color:white;

    font-size:14px;

}


.status-wait{

    background:#E67E22;

}


.status-rent{

    background:#2980B9;

}


.status-return{

    background:#8E44AD;

}


.status-done{

    background:#27AE60;

}


/* =========================
   Voice Button
========================= */

.voice-box{

    text-align:center;

    margin-top:30px;

    margin-bottom:25px;

}


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


/* =========================
   Voice Help
========================= */

.voice-help{

    background:#FFF;

    border:1px solid #D2B48C;

    border-radius:12px;

    padding:15px;

    margin-bottom:30px;

}


.voice-help h3{

    margin-top:0;

}


.voice-help p{

    margin:7px 0;

}


/* =========================
   Empty
========================= */

.empty{

    text-align:center;

    padding:20px;

    color:#777;

}


/* =========================
   Back
========================= */

.back{

    display:inline-block;

    margin-top:25px;

    text-decoration:none;

}


.back button{

    background:#8B5E3C;

}


/* =========================
   Mobile
========================= */

@media(max-width:700px){

    .container{

        margin:20px 10px;

        padding:20px;

    }


    header{

        font-size:21px;

        padding:18px;

    }


    .voice-btn{

        width:100%;

        margin:5px 0;

    }

}

</style>

</head>


<body>


<!-- =========================
     Header
========================= -->

<header>

    🏍️ เช่ารถจักรยานยนต์

</header>


<div class="container">


<!-- =========================
     ข้อมูลลูกค้า
========================= -->

<h2>

    👋 สวัสดีคุณ
    <?= htmlspecialchars($customer['fullname']); ?>

</h2>


<!-- =========================
     ระบบเสียง
========================= -->

<div class="voice-box">

    <button
        class="voice-btn"
        onclick="speak('หน้านี้เป็นหน้าสำหรับเช่ารถจักรยานยนต์ สามารถเลือกประเภทรถ วันที่เช่า และวันที่คืนได้')">

        🔊 อธิบายหน้านี้

    </button>


    <button
        class="voice-btn"
        onclick="startVoiceCommand()">

        🎤 พูดคำสั่ง

    </button>

</div>


<!-- =========================
     ตัวอย่างคำสั่งเสียง
========================= -->

<div class="voice-help">


</div>


<!-- =========================
     ฟอร์มเช่ารถ
========================= -->

<h2>

    🏍️ เลือกรถที่ต้องการเช่า

</h2>


<form
    method="POST"
    class="rent-box"
    onsubmit="return confirm('ยืนยันการส่งคำขอเช่ารถหรือไม่?');"



    <!-- ===== เลือกรถ ===== -->

    <label>

        รถจักรยานยนต์

    </label>


    <select
        name="motorcycles_id"
        required
    >

        <option value="">

            -- เลือกรถ --

        </option>


        <?php

        if(mysqli_num_rows($motorcycles) > 0){

            while($m = mysqli_fetch_assoc($motorcycles)){

        ?>

        <option value="<?= $m['motorcycles_id']; ?>">

            <?= htmlspecialchars($m['brand']); ?>
            <?= htmlspecialchars($m['model']); ?>

            -
            <?= number_format($m['price_per_day']); ?>
            บาท/วัน

        </option>

        <?php

            }

        }else{

        ?>

        <option value="">

            ไม่มีรถว่างในขณะนี้

        </option>

        <?php

        }

        ?>

    </select>


    <!-- ===== วันที่เช่า ===== -->

    <label>

        วันที่เช่า

    </label>


    <input
        type="date"
        name="rent_date"
        id="rent_date"
        required
    >


    <!-- ===== วันที่คืน ===== -->

    <label>

        วันที่คืน

    </label>


    <input
        type="date"
        name="return_date"
        id="return_date"
        required
    >


    <!-- ===== ปุ่มส่งคำขอ ===== -->

    <button
        type="submit"
        name="save"
    >

        🏍️ ส่งคำขอเช่า

    </button>


</form>


<!-- =========================
     รายการเช่าของฉัน
========================= -->

<h2>

    📋 รายการเช่าของฉัน

</h2>


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

    <th>การจัดการ</th>

</tr>


<?php

$no = 1;


if(mysqli_num_rows($rentals) > 0){

    while($r = mysqli_fetch_assoc($rentals)){

?>


<tr>


    <!-- ===== ลำดับ ===== -->

    <td>

        <?= $no++; ?>

    </td>


    <!-- ===== รถ ===== -->

    <td>

        <?= htmlspecialchars($r['brand'].' '.$r['model']); ?>

    </td>


    <!-- ===== วันที่เช่า ===== -->

    <td>

        <?= htmlspecialchars($r['rent_date']); ?>

    </td>


    <!-- ===== วันที่คืน ===== -->

    <td>

        <?= htmlspecialchars($r['return_date']); ?>

    </td>


    <!-- ===== จำนวนวัน ===== -->

    <td>

        <?= htmlspecialchars($r['total_date']); ?>

        วัน

    </td>


    <!-- ===== ราคารวม ===== -->

    <td>

        <?= number_format($r['total_price']); ?>

        บาท

    </td>


    <!-- ===== สถานะ ===== -->

    <td>

        <?php

        if($r['rentals_status'] == 'รอแอดมินยืนยัน'){

        ?>

            <span class="status status-wait">

                รอแอดมินยืนยัน

            </span>

        <?php

        }elseif($r['rentals_status'] == 'กำลังเช่า'){

        ?>

            <span class="status status-rent">

                กำลังเช่า

            </span>

        <?php

        }elseif($r['rentals_status'] == 'รอแอดมินยืนยันคืนรถ'){

        ?>

            <span class="status status-return">

                รอแอดมินยืนยันคืนรถ

            </span>

        <?php

        }elseif($r['rentals_status'] == 'คืนแล้ว'){

        ?>

            <span class="status status-done">

                คืนรถแล้ว

            </span>

        <?php

        }else{

        ?>

            <span class="status status-wait">

                <?= htmlspecialchars($r['rentals_status']); ?>

            </span>

        <?php

        }

        ?>

    </td>


    <!-- =========================
         การจัดการ
    ========================= -->

    <td>


        <?php

        /*
            ลูกค้าสามารถขอคืนรถได้
            เมื่อสถานะเป็น "กำลังเช่า"
        */

        if($r['rentals_status'] == 'กำลังเช่า'){

        ?>

        <form
            method="POST"
            action="request_return.php"
            style="display:inline;"
            onsubmit="return confirm('ต้องการแจ้งขอคืนรถคันนี้หรือไม่?');"
        >

            <input
                type="hidden"
                name="rentals_id"
                value="<?= $r['rentals_id']; ?>"
            >

            <button
                type="submit"
                class="btn-return"
            >

                🔄 ต้องการคืนรถ

            </button>

        </form>


        <?php

        }


        /*
            รายการที่ยังไม่เริ่มเช่า
            สามารถลบคำขอได้
        */

        if(
            $r['rentals_status'] != 'กำลังเช่า'
            &&
            $r['rentals_status'] != 'รอแอดมินยืนยันคืนรถ'
            &&
            $r['rentals_status'] != 'คืนแล้ว'
        ){

        ?>

        <form
            method="POST"
            action="delete_rental.php"
            style="display:inline;"
            onsubmit="return confirm('ต้องการยกเลิกรายการเช่านี้หรือไม่?');"
        >

            <input
                type="hidden"
                name="rentals_id"
                value="<?= $r['rentals_id']; ?>"
            >

            <button
                type="submit"
                class="btn-delete"
            >

                🗑 ลบ

            </button>

        </form>

        <?php

        }


        /*
            ถ้าไม่มีปุ่มใด ๆ
        */

        if(
            $r['rentals_status'] == 'รอแอดมินยืนยันคืนรถ'
            ||
            $r['rentals_status'] == 'คืนแล้ว'
        ){

        ?>

            <span style="color:#777;">

                -

            </span>

        <?php

        }

        ?>

    </td>


</tr>


<?php

    }

}else{

?>


<tr>

    <td
        colspan="8"
        class="empty"
    >

        ยังไม่มีรายการเช่ารถจักรยานยนต์

    </td>

</tr>


<?php

}

?>


</table>

</div>


<!-- =========================
     ปุ่มกลับหน้าหลัก
========================= -->

<a
    href="dashboard_customer.php"
    class="back"


    <button>

        ← กลับหน้าหลัก

    </button>

</a>


</div>


<!-- =========================
     ระบบกำหนดวันที่
========================= -->

<script>

document.addEventListener('DOMContentLoaded', function(){

    const rentDate =
        document.getElementById('rent_date');

    const returnDate =
        document.getElementById('return_date');


    /* ===== วันที่เช่าเริ่มต้นเป็นวันนี้ ===== */

    const today =
        new Date().toISOString().split('T')[0];


    rentDate.min = today;


    /* ===== เมื่อเลือกวันที่เช่า ===== */

    rentDate.addEventListener('change', function(){

        returnDate.min = rentDate.value;


        if(returnDate.value < rentDate.value){

            returnDate.value = rentDate.value;

        }

    });

});

</script>


<!-- =========================
     ระบบพูดอัตโนมัติ
========================= -->

<script>

window.addEventListener('load', function(){

    setTimeout(function(){

        speak(
            'เข้าสู่หน้าการเช่ารถจักรยานยนต์ สามารถเลือกรถจักรยานยนต์ วันที่เช่ารถจักรยานยนต์ และวันที่คืนรถจักรยานยนต์'
        );

    }, 700);

});

</script>


</body>

</html>