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
    <title>My Favourites</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/myPhotos&Favourites.css">
</head>
<body>
<?php require_once("navigator.php"); ?>

<div class="content-wrapper">
    <div class="title">
        <span class="left">My favourites</span>
    </div>

    <div class="content">
        <?php
        if (!isset($_SESSION['UID'])) {
            noResult('Please login first!', 460);
        } else {
            $UID = $_SESSION['UID'];
            $sql_search = "SELECT ImageID FROM travelimagefavor WHERE UID='" . $_SESSION['UID'] . "'";
            $result_search = $mysqli->query($sql_search);

            if ($total_count = $result_search->num_rows) {
                $current_page = !isset($_GET['page']) ? 1 : $_GET['page'];
                setPage(5, $total_count, $current_page);
                ?>
                <table id="image-view">
                <?php
                $sql_favourite = 'SELECT travelimage.ImageID, Title, Description, Path, travelimagefavor.FavorID FROM travelimage
 INNER JOIN travelimagefavor ON travelimagefavor.ImageID = travelimage.ImageID
 WHERE travelimagefavor.UID= ' . $UID . ' limit ' . $mark . ', ' . $page_size;
                $result_favourite = $mysqli->query($sql_favourite);

                if ($result_favourite->num_rows) {
                    while ($rows = $result_favourite->fetch_assoc()) {
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
                                    <button class="delete" value="delete"
                                            onclick="toDelete(<?php echo $rows['FavorID']; ?>)">Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                    <tr id="page-wrapper">
                        <td>
                            <div id="page-wrapper">
                                <div id="page">
                                    <a href="./myFavourites.php?page=<?php echo $first_page; ?>"><<</a>
                                    <a href="./myFavourites.php?page=<?php echo $pre_page; ?>"><</a>
                                    <?php
                                    for ($i = 1;
                                         $i <= min(5, $total_page);
                                         $i++) {
                                        if ($i == $current_page) {
                                            echo '<a class="now" href=./myFavourites.php?page=' . $i . ">$i</a>";
                                        } else {
                                            echo '<a href=./myFavourites.php?page=' . $i . ">$i</a>";
                                        }
                                    }
                                    ?>
                                    <a href="./myFavourites.php?page=<?php echo $next_page; ?>">></a>
                                    <a href="./myFavourites.php?page=<?php echo $last_page; ?>">>></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </table>
                    <?php
                } else {
                    header('refresh:0;url=./myFavourites.php?page=' . $pre_page);
                }
            } else {
                noResult("why not add some to your favourite now!", 460);
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
    function toDelete(FavorID) {
        const sure = confirm("Are you sure to remove it from your favourites?");
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
            xmlhttp.open("POST", "deleteFavourite.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send('FavorID=' + FavorID);
        }
    }
</script>
</body>
</html>