<?php
$sub_menu = "100280";
define('_THEME_PREVIEW_', true);
include_once('./_common.php');

$theme_dir = get_theme_dir();

if(!$theme || !in_array($theme, $theme_dir))
    alert_close('테마가 존재하지 않거나 올바르지 않습니다.');

$info = get_theme_info($theme);

$arr_mode = array('index', 'list', 'view', 'contents', 'ca_list', 'item');
$mode = substr(strip_tags($_GET['mode']), 0, 20);
if(!in_array($mode, $arr_mode))
    $mode = 'index';

if(G5_COMMUNITY_USE === false || $mode == 'contents' || $mode == 'ca_list' || $mode == 'item')
    define('_CONTENTS_', true);

$qstr_index  = '&amp;mode=index';
$qstr_list   = '&amp;mode=list';
$qstr_view   = '&amp;mode=view';
$qstr_contents = '&amp;mode=contents';
$qstr_ca_list = '&amp;mode=ca_list';
$qstr_item    = '&amp;mode=item';
$qstr_device = '&amp;mode='.$mode.'&amp;device='.(G5_IS_MOBILE ? 'pc' : 'mobile');

$sql = " select bo_table, wr_parent from {$g5['board_new_table']} order by bn_id desc limit 1 ";
$row = sql_fetch($sql);
$bo_table = $row['bo_table'];
$board = sql_fetch(" select * from {$g5['board_table']} where bo_table = '$bo_table' ");
$write_table = $g5['write_prefix'] . $bo_table;

// theme.config.php 미리보기 게시판 스킨이 설정돼 있다면
$tconfig = get_theme_config_value($theme);
if($mode == 'list' || $mode == 'view') {
    if($tconfig['preview_board_skin'])
        $board['bo_skin'] = preg_match('#^theme/.+$#', $tconfig['preview_board_skin']) ? $tconfig['preview_board_skin'] : 'theme/'.$tconfig['preview_board_skin'];

    if($tconfig['preview_mobile_board_skin'])
        $board['bo_mobile_skin'] = preg_match('#^theme/.+$#', $tconfig['preview_mobile_board_skin']) ? $tconfig['preview_mobile_board_skin'] : 'theme/'.$tconfig['preview_mobile_board_skin'];
}

// 스킨경로
if (G5_IS_MOBILE) {
    $board_skin_path    = get_skin_path('board', $board['bo_mobile_skin']);
    $board_skin_url     = get_skin_url('board', $board['bo_mobile_skin']);
    $member_skin_path   = get_skin_path('member', $config['cf_mobile_member_skin']);
    $member_skin_url    = get_skin_url('member', $config['cf_mobile_member_skin']);
    $new_skin_path      = get_skin_path('new', $config['cf_mobile_new_skin']);
    $new_skin_url       = get_skin_url('new', $config['cf_mobile_new_skin']);
    $search_skin_path   = get_skin_path('search', $config['cf_mobile_search_skin']);
    $search_skin_url    = get_skin_url('search', $config['cf_mobile_search_skin']);
    $connect_skin_path  = get_skin_path('connect', $config['cf_mobile_connect_skin']);
    $connect_skin_url   = get_skin_url('connect', $config['cf_mobile_connect_skin']);
    $faq_skin_path      = get_skin_path('faq', $config['cf_mobile_faq_skin']);
    $faq_skin_url       = get_skin_url('faq', $config['cf_mobile_faq_skin']);
} else {
    $board_skin_path    = get_skin_path('board', $board['bo_skin']);
    $board_skin_url     = get_skin_url('board', $board['bo_skin']);
    $member_skin_path   = get_skin_path('member', $config['cf_member_skin']);
    $member_skin_url    = get_skin_url('member', $config['cf_member_skin']);
    $new_skin_path      = get_skin_path('new', $config['cf_new_skin']);
    $new_skin_url       = get_skin_url('new', $config['cf_new_skin']);
    $search_skin_path   = get_skin_path('search', $config['cf_search_skin']);
    $search_skin_url    = get_skin_url('search', $config['cf_search_skin']);
    $connect_skin_path  = get_skin_path('connect', $config['cf_connect_skin']);
    $connect_skin_url   = get_skin_url('connect', $config['cf_connect_skin']);
    $faq_skin_path      = get_skin_path('faq', $config['cf_faq_skin']);
    $faq_skin_url       = get_skin_url('faq', $config['cf_faq_skin']);
}

// 쇼핑몰 스킨 재설정
if($tconfig['de_contents_skin'])
    $setting['de_contents_skin'] = preg_match('#^theme/.+$#', $tconfig['de_contents_skin']) ? $tconfig['de_contents_skin'] : 'theme/'.$tconfig['de_contents_skin'];

