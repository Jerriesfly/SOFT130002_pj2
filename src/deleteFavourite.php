<?php
require_once('config.php');

if($FavorID = $_POST['FavorID']){
    $sql_remove = "DELETE FROM travelimagefavor WHERE FavorID='$FavorID'";
    $sql_drop = " ALTER  TABLE travelimagefavor DROP FavorID";
    $sql_create = "ALTER TABLE travelimagefavor ADD FavorID INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT FIRST";
    $mysqli ->query($sql_remove);
    $mysqli ->query($sql_drop);
    $mysqli ->query($sql_create);
}



