<?php
include_once('./_common.php');

if($type == 'mobile') {
    if(preg_match('#^theme/(.+)$#', $dir, $match))
        $skin_dir = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1];
    else
        $skin_dir = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/contents/'.$dir;
} else {
    if(preg_match('#^theme/(.+)$#', $dir, $match))
        $skin_dir = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1];
    else
        $skin_dir = G5_PATH.'/'.G5_SKIN_DIR.'/contents/'.$dir;
}

echo cm_get_list_skin_options("^list.[0-9]+\.skin\.php", $skin_dir, $sval);
?>