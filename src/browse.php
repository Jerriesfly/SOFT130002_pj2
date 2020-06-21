<!DOCTYPE html>
<?php
session_start();
require_once("config.php");
require_once("pageNumber.php");
setcookie('previous_page', './browse.php', time() + 60 * 60 * 24);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browser</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/browse.css">
</head>
<div>
    <?php require_once("navigator.php"); ?>

    <div class="content-wrapper">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $title = isset($_GET['title']) && $_GET['title'] ? $_GET['title'] : null;
            $content = isset($_GET['content']) && $_GET['content'] ? $_GET['content'] : null;
            $country = isset($_GET['country']) && $_GET['country'] ? $_GET['country'] : null;
            $city = isset($_GET['city']) && $_GET['city'] ? $_GET['city'] : null;
            $current_page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] : 1;
            $parameters = "?title=$title&country=$country&city=$city&page=";
            setCookie('previous_page', './browse.php' . $parameters . $current_page, time() + 60 * 60 * 24);
        }
        ?>

        <aside>
            <div id="search">
                <div class="title">
                    <span>Search by title</span>
                </div>
                <div class="content">
                    <form method="get">
                        <input name="title" type="text" id="title-search"
                               value="<?php echo isset($_GET['title']) ? $_GET['title'] : ''; ?>">
                        <input type="submit" id='search-submit' value="search">
                    </form>
                </div>
            </div>

            <div id="hot-content">
                <div class="title">
                    <span>Hot Content</span>
                </div>
                <div class="content">
                    <p><a href="./browse.php?content=scenery">Scenery</a></p>
                    <p><a href="./browse.php?content=city">City</a></p>
                    <p><a href="./browse.php?content=people">People</a></p>
                    <p><a href="./browse.php?content=animal">Animal</a></p>
                    <p><a href="./browse.php?content=building">Building</a></p>
                    <p><a href="./browse.php?content=wonder">Wonder</a></p>
                </div>
            </div>

            <div id="hot-country">
                <div class="title">
                    <span>Hot Country</span>
                </div>
                <div class="content">
                    <?php
                    $sql_hot_country = "select Country_RegionName FROM geocountries_regions
INNER JOIN travelimage ON travelimage.Country_RegionCodeISO = geocountries_regions.ISO 
INNER JOIN travelimagefavor ON travelimagefavor.ImageID = travelimage.ImageID 
GROUP by ISO Order BY Count(travelimagefavor.favorID) DESC";
                    $result_hot_country = $mysqli->query($sql_hot_country);
                    for ($i = 0; $i < 5; $i++) {
                        if ($row_hot_country = $result_hot_country->fetch_assoc()) {
                            $hot_country = $row_hot_country['Country_RegionName'];
                            echo "<p><a href='./browse.php?country=$hot_country'>$hot_country</a></p>";
                        }
                    }
                    ?>
                </div>
            </div>

            <div id="hot-city">
                <div class="title">
                    <span>Hot City</span>
                </div>
                <div class="content" id="hotCities">
                    <?php
                    if (isset($_GET['country']) && $selected_country = $_GET['country']) {
                        $statement = " WHERE geocountries_regions.Country_RegionName ='$selected_country'";
                    } else {
                        $statement = '';
                    }

                    $sql_hot_city = "select geocities.AsciiName, geocountries_regions.Country_RegionName FROM travelimage 
