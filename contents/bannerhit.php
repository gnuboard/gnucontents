<?php
include_once('./_common.php');

if ($_COOKIE['ck_cm_bn_id'] != $bn_id)
{
    $sql = " update {$g5['g5_contents_banner_table']} set bn_hit = bn_hit + 1 where bn_id = '$bn_id' ";
    sql_query($sql);
    // 하루 동안
    set_cookie("ck_cm_bn_id", $bn_id, 60*60*24);
}

goto_url($url);
?>
