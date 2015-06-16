<?php
include_once('./common.php');

if((isset($setting['de_root_index_use']) && $setting['de_root_index_use']) || (isset($setting['de_contents_layout_use']) && $setting['de_contents_layout_use'])) {
    if (!defined('G5_USE_CONTENTS') || !G5_USE_CONTENTS)
        die('<p>컨텐츠몰 설치 후 이용해 주십시오.</p>');

    define('_CONTENTS_', true);
}
?>