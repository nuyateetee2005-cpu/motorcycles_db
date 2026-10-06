<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username='$username' LIMIT 1";
    $q = mysqli_query($conn, $sql);

    if (mysqli_num_rows($q) == 1) {
        $row = mysqli_fetch_assoc($q);

        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_username'] = $row['username'];

            header("Location: dashboard_admin.php");
            exit();
        }
    }
    $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

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
    width:380px;
    padding:30px;
    border-radius:14px;
    box-shadow:2px 4px 10px rgba(0,0,0,.2);
    border:1px solid #D2B48C;
}

h2{
    text-align:center;
    color:#8B5E3C;
    margin-bottom:20px;
}

label{
    font-weight:bold;
    color:#4B2E1E;
    font-size:14px;
}

input{
    width:100%;
    padding:10px;
    margin-top:6px;
    margin-bottom:14px;
    border-radius:6px;
    border:1px solid #8B5E3C;
    font-size:15px;
    box-sizing:border-box;
}

input:focus{
    outline:none;
    border-color:#A0522D;
    box-shadow:0 0 6px rgba(139,94,60,.4);
}

button{
    width:100%;
    background:#8B5E3C;
    color:white;
    border:none;
    padding:12px;
    border-radius:6px;
    font-size:16px;
    cursor:pointer;
    margin-top:5px;
}

button:hover{
    background:#A0522D;
}

.error{
    color:red;
    text-align:center;
    margin-bottom:12px;
}
</style>
</head>

<body>

<div class="card">
    <h2>🔐 Admin Login</h2>

    <?php if(isset($error)){ ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">เข้าสู่ระบบ</button>
    </form>
</div>

</body>
</html>