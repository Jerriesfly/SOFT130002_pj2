<!DOCTYPE html>
<?php
session_start();
require_once("config.php");
require_once("pageNumber.php");
setcookie('previous_page', './home.php', time() + 60 * 60 * 24);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Photos</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/myPhotos&Favourites.css">
</head>
<body>
<?php require_once("navigator.php"); ?>

<div class="content-wrapper">
    <div class="title">
        <span class="left">My photos</span>
    </div>

    <div class="content">
        <?php
        if (!isset($_SESSION['UID'])) {
            noResult('Please login first!', 460);
        } else {
            $UID = $_SESSION['UID'];
            $sql_search = "SELECT ImageID FROM travelimage WHERE UID='" . $_SESSION['UID'] . "'";
            $result_search = $mysqli->query($sql_search);

            if ($total_count = $result_search->num_rows) {
                $current_page = !isset($_GET['page']) ? 1 : $_GET['page'];
                setPage(5, $total_count, $current_page);
                ?>
                <table id="image-view">
                <?php
                $sql_photo = 'SELECT ImageID, Title, Description, Path FROM travelimage
 WHERE UID= ' . $UID . ' limit ' . $mark . ', ' . $page_size;
                $result_photo = $mysqli->query($sql_photo);

                if ($result_photo->num_rows) {
                    while ($rows = $result_photo->fetch_assoc()) {
                        ?>
                        <tr>
                            <td>
                                <div class="imgDiv">
                                    <a href="./detail.php?id=<?php echo $rows['ImageID']; ?>">
                                        <img src="../img/travel-images/small/<?php echo $rows['Path']; ?>" alt="img-view">
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="descriptionDiv">
                                    <h1><?php echo $rows['Title']; ?></h1>
                                    <p><?php echo $rows['Description']; ?></p>
                                    <a href="./upload.php?id=<?php echo $rows['ImageID']; ?>">
                                        <button class="modify" value="modify">Modify</button>
                                    </a>
                                    <button class="delete" value="delete"
                                            onclick="toDelete(<?php echo $rows['ImageID']; ?>)">Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                    <tr id="page-wrapper">
                        <td>
                            <div id="page">
                                <a href="./search.php<?php echo $first_page; ?>"><<</a>
                                <a href="./search.php<?php echo $pre_page; ?>"><</a>
                                <?php
                                for ($i = 1; $i <= min(5, $total_page); $i++) {
                                    if ($i == $current_page) {
                                        echo '<a class="now" href=./myPhotos.php?page=' . $i . ">$i</a>";
                                    } else {
                                        echo '<a href=./myPhotos.php?page=' . $i . ">$i</a>";
                                    }
                                }
                                ?>
                                <a href="./search.php<?php echo $next_page; ?>">></a>
                                <a href="./search.php<?php echo $last_page; ?>">>></a>
                            </div>
                        </td>
                    </tr>
                    </table>
                    <?php
                } else {
                    header('refresh:0;url=./myFavourites.php?=' . $pre_page);
                }
            } else {
                noResult("why not upload some photo now!", 460);
            }
        }
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
        resizeAndReposition(300, 300, "image-view", true);
    }
</script>
<script>
    function toDelete(ImageID) {
        const sure = confirm("Are you sure to delete this photo?");
        if (sure) {
            let xmlhttp;
            if (window.XMLHttpRequest) {
                // IE7+, Firefox, Chrome, Opera, Safari 浏览器执行代码
                xmlhttp = new XMLHttpRequest();
            } else {
                // IE6, IE5 浏览器执行代码
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    window.location.reload();
                }
            };
            xmlhttp.open("POST", "./deletePhoto.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send('ImageID=' + ImageID);
        }
    }
</script>
</body>
</html>