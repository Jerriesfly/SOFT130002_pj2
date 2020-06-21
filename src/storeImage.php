<?php

class imgcompress
{
    private $src;
    private $image;
    private $imageinfo;
    private $percent = 0.5;

    public function __construct($src, $percent = 1)
    {
        $this->src = $src;
        $this->percent = $percent;
    }

    public function compressImg($saveName = '')
    {
        $this->_openImage();
        if (!empty($saveName)) $this->_saveImage($saveName);  //保存
        else $this->_showImage();
    }

    private function _openImage()
    {
        list($width, $height, $type, $attr) = getimagesize($this->src);
        $this->imageinfo = array(
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_extension($type, false),
            'attr' => $attr
        );
        $fun = "imagecreatefrom" . $this->imageinfo['type'];
        $this->image = $fun($this->src);
        $this->_thumpImage();
    }

    private function _thumpImage()
    {
        $new_width = $this->imageinfo['width'] * $this->percent;
        $new_height = $this->imageinfo['height'] * $this->percent;
        $image_thump = imagecreatetruecolor($new_width, $new_height);
        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->imageinfo['width'], $this->imageinfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }

    private function _showImage()
    {
        header('Content-Type: image/' . $this->imageinfo['type']);
        $funcs = "image" . $this->imageinfo['type'];
        $funcs($this->image);
    }

    private function _saveImage($dstImgName)
    {
        if (empty($dstImgName)) return false;
        $allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp', '.gif'];   //如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $dstExt = strrchr($dstImgName, ".");
        $sourseExt = strrchr($this->src, ".");
        if (!empty($dstExt)) $dstExt = strtolower($dstExt);
        if (!empty($sourseExt)) $sourseExt = strtolower($sourseExt);

        //有指定目标名扩展名
        if (!empty($dstExt) && in_array($dstExt, $allowImgs)) {
            $dstName = $dstImgName;
        } elseif (!empty($sourseExt) && in_array($sourseExt, $allowImgs)) {
            $dstName = $dstImgName . $sourseExt;
        } else {
            $dstName = $dstImgName . $this->imageinfo['type'];
        }
        $funcs = "image" . $this->imageinfo['type'];
        $funcs($this->image, $dstName);
    }

    public function __destruct()
    {
        imagedestroy($this->image);
    }
}

session_start();
require_once("config.php");

$destination_large = '../img/travel-images/large/';
$destination_medium = '../img/travel-images/medium/';
$destination_small = '../img/travel-images/small/';
$destination_thumb = '../img/travel-images/thumb/';

if (isset($_SESSION['UID'])) {
    $UID = $_SESSION['UID'];
    $ImageID = isset($_GET['ImageID']) ? $_GET['ImageID'] : '';
    $content = htmlspecialchars($_POST['content']);
    $country = htmlspecialchars($_POST['country']);
    $city = htmlspecialchars($_POST['city']);
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);

    $sql_information = "SELECT GeoNameID, Country_RegionCodeISO FROM geocities WHERE geocities.AsciiName='$city'";
    $result_information = $mysqli->query($sql_information);
    if ($row = $result_information->fetch_assoc()) {
        $country = $row['Country_RegionCodeISO'];
        $city = $row['GeoNameID'];
    }

    $file = $_FILES["file0"];
    $file_name = $file["name"];
    $temp = explode(".", $file["name"]);
    $extension = end($temp);
    $salt = base64_encode(random_bytes(8));
    $new_file_name = sha1($file_name . $salt) . '.' . $extension;

    compressAndMove($file, $destination_large . $new_file_name, 1);
    compressAndMove($file, $destination_medium . $new_file_name, 0.625);
    compressAndMove($file, $destination_small . $new_file_name, 5 / 16);
    compressAndMove($file, $destination_thumb . $new_file_name, 25 / 256);

    if ($ImageID) {
        $sql_get_path = 'SELECT Path FROM travelimage WHERE ImageID =' . $ImageID;
        $result_get_path = $mysqli->query($sql_get_path);

        if ($row = $result_get_path->fetch_assoc()) {
            $path = $row['Path'];
            unlink($destination_large . $path);
            unlink($destination_medium . $path);
            unlink($destination_small . $path);
            unlink($destination_thumb . $path);
            $sql_modify1 = "UPDATE travelimage SET Title='$title' WHERE ImageID = '$ImageID'";
            $sql_modify2 = "UPDATE travelimage SET Description='$description' WHERE ImageID = '$ImageID'";
            $sql_modify3 = "UPDATE travelimage SET CityCode='$city' WHERE ImageID = '$ImageID'";
            $sql_modify4 = "UPDATE travelimage SET Country_RegionCodeISO='$country' WHERE ImageID = '$ImageID'";
            $sql_modify5 = "UPDATE travelimage SET Path='$new_file_name' WHERE ImageID = '$ImageID'";
            $sql_modify6 = "UPDATE travelimage SET Content='$content' WHERE ImageID = '$ImageID'";
            $mysqli->query($sql_modify1);
            $mysqli->query($sql_modify2);
            $mysqli->query($sql_modify3);
            $mysqli->query($sql_modify4);
            $mysqli->query($sql_modify5);
            $mysqli->query($sql_modify6);
        }
    } else {
        $sql_create = "INSERT INTO travelimage(Title, Description, CityCode, Country_RegionCodeISO, UID, Path, Content)
values('$title', '$description', '$city', '$country', '$UID', '$new_file_name', '$content')";
        echo $sql_create;
        $mysqli->query($sql_create);
    }
    echo "<script>alert('successfully upload!');</script>";
    header('refresh:0;url=./myPhotos.php');
} else {
    echo "<script>alert('Please login first!');</script>";
    header('refresh:0;url=./login.php');
}

function compressAndMove($file, $destination, $ratio)
{
    $size = $file['size'] / 1048576.0; //MB数
    if ($size < 1) {
        (new imgcompress($file['tmp_name'], $ratio))->compressImg($destination);
    } else {
        (new imgcompress($file['tmp_name'], ($ratio / sqrt($size))))->compressImg($destination);
    }
}


