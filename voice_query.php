<?php

include 'db.php';

header('Content-Type: application/json; charset=utf-8');


/* =====================================
   รับคำถามจาก voice_command.js
   ===================================== */

$query = isset($_GET['query'])
    ? trim($_GET['query'])
    : '';


/* =====================================
   รับชื่อรุ่นรถ ถ้ามี
   ===================================== */

$model_query = isset($_GET['model'])
    ? trim($_GET['model'])
    : '';


/* =====================================
   รับราคาสูงสุด
   เช่น 200
   ===================================== */

$max_price = null;

if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {

    $max_price = (float)$_GET['max_price'];

}


/* =====================================
   ตรวจสอบการเชื่อมต่อฐานข้อมูล
   ===================================== */

if (!$conn) {

    echo json_encode([
        "success" => false,
        "message" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"
    ], JSON_UNESCAPED_UNICODE);

    exit();

}


/* =====================================
   เงื่อนไขรถที่ถือว่า "ไม่ว่าง"
   
   รถที่มีสถานะเหล่านี้
   จะไม่นับเป็นรถว่าง
   ===================================== */

$unavailable_status = "
    'รอแอดมินยืนยัน',
    'กำลังเช่า',
    'รอคืนรถ',
    'รอแอดมินยืนยันคืนรถ'
";


/* =====================================
   ตัวแปรสำหรับส่งผลลัพธ์
   ===================================== */

$total = 0;

$models = [];

$model_found = false;

$model_available = 0;

$model_price = 0;

$model_name = "";

$price_motorcycles = [];


/* =====================================
   แปลงชื่อรุ่นที่ระบบอาจจับเสียงผิด
   
   เช่น
   PXC → PCX
   พีซีเอ็กซ์ → PCX
   คลิก → Click
   จีออโน่ → Giorno
   ===================================== */

function normalizeModelName($model)
{

    $model = trim($model);

    $lower = strtolower($model);


    /* PCX */

    if (
        strpos($lower, 'pcx') !== false ||
        strpos($lower, 'pxc') !== false ||
        strpos($model, 'พีซีเอ็กซ์') !== false ||
        strpos($model, 'พีซีเอ็ก') !== false
    ) {

        return 'PCX';

    }


    /* Click */

    if (
        strpos($lower, 'click') !== false ||
        strpos($model, 'คลิก') !== false ||
        strpos($model, 'คลิ๊ก') !== false
    ) {

        return 'Click';

    }


    /* Giorno */

    if (
        strpos($lower, 'giorno') !== false ||
        strpos($model, 'จีออโน่') !== false ||
        strpos($model, 'จีออโน') !== false
    ) {

        return 'Giorno';

    }


    return $model;

}


/* =====================================
   ทำความสะอาดชื่อรุ่น
   ===================================== */

if ($model_query !== '') {

    $model_query =
        normalizeModelName($model_query);

}


/* =====================================
   ทำความสะอาดข้อความคำถาม
   ===================================== */

$normalized_query =
    strtolower($query);

$normalized_query =
    str_replace(
        [
            'พีซีเอ็กซ์',
            'พีซีเอ็ก',
            'pxc'
        ],
        'pcx',
        $normalized_query
    );

$normalized_query =
    str_replace(
        [
            'คลิ๊ก',
            'คลิก'
        ],
        'click',
        $normalized_query
    );

$normalized_query =
    str_replace(
        [
            'จีออโน่',
            'จีออโน'
        ],
        'giorno',
        $normalized_query
    );


/* =====================================
   ถ้าไม่ได้ส่ง model มา
   ให้ลองค้นหารุ่นจาก query
   ===================================== */

if ($model_query === '' && $normalized_query !== '') {

    if (strpos($normalized_query, 'pcx') !== false) {

        $model_query = 'PCX';

    }

    elseif (
        strpos($normalized_query, 'click') !== false
    ) {

        $model_query = 'Click';

    }

    elseif (
        strpos($normalized_query, 'giorno') !== false
    ) {

        $model_query = 'Giorno';

    }

}


/* =====================================
   1. นับจำนวนรถที่ว่างทั้งหมด
   ===================================== */

$sql = "
    SELECT COUNT(*) AS total
    FROM motorcycles m
    WHERE NOT EXISTS (
        SELECT 1
        FROM rentals r
        WHERE r.motorcycles_id = m.motorcycles_id
        AND r.rentals_status IN (
            $unavailable_status
        )
    )
";


$result = mysqli_query($conn, $sql);


if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => "ไม่สามารถดึงข้อมูลรถได้",
        "error" => mysqli_error($conn)
    ], JSON_UNESCAPED_UNICODE);

    exit();

}


$row = mysqli_fetch_assoc($result);

$total = (int)$row['total'];


/* =====================================
   2. นับจำนวนรถว่างแยกตามรุ่น
   ===================================== */

