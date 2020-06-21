<?php
$page_size = 5;
$total_page = 0;
$mark = 0;
$first_page = 1;
$last_page = $total_page;
$pre_page = 0;
$next_page = 0;

function setPage($size, $total_count, $current)
{
    global $page_size, $total_page, $mark, $first_page, $last_page, $pre_page, $next_page;
    $page_size = $size;
    $total_page = (int)(($total_count % $page_size == 0) ? ($total_count / $page_size) : ($total_count / $page_size + 1));
    $mark = ($current - 1) * $page_size;
    $first_page = 1;
    $last_page = $total_page;
    $pre_page = ($current > 1) ? $current - 1 : 1;
    $next_page = ($total_page - $current > 0) ? $current + 1 : $total_page;
}


