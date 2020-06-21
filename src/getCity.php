<?php
require_once('config.php');
$selectedCountry = $_POST['country'];
$sql_city = "SELECT AsciiName FROM geocities
INNER JOIN geocountries_regions ON geocountries_regions.ISO = Country_RegionCodeISO
WHERE geocountries_regions.Country_RegionName='$selectedCountry' ORDER BY AsciiName";
$result_city = $mysqli->query($sql_city);
$array_city = array('Select city');
while ($row = $result_city->fetch_assoc()) {
    $array_city[] = $row['AsciiName'];
}
$json_city = json_encode($array_city);

echo $json_city;

