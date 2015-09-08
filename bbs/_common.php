<?php
include_once('../common.php');

// 커뮤니티 사용여부
if(G5_COMMUNITY_USE === false) {
    if (!defined('G5_USE_CONTENTS') || !G5_USE_CONTENTS)
        die('<p>컨텐츠몰 설치 후 이용해 주십시오.</p>');

    define('_CONTENTS_', true);
}
?>