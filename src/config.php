<?php
$mysqli = new mysqli("localhost", "Jerry", "Zalone1314520", "project2");

if($mysqli -> connect_errno){
    echo "Failed to connect to MYSQL: " . $mysqli -> connect_error;
}

function justSearch($mysqli)
{
    $sql = "SELECT ImageID, Title, Path FROM travelimage";
    return $mysqli->query($sql);
}

function searchByTitle($mysqli, $title)
{
    $sql = "SELECT ImageID, Title, Path FROM travelimage WHERE Title LIKE '%$title%'";
    return $mysqli->query($sql);
}

function searchByDescription($mysqli, $description)
{
    $sql = "SELECT ImageID, Title, Path FROM travelimage WHERE Title LIKE '%$description%'";
    return $mysqli->query($sql);
}

function noResult($message, $height)
{
    echo '<div style="height:' . $height . 'px; line-height:' .  $height . 'px; text-align: center"><p style="margin: 0">' . $message . '</p></div>';
}