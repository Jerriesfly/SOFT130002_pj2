<!DOCTYPE html>
<?php
session_start();
require_once("config.php");
setcookie('previous_page', './home.php', time() + 60 * 60 * 24);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/upload.css">
</head>
<body>
<?php require_once("navigator.php"); ?>

<div class="content-wrapper" id="wrapper">
    <div class="title">
        <span>Upload</span>
    </div>

    <?php
    if(!isset($_SESSION['UID'])){
        echo "<script type='text/javascript'>alert('Please login first!');location='javascript:history.back()';</script>";
    } else {
        if (isset($_GET['id'])) {
            $ImageID = $_GET['id'];
            $sql_verify = "select Title, Description, Path, Content, UID, 
geocountries_regions.Country_RegionName, geocities.AsciiName FROM travelimage 
INNER JOIN geocountries_regions ON travelimage.Country_RegionCodeISO = geocountries_regions.ISO 
INNER JOIN geocities ON travelimage.CityCode = geocities.GeoNameID WHERE travelimage.ImageID ='$ImageID'";
            $result_verify = $mysqli->query($sql_verify);
            if ($row = $result_verify->fetch_assoc()) {
                if ($row['UID'] === $_SESSION['UID']) {
                    $is_modify = true;
                    $title = $row['Title'];
                    $description = $row['Description'];
                    $path = $row['Path'];
                    $content = $row['Content'];
                    $country = $row['Country_RegionName'];
                    $city = $row['AsciiName'];
                } else {
                    echo "<script type='text/javascript'>alert('you cannot modify this image！');location='javascript:history.back()';</script>";
                }
            } else {
                echo "<script type='text/javascript'>alert('There is no such image！');location='javascript:history.back()';</script>";
            }
        } else {
            $is_modify = false;
            $ImageID = 0;
        }
    }
    ?>
    <div class="content">
        <form onsubmit="return checkForm()" method="post"
              action="storeImage.php?ImageID=<?php echo $ImageID; ?>" enctype="multipart/form-data">
            <div id="preview">
                <?php
                echo $is_modify ? "<img src='../img/travel-images/medium/$path' width='400px' alt='img'>"
                    : "<span>Photo not uploaded yet.</span>";
                ?>
            </div>

            <div id="upload">
                <label for="file0" id="label-file"><?php echo $is_modify ? 'modify' : 'upload' . ' photo'; ?></label>
                <input type="file" name="file0" id="file0" accept="image/*" onchange="uploadImg(document,this)" hidden required>
            </div>

            <div id="information">
                <p>Title of the photo:</p>
                <input name="title" type="text" id="title" value="<?php echo $is_modify? $title : ''; ?>" required>
                <p>Description of the photo:</p>
                <textarea name="description" id="description" required><?php echo $is_modify? $description : ''; ?></textarea>
                <select name="content" id="select1"></select>
                <select name="country" id="select2" onchange="setCity(this.selectedIndex)"></select>
                <select name="city" id="select3"></select>
                <input type="submit" id="submit" value="Submit">
            </div>
        </form>

    </div>
</div>

<?php
require_once('footer.php');
require_once('fixed.php');
?>

<script src="./js/jquery.min.js"></script>
<script src="./js/common.js"></script>
<script>
    const URL = window.URL || window.webkitURL || window.mozURL;

    function uploadImg(e, dom) {
        //根据浏览器不同设置不同获取对象方式
        const fileObj = dom instanceof HTMLElement ? dom.files[0] : $(dom)[0].files[0];
        const container = document.querySelector('#preview');
        const img = new Image();
        img.src = URL.createObjectURL(fileObj);
        //清空容器内之前的内容（包括文字提示）并加入上传的图片，调整图片大小适应宽高
        img.onload = function () {
            container.innerHTML = "";
            container.appendChild(img);
            resizeAndReposition(400, 300, "preview", false);
        }
    }
</script>
<?php
require_once('setSelection.php');
?>
<script>
    function checkForm() {
        if (content.selectedIndex && content.selectedIndex && content.selectedIndex) {
            return true;
        } else {
            alert("Please select content, country and city!");
            return false;
        }
    }
</script>
</body>
</html>