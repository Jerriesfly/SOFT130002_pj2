<nav id="navigator">
    <div class="left icon"><a href="./home.php"><img src="../img/icon-inverse.png" height="50" width="50" alt="icon"></a>
    </div>
    <div id="home" class="left"><a href="./home.php">Home</a></div>
    <div id="browse" class="left"><a href="./browse.php">Browse</a></div>
    <div id="search" class="left"><a href="./search.php">Search</a></div>
    <div class="right drop-down" id="drop-down">
        <?php
        if (!isset($_SESSION['UID'])) {
            ?>
            <a id="drop-down-title" href="login.php"> Login</a>
            <?php
        } else {
            ?>
            <a id="drop-down-title"> My profile <span class="caret"></span></a>
            <ul class="drop-down-menu" id="drop-down-menu">
                <li class="drop-down-menuItem"><a href="./upload.php"><img src="../img/navbar/upload.png" alt="upload"
                                                                         height="15" width="15">upload</a></li>
                <li class="drop-down-menuItem"><a href="./myPhotos.php"><img src="../img/navbar/myPhotos.png" alt="myPhotos"
                                                                           height="15" width="15">my photos</a></li>
                <li class="drop-down-menuItem"><a href="./myFavourites.php"><img src="../img/navbar/myFavourites.png"
                                                                               alt="myFavourites" height="15"
                                                                               width="15">my favourites</a></li>
                <li class="drop-down-menuItem"><a href="./logout.php"><img src="../img/navbar/log out.png" alt="login"
                                                                         height="15" width="15">log out</a></li>
            </ul>
            <?php
        }
        ?>
    </div>
</nav>