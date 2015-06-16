<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(G5_IS_MOBILE)
    include_once(G5_MCONTENTS_PATH.'/contents.head.php');
else
    include_once(G5_CONTENTS_PATH.'/contents.head.php');
?>