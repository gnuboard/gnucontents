<?php
define('G5_IS_ADMIN', true);
include_once ('../../common.php');

if (!defined('G5_USE_CONTENTS') || !G5_USE_CONTENTS)
    die('<p>컨텐츠몰 설치 후 이용해 주십시오.</p>');

include_once(G5_ADMIN_PATH.'/admin.lib.php');
?>
