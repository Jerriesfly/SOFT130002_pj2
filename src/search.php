<!DOCTYPE html>
<?php
session_start();
require_once("config.php");
require_once('pageNumber.php');
setcookie('previous_page', './search.php' . $_SERVER["QUERY_STRING"], time() + 60 * 60 * 24);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/search.css">
</head>
<body>
<?php require_once("navigator.php"); ?>

<div class="content-wrapper">
    <div id="search-bar">
        <?php
        $title_selection = ' selected';
        $title_status = 'active';
        $title_content = '';
        $description_selection = '';
        $description_status = '';
        $description_content = '';

        if (isset($_GET['title'])) {
            $title_content = htmlspecialchars($_GET['title']);
            $result = searchByTitle($mysqli, $title_content);
        } else if (isset($_GET['description'])) {
            $title_selection = '';
            $title_status = '';
            $description_selection = ' selected';
            $description_status = 'active';
            $description_content = htmlspecialchars($_GET['description']);
            $result = searchByDescription($mysqli, $description_content);
        } else {
            $result = justSearch($mysqli);
        }
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
        ?>
        <div class="title">
            <span class="left">Search</span>
            <span id="filter-description" class="right selection<?php echo $description_selection; ?>"
                  onclick="descriptionOnDisplay()">Description</span>
            <span id="filter-title" class="right selection<?php echo $title_selection; ?>" onclick="titleOnDisplay()">Title</span>
            <span class="right">Filter type:</span>
        </div>

        <div class="content">
            <form method="get" id="form-title" class="<?php echo $title_status; ?>">
                <input name="title" id="input-title" type="text" value="<?php echo $title_content; ?>"
                       placeholder="Please enter the title. For example(beach, sea, travel)">
                <input id="submit-title" type="submit" value="Filter">
            </form>

            <form method="get" id="form-description" class="<?php echo $description_status; ?>">
                <textarea name="description" id="input-description"
                          placeholder="Please enter the description."><?php echo $description_content; ?></textarea>
                <input id="submit-description" type="submit" value="filter">
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                $parameters = isset($_GET['title']) ? '?title=' . $title_content . '&page=' :
                    (isset($_GET['description']) ? '?description=' . $description_content . '&page=' : '?page=');
                setCookie('previous_page', './search.php' . $parameters . $current_page, time() + 60 * 60 * 24);
            }
            ?>
        </div>
    </div>

    <div id="search-results">
        <div class="title">
            <span class="left">Results</span>
        </div>

        <div class="content">
            <?php
            if ($total_count = $result->num_rows) {
                setPage(5, $total_count, $current_page);
                $condition = isset($_GET['title']) ? "WHERE Title LIKE '%$title_content%' " :
                    (isset($_GET['description']) ? "WHERE Description LIKE '%$description_content%' " : '');
                $sql = 'SELECT ImageID, Title, Description, Path FROM travelimage ' . $condition . 'limit ' . $mark . ', ' . $page_size;
                $result2 = $mysqli->query($sql);
            }

            if ($total_count && $result2->num_rows) {
            ?>
            <table id="image-view">
                <?php
                while ($row = $result2->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <div class="imgDiv">
                                <a href="detail.php?id=<?php echo $row['ImageID']; ?>">
                                    <img src="../img/travel-images/medium/<?php echo $row['Path']; ?>" alt="img-view">
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="descriptionDiv">
                                <h1><?php echo $row['Title']; ?></h1>
                                <p><?php echo $row['Description']; ?></p>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr id="page-wrapper">
                    <td>
                        <div id="page">
                            <a href="./search.php<?php echo $parameters . $first_page; ?>"><<</a>
                            <a href="./search.php<?php echo $parameters . $pre_page; ?>"><</a>
                            <?php
                            for ($i = 1; $i <= min(5, $total_page); $i++) {
                                if ($i == $current_page) {
                                    echo '<a class="now" href=./search.php' . $parameters . $i . ">$i</a>";
                                } else {
                                    echo '<a href=./search.php' . $parameters . $i . ">$i</a>";
                                }
                            }
                            ?>
                            <a href="./search.php<?php echo $parameters . $next_page; ?>">></a>
                            <a href="./search.php<?php echo $parameters . $last_page; ?>">>></a>
                        </div>
                    </td>
                </tr>
                <?php
                } else {
                    noResult('Sorry, no match is found :(', 350);
                }
                ?>
            </table>
        </div>
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
        document.getElementById('search').className = 'left active';
    }
</script>
<script src="./js/changeFilterType.js"></script>
</body>
</html>