<!DOCTYPE html>
<?php
session_start();
require_once("config.php");
require_once('deleteFavourite.php');
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/detail.css">
</head>
<body>
<?php require_once("navigator.php"); ?>

<div class="content-wrapper">
    <div class="title">
        <span>Details</span>
    </div>

    <div class="content">
        <?php

        if (isset($_GET['id'])) {
            setcookie('previous_page', './detail.php?id=' . $_GET['id'], time() + 60 * 60 * 24);
            $id = $_GET['id'];
            $sql_information = "select Title, Description, Path, Content, traveluser.UserName, geocountries_regions.Country_RegionName, geocities.AsciiName,
Count(travelimagefavor.favorID) AS numFavored FROM travelimage 
INNER JOIN traveluser ON traveluser.UID = travelimage.UID
INNER JOIN geocountries_regions ON travelimage.Country_RegionCodeISO = geocountries_regions.ISO 
INNER JOIN geocities ON travelimage.CityCode = geocities.GeoNameID 
INNER JOIN travelimagefavor ON travelimagefavor.ImageID = travelimage.ImageID 
WHERE travelimage.ImageID ='$id'";
            $result_information = $mysqli->query($sql_information);
            $image = $result_information->fetch_assoc();

            if ($image['Path']) {
                ?>
                <div id="content-title">
                    <p>
                        <span id="title"><?php echo $image['Title'] ?></span>
                        <span id="uploader">by <?php echo $image['UserName'] ?></span>
                    </p>
                </div>

                <div id="content-information">
                    <div id="overview">
                        <img src="../img/travel-images/medium/<?php echo $image['Path'] ?>" alt="img">
                    </div>

                    <div id="information-wrapper">
                        <div id="like">
                            <div class="title">
                                <span>Like number</span>
                            </div>

                            <div class="content">
                                <span><?php echo $image['numFavored'] ?></span>
                            </div>
                        </div>

                        <div id="detail">
                            <div class="title">
                                <span>Image details</span>
                            </div>

                            <div class="content">
                                <p>Content:<span class="type"><?php echo $image['Content'] ?></span></p>
                                <p>Country:<span class="country"><?php echo $image['Country_RegionName'] ?></span></p>
                                <p>City:<span class="city"><?php echo $image['AsciiName'] ?></span></p>
                            </div>
                        </div>

                        <form id="form" method="post">
                            <?php
                            if (!isset($_SESSION['UID'])) {
                                echo '<input type="submit" id="favourite"  value="Please login first">';

                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    header("refresh:0;url=./login.php");
                                }
                            } else {
                                $sql_search = "SELECT ImageID, UID FROM travelimagefavor WHERE UID='" . $_SESSION['UID'] . "' AND ImageID='" . $_GET['id'] . "'";
                                $result_search = $mysqli->query($sql_search);

                                if ($row = $result_search->fetch_assoc()) {
                                    echo '<input type="submit" id="favourite" class="favored" value="Remove from favorite">';
                                } else {
                                    echo '<input type="submit" id="favourite" value="Add to favorite">';
                                }

                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    if ($row) {
                                        $UID = $row['UID'];
                                        $imgID = $row['ImageID'];
                                        $sql_remove = "DELETE FROM travelimagefavor WHERE UID='$UID' AND ImageID='$imgID'";
                                        $sql_drop = " ALTER  TABLE travelimagefavor DROP FavorID";
                                        $sql_create = "ALTER TABLE travelimagefavor ADD FavorID INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT FIRST";
                                        $mysqli->query($sql_remove);
                                        $mysqli->query($sql_drop);
                                        $mysqli->query($sql_create);
                                    } else {
                                        $UID = $_SESSION['UID'];
                                        $sql_user = "SELECT UID FROM traveluser WHERE UID='$UID'";
                                        $result_user = $mysqli->query($sql_user);
                                        $UID = $result_user->fetch_assoc()['UID'];
                                        $imageID = $_GET['id'];
                                        $sql_add = "INSERT INTO travelimagefavor(UID, ImageID) values ('$UID', '$imageID')";
                                        $mysqli->query($sql_add);
                                    }
                                    header("refresh:0");
                                }
                            }
                            ?>
                        </form>
                    </div>
                </div>

                <div id="content-description">
                    <p><?php echo $image['Description'] ?></p>
                </div>

                <?php
            } else {
                noResult('sorry, there is no such image :(', 420);
            }
        } else {
            setcookie('previous_page', './detail.php?id=', time() + 60 * 60 * 24);
            noResult('sorry, no ID is found!', 420);
        }
        ?>
    </div>
</div>

<?php
require_once('footer.php');
require_once('fixed.php');
?>

<script src="js/jquery.min.js"></script>
<script src="js/common.js"></script>
<script>
    window.onload = function () {
        resizeAndReposition(400, 400, "overview", false);
    }
</script>
</body>
</html>