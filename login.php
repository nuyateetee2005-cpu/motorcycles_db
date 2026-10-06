<?php
session_start();
include 'db.php';

if(isset($_POST['login'])){

    $role = $_POST['role']; // customer | admin

    /* ===== LOGIN ลูกค้า ===== */
    if($role == 'customer'){
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $password = $_POST['password'];

        $sql = mysqli_query($conn,"
            SELECT * FROM customers 
            WHERE fullname='$fullname' AND password='$password'
            LIMIT 1
        ");

        if(mysqli_num_rows($sql)==1){
            $row = mysqli_fetch_assoc($sql);
            $_SESSION['customer_id'] = $row['customer_id'];
            $_SESSION['fullname']    = $row['fullname'];
            header("Location: dashboard_customer.php");
            exit();
        }else{
            $error = "ชื่อหรือรหัสผ่านลูกค้าไม่ถูกต้อง";
        }
    }

    /* ===== LOGIN แอดมิน ===== */
    if($role == 'admin'){
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        $sql = mysqli_query($conn,"
            SELECT * FROM admins 
            WHERE username='$username'
            LIMIT 1
        ");

        if(mysqli_num_rows($sql)==1){
            $row = mysqli_fetch_assoc($sql);
            if(password_verify($password,$row['password'])){
                $_SESSION['admin_id']       = $row['admin_id'];
                $_SESSION['admin_username'] = $row['username'];
                header("Location: dashboard_admin.php");
                exit();
            }
        }
        $error = "ชื่อผู้ใช้หรือรหัสผ่านแอดมินไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(120deg,#FFF5E6,#FFEBCD);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}
.card{
    background:#FFF8F0;
    width:420px;
    padding:30px;
    border-radius:14px;
    border:1px solid #D2B48C;
    box-shadow:2px 4px 10px rgba(0,0,0,.2);
}
h2{
    text-align:center;
    color:#8B5E3C;
}
.role{
    display:flex;
    justify-content:center;
    gap:20px;
    margin:15px 0;
}
input,select{
    width:100%;
    padding:10px;
    margin:8px 0 14px;
    border-radius:6px;
    border:1px solid #8B5E3C;
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
    margin-bottom:10px;
}
.link{
    text-align:center;
    margin-top:12px;
}
a{
    color:#8B5E3C;
    font-weight:bold;
    text-decoration:none;
}
</style>

<script>
function switchRole(){
    let role = document.getElementById('role').value;
    document.getElementById('customer').style.display = (role=='customer')?'block':'none';
    document.getElementById('admin').style.display    = (role=='admin')?'block':'none';
}
</script>
</head>

<body>

<div class="card">
<h2>🔐 Login</h2>

<?php if(isset($error)){ ?>
    <div class="error"><?= $error ?></div>
<?php } ?>

<form method="post">
    <label>เข้าสู่ระบบในฐานะ</label>
    <select name="role" id="role" onchange="switchRole()" required>
        <option value="customer">ลูกค้า</option>
        <option value="admin">แอดมิน</option>
    </select>

    <!-- ลูกค้า -->
    <div id="customer">
        <input name="fullname" placeholder="ชื่อผู้ใช้">
    </div>

    <!-- แอดมิน -->
    <div id="admin" style="display:none;">
        <input name="username" placeholder="Username แอดมิน">
    </div>

    <input type="password" name="password" placeholder="รหัสผ่าน" required>

    <button name="login">เข้าสู่ระบบ</button>
</form>

<div class="link">
    <a href="register.php">สมัครสมาชิก (ลูกค้า)</a>
</div>
</div>

</body>
</html>