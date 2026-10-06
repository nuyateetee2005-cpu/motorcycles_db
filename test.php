<?php
$hash = '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; 
// 👆 เอา password จาก phpMyAdmin มาใส่ตรงนี้

if (password_verify('1234', $hash)) {
    echo "รหัสถูก";
} else {
    echo "รหัสผิด";
}