<!DOCTYPE html>
<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/index&register.css">
    <link id="settings" class="original" rel="stylesheet" href="./css/index&register-original.css">
</head>
<body>
<div class="container">
    <img id="icon" class="original" src="../img/icon.png" alt="icon" onclick="changeColor()">
    <h1>Join us now!</h1>
    <form method="post">
        <div id="formTable">
            <?php
            $username_error = '';
            $Email_error = '';
            $password_error = '';
            $repassword_error = '';
            $errno_status = true;
            $salt = base64_encode(random_bytes(32));

            require_once("config.php");

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = test_input($_POST["username"]);
                $Email = test_input($_POST["Email"]);
                $password = test_input($_POST["password"]);
                $repassword = test_input($_POST["repassword"]);
                $saltword = sha1($password . $salt);

                $sql = "select UserName, Email from traveluser where UserName='$username' or Email='$Email'";
                $result = $mysqli->query($sql);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        if ($username == $row['UserName']) {
                            $errno_status = false;
                            $username_error = "该用户名已注册！";
                        }
                        if ($Email == $row['Email']) {
                            $errno_status = false;
                            $Email_error = "该邮箱已注册！";
                        }
                    }
                }

                if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
                    $username_error = "只允许字母、数字和空格！";
                    $errno_status = false;
                } else if (strlen($username) > 10) {
                    $username_error = "用户名过长！";
                    $errno_status = false;
                }

                if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $Email)) {
                    $Email_error = "无效的Email格式！";
                    $errno_status = false;
                }

                if (!preg_match("/\w{8}/", $password) && !preg_match("/\d/", $password)
                    && !preg_match("/[A-Za-z]/", $password)) {
                    $password_error = "密码过弱！适当增加位数并包含数字和字母";
                    $errno_status = false;
                }

                if ($password !== $repassword) {
                    $repassword = "两次密码不匹配！";
                    $errno_status = false;
                }

                if ($errno_status) {
                    $sql2 = "insert into traveluser(UserName,Pass,Email,salt,State) values ('$username','$saltword','$Email','$salt', '1')";
                    $result = $mysqli->query($sql2);

                    if (!$result) {
                        die('Error: ' . $mysqli->error);
                    } else {
                        echo "<script type='text/javascript'>alert('注册成功！即将跳往登录界面');</script>";
                        header("refresh:0;url=./login.php");
                    }
                }
                $mysqli->close();
            }

            function test_input($data)
            {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

            ?>
            <label>
                Username:<br>
                <input name="username" type="text" required>
                <?php echo $username_error ?>
            </label><br>
            <label>
                E-mail:<br>
                <input name="Email" type="text" required>
                <?php echo $Email_error ?>
            </label><br>
            <label>
                Password:<br>
                <input name="password" type="password" required>
                <?php echo $password_error ?>
            </label><br>
            <label>
                Please confirm your password:<br>
                <input name="repassword" type="password" required>
                <?php echo $repassword_error ?>
            </label><br>
            <label>
                <input type="submit" id="submit" value="Sign in">
            </label>
        </div>
    </form>
    <p>Already have an account? <a href="login.php">Click here to login</a></p>
</div>

<footer>
    <p>Copyright © 2019-2021 Web fundamental. All Rights Reserved. 备案号：19302010035</p>
</footer>

<script src="./js/jquery.min.js"></script>
<script src="./js/common.js"></script>
</body>
</html>