if($tconfig['de_contents_mobile_skin'])
    $setting['de_contents_mobile_skin'] = preg_match('#^theme/.+$#', $tconfig['de_contents_mobile_skin']) ? $tconfig['de_contents_mobile_skin'] : 'theme/'.$tconfig['de_contents_mobile_skin'];

// 쇼핑몰초기화면 변수 재설정
for($i=1; $i<=4; $i++) {
    $setting['de_type'.$i.'_list_use']          = (isset($tconfig['de_type'.$i.'_list_use']) && $tconfig['de_type'.$i.'_list_use']) ? $tconfig['de_type'.$i.'_list_use'] : $setting['de_type'.$i.'_list_use'];
    $setting['de_type'.$i.'_list_skin']         = (isset($tconfig['de_type'.$i.'_list_skin']) && $tconfig['de_type'.$i.'_list_skin']) ? $tconfig['de_type'.$i.'_list_skin'] : $setting['de_type'.$i.'_list_skin'];
    $setting['de_type'.$i.'_list_mod']          = (isset($tconfig['de_type'.$i.'_list_mod']) && $tconfig['de_type'.$i.'_list_mod']) ? $tconfig['de_type'.$i.'_list_mod'] : $setting['de_type'.$i.'_list_mod'];
    $setting['de_type'.$i.'_list_row']          = (isset($tconfig['de_type'.$i.'_list_row']) && $tconfig['de_type'.$i.'_list_row']) ? $tconfig['de_type'.$i.'_list_row'] : $setting['de_type'.$i.'_list_row'];
    $setting['de_type'.$i.'_img_width']         = (isset($tconfig['de_type'.$i.'_img_width']) && $tconfig['de_type'.$i.'_img_width']) ? $tconfig['de_type'.$i.'_img_width'] : $setting['de_type'.$i.'_img_width'];
    $setting['de_type'.$i.'_img_height']        = (isset($tconfig['de_type'.$i.'_img_height']) && $tconfig['de_type'.$i.'_img_height']) ? $tconfig['de_type'.$i.'_img_height'] : $setting['de_type'.$i.'_img_height'];

    $setting['de_mobile_type'.$i.'_list_use']   = (isset($tconfig['de_mobile_type'.$i.'_list_use']) && $tconfig['de_mobile_type'.$i.'_list_use']) ? $tconfig['de_mobile_type'.$i.'_list_use'] : $setting['de_mobile_type'.$i.'_list_use'];
    $setting['de_mobile_type'.$i.'_list_skin']  = (isset($tconfig['de_mobile_type'.$i.'_list_skin']) && $tconfig['de_mobile_type'.$i.'_list_skin']) ? $tconfig['de_mobile_type'.$i.'_list_skin'] : $setting['de_mobile_type'.$i.'_list_skin'];
    $setting['de_mobile_type'.$i.'_list_mod']   = (isset($tconfig['de_mobile_type'.$i.'_list_mod']) && $tconfig['de_mobile_type'.$i.'_list_mod']) ? $tconfig['de_mobile_type'.$i.'_list_mod'] : $setting['de_mobile_type'.$i.'_list_mod'];
    $setting['de_mobile_type'.$i.'_list_row']   = (isset($tconfig['de_mobile_type'.$i.'_list_row']) && $tconfig['de_mobile_type'.$i.'_list_row']) ? $tconfig['de_mobile_type'.$i.'_list_row'] : $setting['de_mobile_type'.$i.'_list_row'];
    $setting['de_mobile_type'.$i.'_img_width']  = (isset($tconfig['de_mobile_type'.$i.'_img_width']) && $tconfig['de_mobile_type'.$i.'_img_width']) ? $tconfig['de_mobile_type'.$i.'_img_width'] : $setting['de_mobile_type'.$i.'_img_width'];
    $setting['de_mobile_type'.$i.'_img_height'] = (isset($tconfig['de_mobile_type'.$i.'_img_height']) && $tconfig['de_mobile_type'.$i.'_img_height']) ? $tconfig['de_mobile_type'.$i.'_img_height'] : $setting['de_mobile_type'.$i.'_img_height'];
}

// 상품상세 이미지 사이즈 재설정
$setting['de_mimg_width']  = (isset($tconfig['de_mimg_width']) && $tconfig['de_mimg_width']) ? $tconfig['de_mimg_width'] : $setting['de_mimg_width'];
$setting['de_mimg_height'] = (isset($tconfig['de_mimg_height']) && $tconfig['de_mimg_height']) ? $tconfig['de_mimg_height'] : $setting['de_mimg_height'];

