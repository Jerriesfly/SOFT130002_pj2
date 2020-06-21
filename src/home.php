<!DOCTYPE html>
<?php
session_start();
require_once("config.php");
setcookie('previous_page', './home.php', time() + 60 * 60 * 24);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/home.css">
</head>
<body>
<?php require_once("navigator.php"); ?>

<div id="front-image">
    <?php
    $sql = "select ImageID from travelimage";//随机获取一个存在的ImageID
    $result = $mysqli->query($sql);
    $number = rand(1, $result->num_rows);
    $sql2 = "select path from travelimage WHERE ImageID='$number'";//查找对应此ID的图片的路径
    $result2 = $mysqli->query($sql2);
    $path_front = $result2->fetch_assoc()['path'];

    echo '<img src="../img/travel-images/large/' . $path_front . '" width="100%" alt="front image">'
    ?>
</div>

<div class="content-wrapper">
    <div id="showImages">
        <?php
        $sql3 = "SELECT ImageID, Count(UID) AS favorCount FROM `travelimagefavor` GROUP BY ImageID ORDER by favorCount DESC";//按收藏数降序排列
        $result3 = $mysqli->query($sql3);

        echo '<div class="row">';
        //依次选择最多6个收藏数最多的图片并获取其ImageID
        for ($i = 0; $i < 6; $i++) {
            if ($favor_image = $result3->fetch_assoc()) {
                $favor_id = $favor_image['ImageID'];
                $sql4 = "SELECT path, Title, Description FROM `travelimage` WHERE ImageID='$favor_id'"; //获取图片信息
                $result4 = $mysqli->query($sql4);
                $img = $result4->fetch_assoc();
                $description = $img['Description'] ? $img['Description'] : 'no description';

                echo '<div class="pic">';
                echo '<a href="detail.php?id=' . $favor_id . '">';
                echo '<p><img src="../img/travel-images/large/' . $img['path'] . '" width="100%" alt="front image"></p>';
                echo '<h3>' . $img['Title'] . '</h3>';
                echo '<p>' . $description . '</p>';
                echo '</a>';
                echo '</div>';

                if ($i == 2) {
                    echo '</div>';
                    echo '<div class="row">';
                }
            }

        }
        echo '</div>';

        ?>
    </div>
</div>

<?php
require_once('footer.php');
require_once('fixed.php');
?>

<script src="./js/jquery.min.js"></script>
<script src="./js/common.js"></script>
<script>
    window.onload = function () {
        document.getElementById('home').className = 'left active';
    }
</script>
</body>
</html>