$sql_model = "
    SELECT 
        m.model,
        COUNT(*) AS total
    FROM motorcycles m
    WHERE NOT EXISTS (
        SELECT 1
        FROM rentals r
        WHERE r.motorcycles_id = m.motorcycles_id
        AND r.rentals_status IN (
            $unavailable_status
        )
    )
    GROUP BY m.model
    ORDER BY m.model
";


$result_model =
    mysqli_query($conn, $sql_model);


if ($result_model) {

    while (
        $row = mysqli_fetch_assoc($result_model)
    ) {

        $models[] = [

            "model" => $row['model'],

            "total" =>
                (int)$row['total']

        ];

    }

}


/* =====================================
   3. ถ้าระบุรุ่นรถ
   เช่น
   
   PCX
   Click
   Giorno
   
   ===================================== */

if ($model_query !== '') {


    $safe_model =
        mysqli_real_escape_string(
            $conn,
            $model_query
        );


    /* =====================================
       ค้นหาข้อมูลรุ่นรถ
       ===================================== */

    $sql_specific_model = "
        SELECT 
            m.model,
            MIN(m.price_per_day) AS price_per_day,
            COUNT(*) AS total_motorcycles
        FROM motorcycles m
        WHERE LOWER(m.model)
        LIKE LOWER('%$safe_model%')
        GROUP BY m.model
        ORDER BY m.model
    ";


    $result_specific_model =
        mysqli_query(
            $conn,
            $sql_specific_model
        );


    if ($result_specific_model) {


        while (
            $specific =
            mysqli_fetch_assoc(
                $result_specific_model
            )
        ) {


            $model_found = true;


            $model_name =
                $specific['model'];


            $model_price =
                (float)$specific['price_per_day'];


            /* =====================================
               นับเฉพาะรถรุ่นนี้ที่ว่าง
               ===================================== */

            $safe_specific_model =
                mysqli_real_escape_string(
                    $conn,
                    $specific['model']
                );


            $sql_available_model = "
                SELECT COUNT(*) AS total
                FROM motorcycles m
                WHERE m.model = '$safe_specific_model'
                AND NOT EXISTS (
                    SELECT 1
                    FROM rentals r
                    WHERE r.motorcycles_id =
                          m.motorcycles_id
                    AND r.rentals_status IN (
                        $unavailable_status
                    )
                )
            ";


            $result_available_model =
                mysqli_query(
                    $conn,
                    $sql_available_model
                );


            if ($result_available_model) {


                $available_row =
                    mysqli_fetch_assoc(
                        $result_available_model
                    );


                $model_available =
                    (int)$available_row['total'];

            }


            /*
             * หยุดหลังจากเจอรุ่นแรก
             * เพราะชื่อรุ่นเป็นข้อมูลหลักที่ต้องการ
             */

            break;

        }

    }

}


/* =====================================
   4. ค้นหารถตามราคาสูงสุด
   เช่น
   
   รถราคาไม่เกิน 200 บาท
   รถไม่เกิน 300 บาท
   
   ===================================== */

if ($max_price !== null) {


    $safe_max_price =
        (float)$max_price;


    $sql_price = "
        SELECT
            m.model,
            m.brand,
            m.price_per_day
        FROM motorcycles m
        WHERE m.price_per_day <= $safe_max_price

        AND NOT EXISTS (
            SELECT 1
            FROM rentals r
            WHERE r.motorcycles_id =
                  m.motorcycles_id
            AND r.rentals_status IN (
                $unavailable_status
            )
        )

        ORDER BY
            m.price_per_day ASC,
            m.model ASC
    ";


    $result_price =
        mysqli_query(
            $conn,
            $sql_price
        );


    if ($result_price) {


        while (
            $price_row =
            mysqli_fetch_assoc(
                $result_price
            )
        ) {


            $price_motorcycles[] = [

                "brand" =>
                    $price_row['brand'],

                "model" =>
                    $price_row['model'],

                "price_per_day" =>
                    (float)$price_row['price_per_day']

            ];

        }

    }

}


/* =====================================
   5. ถ้าถามเรื่องรถว่าง
   ส่งข้อมูลเพิ่มเติมให้ JavaScript
   ===================================== */

$has_available_model = false;


if ($model_found && $model_available > 0) {

    $has_available_model = true;

}


/* =====================================
   6. ส่งข้อมูลกลับให้ voice_command.js
   ===================================== */

echo json_encode([

    "success" => true,

    /* จำนวนรถว่างทั้งหมด */
    "total" => $total,

    /* จำนวนรถว่างแยกตามรุ่น */
    "models" => $models,

    /* ข้อมูลรุ่นที่ค้นหา */
    "model_found" => $model_found,

    "model_name" => $model_name,

    "model_available" => $model_available,

    "model_price" => $model_price,

    /* ตรวจว่ารุ่นนั้นมีรถว่างหรือไม่ */
    "has_available_model" =>
        $has_available_model,

    /* ข้อมูลรถตามราคา */
    "max_price" => $max_price,

    "price_motorcycles" =>
        $price_motorcycles

], JSON_UNESCAPED_UNICODE);

?>