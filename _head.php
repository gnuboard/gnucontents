<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(isset($setting['de_contents_layout_use']) && $setting['de_contents_layout_use'])
    include_once(G5_CONTENTS_PATH.'/_head.php');
else
    include_once(G5_PATH.'/head.php');
?>