// 테마 경로 설정
if(defined('G5_THEME_PATH')) {
    define('G5_THEME_CONTENTS_PATH',   G5_THEME_PATH.'/'.G5_CONTENTS_DIR);
    define('G5_THEME_CONTENTS_URL',    G5_THEME_URL.'/'.G5_CONTENTS_DIR);
    define('G5_THEME_MCONTENTS_PATH',  G5_THEME_PATH.'/'.G5_MOBILE_DIR.'/'.G5_CONTENTS_DIR);
    define('G5_THEME_MCONTENTS_URL',   G5_THEME_URL.'/'.G5_MOBILE_DIR.'/'.G5_CONTENTS_DIR);
}

// 스킨 경로 설정
if(preg_match('#^theme/(.+)$#', $setting['de_contents_skin'], $match)) {
    define('G5_CONTENTS_SKIN_PATH',  G5_THEME_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
    define('G5_CONTENTS_SKIN_URL',   G5_THEME_URL .'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
} else {
    define('G5_CONTENTS_SKIN_PATH',  G5_PATH.'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_skin']);
    define('G5_CONTENTS_SKIN_URL',   G5_URL .'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_skin']);
}

if(preg_match('#^theme/(.+)$#', $setting['de_contents_mobile_skin'], $match)) {
    define('G5_MCONTENTS_SKIN_PATH', G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
    define('G5_MCONTENTS_SKIN_URL',  G5_THEME_URL .'/'.G5_MOBILE_DIR.'/'.G5_SKIN_DIR.'/contents/'.$match[1]);
} else {
    define('G5_MCONTENTS_SKIN_PATH', G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_mobile_skin']);
    define('G5_MCONTENTS_SKIN_URL',  G5_MOBILE_URL .'/'.G5_SKIN_DIR.'/contents/'.$setting['de_contents_mobile_skin']);
}

$conf = sql_fetch(" select cf_theme from {$g5['config_table']} ");
$name = get_text($info['theme_name']);
if($conf['cf_theme'] != $theme) {
    if($tconfig['set_default_skin'])
        $set_default_skin = 'true';
    else
        $set_default_skin = 'false';

    $btn_active = '<li><button type="button" class="theme_sl theme_active" data-theme="'.$theme.'" '.'data-name="'.$name.'" data-set_default_skin="'.$set_default_skin.'">테마적용</button></li>';
} else {
    $btn_active = '';
}

$g5['title'] = get_text($info['theme_name']).' 테마 미리보기';
require_once(G5_PATH.'/head.sub.php');
?>

<link rel="stylesheet" href="<?php echo G5_ADMIN_URL; ?>/css/theme.css">
<script src="<?php echo G5_ADMIN_URL; ?>/theme.js"></script>

<section id="preview_item">
    <ul>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_index; ?>">인덱스 화면</a></li>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_list; ?>">게시글 리스트</a></li>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_view; ?>">게시글 보기</a></li>
        <?php if(defined('G5_USE_CONTENTS') && G5_USE_CONTENTS) { ?>
        <?php if(G5_COMMUNITY_USE) { ?>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_contents; ?>">컨텐츠몰</a></li>
        <?php } ?>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_ca_list; ?>">상품리스트</a></li>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_item; ?>">상품상세</a></li>
        <?php } ?>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_device; ?>"><?php echo (G5_IS_MOBILE ? 'PC 버전' : '모바일 버전'); ?></a></li>
        <?php echo $btn_active; ?>
    </ul>
</section>

<section id="preview_content">
    <?php
    switch($mode) {
        case 'list':
            include(G5_BBS_PATH.'/board.php');
            break;
        case 'view':
            $wr_id = $row['wr_parent'];
            $write = sql_fetch(" select * from $write_table where wr_id = '$wr_id' ");
            include(G5_BBS_PATH.'/board.php');
            break;
        case 'contents':
            include(G5_CONTENTS_PATH.'/index.php');
            break;
        case 'ca_list':
            $sql = " select ca_id from {$g5['g5_contents_category_table']} where ca_use = '1' order by ca_id limit 1 ";
            $tmp = sql_fetch($sql);
            $ca_id = $tmp['ca_id'];
            include(G5_CONTENTS_PATH.'/list.php');
            break;
        case 'item':
            $sql = " select it_id from {$g5['g5_contents_item_table']} where it_use = '1' order by it_id desc limit 1 ";
            $tmp = sql_fetch($sql);
            $_GET['it_id'] = $tmp['it_id'];
            include(G5_CONTENTS_PATH.'/item.php');
            break;
        default:
            include(G5_PATH.'/index.php');
            break;
    }
    ?>
</section>

<?php
require_once(G5_PATH.'/tail.sub.php');
?>