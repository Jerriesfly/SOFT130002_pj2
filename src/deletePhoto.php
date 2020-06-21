<?php
require_once('config.php');

if ($ImageID = $_POST['ImageID']) {
    $sql_get_path = 'SELECT Path FROM travelimage WHERE ImageID =' . $ImageID;
    $result_get_path = $mysqli->query($sql_get_path);
    if ($row = $result_get_path->fetch_assoc()) {
        $path = $row['Path'];
        unlink('../img/travel-images/large/' . $path);
        unlink('../img/travel-images/medium/' . $path);
        unlink('../img/travel-images/small/' . $path);
        unlink('../img/travel-images/thumb/' . $path);
        $sql_delete = "DELETE FROM travelimagefavor WHERE ImageID='$ImageID'";
        $sql_drop = " ALTER  TABLE travelimagefavor DROP FavorID";
        $sql_create = "ALTER TABLE travelimagefavor ADD FavorID INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT FIRST";
        $sql_remove = "DELETE FROM travelimage WHERE ImageID='$ImageID'";
        $mysqli->query($sql_delete);
        $mysqli->query($sql_drop);
        $mysqli->query($sql_create);
        $mysqli->query($sql_remove);
    }
}