<!DOCTYPE html>
<?php
session_start();
require_once("config.php");
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/index&register.css">
    <link id="settings" class="original" rel="stylesheet" href="./css/index&register-original.css">
</head>
<body>
<div class="container">
    <img id="icon" class="original" src="../img/icon.png" alt="icon" onclick="changeColor()">
    <h1>Welcome to gayhub!</h1>
    <form method="post">
        <div id="formTable">
            <label>
                Username/Email:<br>
                <input name="username" type="text" required>
            </label><br>
            <label>
                Password:<br>
                <input name="password" type="password" required>
            </label><br>
            <label>
                <input type="submit" id="submit" value="Sign in">
            </label><br>
            <span>
            <?php
            $error = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = test_input($_POST["username"]);
                $password = test_input($_POST["password"]);
                if (preg_match("/@/", $username)) {
                    $sql = "select UID, Pass, salt from traveluser where Email='$username'";
                } else {
                    $sql = "select UID, Pass, salt from traveluser where UserName='$username'";
                }
                $result = $mysqli->query($sql);

                if ($rows = $result->fetch_assoc()) {
                    if (sha1($password . $rows['salt']) == $rows['Pass']) {
                        $_SESSION['UID'] = $rows['UID'];
                        if (!isset($_COOKIE['previous_page'])) {
                            header('refresh:0;url=./home.php');
                        } else {
                            header('refresh:0;url=' . $_COOKIE['previous_page']);
                        }
                    } else {
                        $error = "用户名或密码错误";
                    }
                } else {
                    $error = "用户名或密码错误";
                }
                $mysqli->close();
            }
            echo $error;

            function test_input($data)
            {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

            ?>
                </span>
        </div>
    </form>
    <p>New to gayhub? Why not create one! <a href="register.php">Create an account</a></p>
</div>

<footer>
    <p>Copyright © 2019-2021 Web fundamental. All Rights Reserved. 备案号：19302010035</p>
</footer>

<script src="./js/jquery.min.js"></script>
<script src="./js/common.js"></script>
</body>
</html>