INNER JOIN geocities ON travelimage.CityCode = geocities.GeoNameID 
INNER JOIN travelimagefavor ON travelimagefavor.ImageID = travelimage.ImageID 
INNER JOIN geocountries_regions on geocountries_regions.ISO = geocities.Country_RegionCodeISO
$statement GROUP by geocities.AsciiName Order by Count(travelimagefavor.favorID) DESC";

                    $result_hot_city = $mysqli->query($sql_hot_city);
                    if ($result_hot_city->num_rows) {
                        for ($i = 0; $i < 5; $i++) {
                            if ($row_hot_city = $result_hot_city->fetch_assoc()) {
                                $hot_country = $row_hot_city['Country_RegionName'];
                                $hot_city = $row_hot_city['AsciiName'];
                                echo "<p><a href='./browse.php?country=$hot_country&city=$hot_city'>$hot_city</a></p>";
                            }
                        }
                    } else {
                        $sql_hot_city = "select geocities.AsciiName, geocountries_regions.Country_RegionName FROM travelimage 
INNER JOIN geocities ON travelimage.CityCode = geocities.GeoNameID 
INNER JOIN travelimagefavor ON travelimagefavor.ImageID = travelimage.ImageID 
INNER JOIN geocountries_regions on geocountries_regions.ISO = geocities.Country_RegionCodeISO
GROUP by geocities.AsciiName Order by Count(travelimagefavor.favorID) DESC";
                        $result_hot_city = $mysqli->query($sql_hot_city);
                        for ($i = 0; $i < 5; $i++) {
                            if ($row_hot_city = $result_hot_city->fetch_assoc()) {
                                $hot_country = $row_hot_city['Country_RegionName'];
                                $hot_city = $row_hot_city['AsciiName'];
                                echo "<p><a href='./browse.php?country=$hot_country&city=$hot_city'>$hot_city</a></p>";
                            }
                        }
                    }

                    ?>
                </div>
        </aside>

        <div id="browser">
            <div class="title">
                <span>Filter</span>
            </div>

            <div class="content">
                <div id="filterDiv">
                    <form>
                        <select name="content" id="select1"></select>
                        <select name="country" id="select2" onchange="setCity(this.selectedIndex)"></select>
                        <select name="city" id="select3"></select>
                        <input type="submit" value="过滤">
                    </form>
                </div>
                <?php
                $where_content = $content ? 'travelimage.Content=\'' . $content . '\' AND ' : null;
                $where_title = $title ? 'travelimage.title LIKE \'%' . $title . '%\' AND ' : null;
                $where_country = $country ? 'geocountries_regions.Country_RegionName=\'' . $country . '\' AND ' : null;
                $where_city = $city ? 'geocities.AsciiName=\'' . $city . '\' AND ' : null;
                $where_end = '1=1';
                $sql = 'SELECT ImageID, Path FROM travelimage 
                    INNER JOIN geocountries_regions ON travelimage.Country_RegionCodeISO = geocountries_regions.ISO 
                    INNER JOIN geocities ON travelimage.CityCode = geocities.GeoNameID WHERE ' .
                    $where_content . $where_title . $where_country . $where_city . '1=1';
                $result = $mysqli->query($sql);

                if ($total_count = $result->num_rows) {
                    setPage(16, $total_count, $current_page);
                    $sql_page = $sql . ' limit ' . $mark . ', ' . $page_size;
                    $result2 = $mysqli->query($sql_page);
                }

                if ($total_count && $result2->num_rows) {
                    ?>
                    <div id="images">
                        <?php
                        while ($row = $result2->fetch_assoc()) {
                            ?>
                            <div>
                                <a href="detail.php?id=<?php echo $row['ImageID']; ?>">
                                    <img src="../img/travel-images/small/<?php echo $row['Path']; ?>" alt="img">
                                </a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <div id="page-wrapper">
                        <div id="page">
                            <a href="./browse.php<?php echo $parameters . $first_page; ?>"><<</a>
                            <a href="./browse.php<?php echo $parameters . $pre_page; ?>"><</a>
                            <?php
                            for ($i = 1;
                                 $i <= min(5, $total_page);
                                 $i++) {
                                if ($i == $current_page) {
                                    echo '<a class="now" href=./browse.php' . $parameters . $i . ">$i</a>";
                                } else {
                                    echo '<a href=./browse.php' . $parameters . $i . ">$i</a>";
                                }
                            }
                            ?>
                            <a href="./browse.php<?php echo $parameters . $next_page; ?>">></a>
                            <a href="./browse.php<?php echo $parameters . $last_page; ?>">>></a>
                        </div>
                    </div>
                    <?php
                } else {
                    noResult('Sorry, no image is found :(', 350);
                }
                ?>
            </div>
        </div>
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
        resizeAndReposition(200, 200, "images", true);
        document.getElementById('search').className = 'left active';
    }
</script>
<?php
require_once('setSelection.php');
?>
</body>
</html>