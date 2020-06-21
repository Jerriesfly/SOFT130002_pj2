<?php
session_start();
session_destroy();

if (!isset($_COOKIE['previous_page']))  {
    header('refresh:0;url=./home.php');
} else {
    header('refresh:0;url=' . $_COOKIE['previous_page']);
}