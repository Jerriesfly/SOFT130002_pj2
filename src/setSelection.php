<script>
    <?php
    if(!isset($content)) $content = '';
    if(!isset($country)) $country = '';
    if(!isset($city)) $city = '';

    $sql_country = 'select Country_RegionName FROM geocountries_regions';
    $result_country = $mysqli->query($sql_country);
    $array_country = array('Select country');
    while ($row = $result_country->fetch_assoc()) {
        $array_country[] = $row['Country_RegionName'];
    }
    $json_country = json_encode($array_country);
    ?>

    const arr_content = ["Select content", "scenery", "city", "people", "animal", "building", "wonder"];
    let arr_country = <?php echo $json_country; ?>;
    let arr_city = ["Select city"];

    const content = document.getElementById("select1");
    const country = document.getElementById("select2");
    const city = document.getElementById("select3");

    // 界面加载时设置内容筛选和国家筛选框中的内容
    window.onload = function () {
        content.length = arr_content.length;
        country.length = arr_country.length;
        city.length = arr_city.length;

        for (let i = 0; i < arr_content.length; i++) {
            content.options[i].text = arr_content[i];
            content.options[i].value = arr_content[i];
            if (arr_content[i] === '<?php echo $content; ?>') {
                content.options[i].selected = true;
            }
        }

        for (let i = 0; i < arr_country.length; i++) {
            country.options[i].text = arr_country[i];
            country.options[i].value = arr_country[i];
            if (arr_country[i] === '<?php echo $country; ?>') {
                country.options[i].selected = true;
                setCity(i);
            }
        }

        city.options[0].text = arr_city[0];
        content.options[0].value = '';
        country.options[0].value = '';
        city.options[0].value = '';
    };

    // 根据国家筛选框中选中的选项设置城市筛选框中的选项
    function setCity(index) {
        let selectedCountry = arr_country[index];

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
                arr_city = JSON.parse(xmlhttp.responseText);
                city.length = arr_city.length;
                for (let i = 0; i < arr_city.length; i++) {
                    city.options[i].text = arr_city[i];
                    city.options[i].value = arr_city[i];
                    if (arr_city[i] === '<?php echo $city; ?>') {
                        city.options[i].selected = true;
                    }
                }
                city.options[0].value = '';
            }
        };
        xmlhttp.open("POST", "getCity.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send('country=' + selectedCountry);
    }
</script>