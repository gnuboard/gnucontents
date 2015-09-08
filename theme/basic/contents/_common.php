<?php
include_once('../../../common.php');

if (isset($_REQUEST['sort']))  {
    $sort = trim($_REQUEST['sort']);
    $sort = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $sort);
} else {
    $sort = '';
}

if (isset($_REQUEST['sortodr']))  {
    $sortodr = preg_match("/^(asc|desc)$/i", $sortodr) ? $sortodr : '';
} else {
    $sortodr = '';
}

if (!defined('G5_USE_CONTENTS') || !G5_USE_CONTENTS)
    die('<p>컨텐츠몰 설치 후 이용해 주십시오.</p>');
define('_CONTENTS_', true);
?>